# Authorization resolution (global and company context)

## Roles

### Global platform roles
Global roles stay managed at platform level (`ROLE_*`) and keep highest priority for authorization decisions.

### Company membership roles
`CompanyMembership` now supports contextual roles per company:

- `member`
- `crm_manager`
- `shop_admin`
- `teacher`
- `candidate`

These roles are evaluated only in the context of the current `company_id` (`X-Company-Id`).

## Permission matrix by module

A dedicated service `CompanyPermissionMatrix` defines permissions per module:

- blog: `blog.view`, `blog.manage`
- crm: `crm.view`, `crm.manage`
- shop: `shop.view`, `shop.manage`
- education: `education.view`, `education.manage`

## Resolution rule

Authorization checks are resolved with this strict precedence:

1. **Global role** (`ROLE_ROOT`, `ROLE_ADMIN`) → allow.
2. **Membership role** for current `company_id` → allow if permission is granted by matrix.
3. **Ownership fallback** (resource owner) → allow read-level permissions.

In short: **global role > membership role > ownership**.

## Symfony voter context

`IsUserHimselfVoter` now reads `company_id` from:

1. voter subject payload (`company_id` / `companyId`) when provided;
2. otherwise request header `X-Company-Id`.

This allows voter decisions to include company context consistently.
