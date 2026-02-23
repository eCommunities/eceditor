# Codex Request: Keep ECMS Backend Compatible with CKEditor Custom Plugins

Use this exact request in the ECMS repo when ready.

---

I need you to make the ECMS repository explicitly compatible with our CKEditor custom plugins that are being migrated into another repo.

## Objectives
1. Preserve backend/API behavior required by these plugins:
- `ecmsInsertImage`
- `ecmsInsertDocument`
- `ecmsInsertMedia`
- `ecmsMentions` (renamed from older custom `mentions` plugin)
2. Do not move CKEditor plugin source code in this task; only keep/adjust ECMS-side components that plugins call.
3. Add compatibility docs and lightweight verification checks.

## Required compatibility contracts

### A) Media picker iframe contract
Current plugin behavior expects:
- Browser endpoint reachable for image/doc selection:
  - `.../plugins/ecms/browser.php?type=img`
  - `.../plugins/ecms/browser.php?type=doc`
- The picker page must call parent window callbacks:
  - `window.parent.setImageData(url, alt, title)`
  - `window.parent.setDocData(url, icon, title, info)`

Tasks:
1. Confirm endpoint(s) and keep backward-compatible routes.
2. If route changes are needed, add compatibility wrappers/redirects.
3. Ensure callback payload fields are always present and escaped safely.
4. Document the contract and sample payloads.

### B) Mentions lookup contract
Current plugin behavior expects:
- Endpoint: `GET /api/v1.0/ecugm/user/`
- Query params: `terms`, `auth_signature`
- Response shape:
  - `{ data: [{ userid, firstname, lastname, email, ... }] }`
- Frontend obtains auth token from textarea data attribute:
  - `data-mentions-auth`

Tasks:
1. Keep endpoint and parameter names backward compatible.
2. Validate response shape consistency, including empty results.
3. Add/confirm auth signature validation and clear HTTP error responses.
4. Document contract and examples (success, empty, unauthorized).

### C) Media URL resize contract
Current image dialog logic rewrites query string `sid=<w>x<h>`.

Tasks:
1. Ensure media delivery endpoint continues to accept `sid=WxH`.
2. Document accepted ranges/default behavior when `sid` missing/invalid.
3. Keep legacy behavior intact where possible.

## Deliverables
1. Code changes to preserve/restore compatibility where needed.
2. New doc: `docs/ckeditor-plugin-compat.md` including:
- routes
- request/response schemas
- callback signatures
- versioned notes for future changes
3. Add a simple validation script or test notes proving:
- picker route works and returns selection callbacks
- mentions endpoint returns expected shape
- media URL `sid` works

## Constraints
- Avoid breaking existing ECMS UI flows.
- Prefer additive changes (wrappers/adapters) over breaking rewrites.
- Keep security checks (authn/authz) in place or stronger.

## Output format
In your response, provide:
1. Findings
2. Exact files changed
3. Compatibility guarantees added
4. Any remaining risks

---
