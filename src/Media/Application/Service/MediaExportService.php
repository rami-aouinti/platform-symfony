<?php

declare(strict_types=1);

namespace App\Media\Application\Service;

use App\Media\Domain\Entity\Media;

class MediaExportService
{
    /**
     * @param Media[] $mediaItems
     * @param string[] $columns
     */
    public function buildExcelContent(array $mediaItems, array $columns): string
    {
        $lines = [implode("\t", $columns)];

        foreach ($mediaItems as $media) {
            $row = [];

            foreach ($columns as $column) {
                $value = $this->extractValue($media, $column);
                $row[] = str_replace(["\t", "\n", "\r"], ' ', $value);
            }

            $lines[] = implode("\t", $row);
        }

        return implode("\n", $lines);
    }

    /**
     * @param Media[] $mediaItems
     * @param string[] $columns
     */
    public function buildPdfContent(array $mediaItems, array $columns, string $title): string
    {
        $lines = [$title, ''];
        $lines[] = implode(' | ', $columns);

        foreach ($mediaItems as $media) {
            $row = [];

            foreach ($columns as $column) {
                $row[] = $this->extractValue($media, $column);
            }

            $lines[] = implode(' | ', $row);
        }

        return $this->createSimplePdf($lines);
    }

    private function extractValue(Media $media, string $column): string
    {
        return match ($column) {
            'id' => $media->getId(),
            'name' => $media->getName(),
            'path' => $media->getPath(),
            'mimeType' => $media->getMimeType(),
            'size' => (string)$media->getSize(),
            'status' => $media->getStatus()->value,
            'ownerId' => $media->getOwner()?->getId() ?? '',
            'createdAt' => $media->getCreatedAt()?->format('Y-m-d H:i:s') ?? '',
            default => '',
        };
    }

    /**
     * @param string[] $lines
     */
    private function createSimplePdf(array $lines): string
    {
        $escapedLines = array_map(
            static fn (string $line): string => str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $line),
            $lines,
        );

        $y = 800;
        $stream = "BT\n/F1 10 Tf\n50 {$y} Td\n";

        foreach ($escapedLines as $index => $line) {
            if ($index > 0) {
                $stream .= "0 -14 Td\n";
            }

            $stream .= "({$line}) Tj\n";
        }

        $stream .= 'ET';

        $objects = [];
        $objects[] = "1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n";
        $objects[] = "2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n";
        $objects[] = "3 0 obj\n<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >>\nendobj\n";
        $objects[] = '4 0 obj' . "\n<< /Length " . strlen($stream) . " >>\nstream\n{$stream}\nendstream\nendobj\n";
        $objects[] = "5 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>\nendobj\n";

        $pdf = "%PDF-1.4\n";
        $offsets = [0];

        foreach ($objects as $object) {
            $offsets[] = strlen($pdf);
            $pdf .= $object;
        }

        $xrefOffset = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";

        for ($i = 1; $i <= count($objects); $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
        }

        $pdf .= "trailer\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\nstartxref\n{$xrefOffset}\n%%EOF";

        return $pdf;
    }
}
