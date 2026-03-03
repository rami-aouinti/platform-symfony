#!/usr/bin/env python3
"""Generate a module dependency map SVG for src/* modules.

A dependency edge A -> B exists when a PHP class in module A imports App\\B\\...
via a `use` statement. Only edges with weight >= MIN_EDGE_WEIGHT are rendered to keep
readability.
"""

from __future__ import annotations

import math
import os
import re
from collections import Counter
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
SRC_DIR = ROOT / "src"
OUT_FILE = ROOT / "docs" / "images" / "module-connections.svg"
MIN_EDGE_WEIGHT = 5

USE_RE = re.compile(r"^use\s+App\\([A-Z][A-Za-z0-9_]*)\\", re.M)
NS_RE = re.compile(r"^namespace\s+App\\([A-Z][A-Za-z0-9_]*)\\", re.M)


def list_modules() -> list[str]:
    return sorted(
        [
            d.name
            for d in SRC_DIR.iterdir()
            if d.is_dir() and d.name and d.name[0].isupper()
        ]
    )


def collect_edges(modules: list[str]) -> Counter[tuple[str, str]]:
    module_set = set(modules)
    edges: Counter[tuple[str, str]] = Counter()

    for module in modules:
        module_dir = SRC_DIR / module
        for dirpath, _, filenames in os.walk(module_dir):
            for filename in filenames:
                if not filename.endswith(".php"):
                    continue

                file_path = Path(dirpath) / filename
                content = file_path.read_text(encoding="utf-8", errors="ignore")

                ns_match = NS_RE.search(content)
                if not ns_match:
                    continue
                source = ns_match.group(1)

                for target in USE_RE.findall(content):
                    if target in module_set and target != source:
                        edges[(source, target)] += 1

    return edges


def polar_positions(modules: list[str], cx: float, cy: float, radius: float) -> dict[str, tuple[float, float]]:
    total = len(modules)
    positions: dict[str, tuple[float, float]] = {}

    for i, module in enumerate(modules):
        angle = (-math.pi / 2) + (2 * math.pi * i / total)
        x = cx + radius * math.cos(angle)
        y = cy + radius * math.sin(angle)
        positions[module] = (x, y)

    return positions


def escape_xml(value: str) -> str:
    return (
        value.replace("&", "&amp;")
        .replace("<", "&lt;")
        .replace(">", "&gt;")
        .replace('"', "&quot;")
        .replace("'", "&apos;")
    )


def line_with_arrow(x1: float, y1: float, x2: float, y2: float, node_radius: float) -> tuple[float, float, float, float]:
    dx = x2 - x1
    dy = y2 - y1
    dist = math.hypot(dx, dy)
    if dist == 0:
        return x1, y1, x2, y2

    ux = dx / dist
    uy = dy / dist

    sx = x1 + ux * node_radius
    sy = y1 + uy * node_radius
    ex = x2 - ux * node_radius
    ey = y2 - uy * node_radius

    return sx, sy, ex, ey


def build_svg(modules: list[str], edges: Counter[tuple[str, str]]) -> str:
    width, height = 1800, 1400
    cx, cy = width / 2, height / 2
    radius = 520
    node_r = 42

    positions = polar_positions(modules, cx, cy, radius)

    rendered_edges = [
        (src, dst, weight)
        for (src, dst), weight in sorted(edges.items(), key=lambda item: (-item[1], item[0][0], item[0][1]))
        if weight >= MIN_EDGE_WEIGHT
    ]

    max_weight = max([w for _, _, w in rendered_edges], default=1)

    parts: list[str] = []
    parts.append('<?xml version="1.0" encoding="UTF-8"?>')
    parts.append(
        f'<svg xmlns="http://www.w3.org/2000/svg" width="{width}" height="{height}" viewBox="0 0 {width} {height}">'
    )
    parts.append("<defs>")
    parts.append(
        '<marker id="arrow" markerWidth="8" markerHeight="8" refX="7" refY="4" orient="auto" markerUnits="strokeWidth">'
        '<path d="M0,0 L8,4 L0,8 z" fill="#6b7280" /></marker>'
    )
    parts.append("</defs>")
    parts.append('<rect x="0" y="0" width="100%" height="100%" fill="#ffffff"/>')

    parts.append('<text x="900" y="52" text-anchor="middle" font-size="30" font-family="Arial" fill="#111827">')
    parts.append("Carte des modules Symfony et leurs connexions")
    parts.append("</text>")
    parts.append('<text x="900" y="84" text-anchor="middle" font-size="18" font-family="Arial" fill="#4b5563">')
    parts.append(f"Connexion A → B : un fichier de A importe App\\B\\* (seuil d&apos;affichage ≥ {MIN_EDGE_WEIGHT})")
    parts.append("</text>")

    for src, dst, weight in rendered_edges:
        x1, y1 = positions[src]
        x2, y2 = positions[dst]
        sx, sy, ex, ey = line_with_arrow(x1, y1, x2, y2, node_r)

        stroke = 1.0 + (4.0 * weight / max_weight)
        opacity = 0.28 + (0.42 * weight / max_weight)

        parts.append(
            f'<line x1="{sx:.2f}" y1="{sy:.2f}" x2="{ex:.2f}" y2="{ey:.2f}" '
            f'stroke="#6b7280" stroke-width="{stroke:.2f}" stroke-opacity="{opacity:.2f}" marker-end="url(#arrow)" />'
        )

    for module in modules:
        x, y = positions[module]
        parts.append(f'<circle cx="{x:.2f}" cy="{y:.2f}" r="{node_r}" fill="#eff6ff" stroke="#2563eb" stroke-width="2"/>')
        parts.append(
            f'<text x="{x:.2f}" y="{y + 5:.2f}" text-anchor="middle" font-size="14" font-family="Arial" fill="#1e3a8a">{escape_xml(module)}</text>'
        )

    legend_x, legend_y = 90, 1160
    parts.append(f'<rect x="{legend_x}" y="{legend_y}" width="700" height="160" rx="12" fill="#f9fafb" stroke="#d1d5db"/>')
    parts.append(
        f'<text x="{legend_x + 20}" y="{legend_y + 34}" font-size="18" font-family="Arial" fill="#111827">Légende</text>'
    )
    parts.append(
        f'<text x="{legend_x + 20}" y="{legend_y + 62}" font-size="14" font-family="Arial" fill="#374151">• Noeud : module (dossier de premier niveau sous src/)</text>'
    )
    parts.append(
        f'<text x="{legend_x + 20}" y="{legend_y + 86}" font-size="14" font-family="Arial" fill="#374151">• Flèche A → B : A dépend de B via imports PHP</text>'
    )
    parts.append(
        f'<text x="{legend_x + 20}" y="{legend_y + 110}" font-size="14" font-family="Arial" fill="#374151">• Épaisseur/opacité de ligne : nombre d&apos;imports (plus fort = plus visible)</text>'
    )
    parts.append(
        f'<text x="{legend_x + 20}" y="{legend_y + 134}" font-size="14" font-family="Arial" fill="#374151">• Liens affichés : poids ≥ {MIN_EDGE_WEIGHT}</text>'
    )

    parts.append("</svg>")
    return "\n".join(parts)


def main() -> None:
    modules = list_modules()
    edges = collect_edges(modules)
    svg = build_svg(modules, edges)

    OUT_FILE.parent.mkdir(parents=True, exist_ok=True)
    OUT_FILE.write_text(svg, encoding="utf-8")

    kept = sum(1 for _, w in edges.items() if w >= MIN_EDGE_WEIGHT)
    print(f"Generated: {OUT_FILE}")
    print(f"Modules: {len(modules)}")
    print(f"Edges total: {len(edges)}")
    print(f"Edges rendered (>= {MIN_EDGE_WEIGHT}): {kept}")


if __name__ == "__main__":
    main()
