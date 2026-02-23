# CKEditor Plugin Migration Plan (ECMS -> eceditor)

## Scope
Source plugin tree reviewed:
- `C:\Portable\Documents\eCommunities\ECMS\Development Workspace\ECMS\public\plugins\ckeditor\plugins`

Target repo:
- `plugins/` in this repository.

## Inventory Classification

### Custom-only (not present in this repo)
- `codemirror`
- `ecms`
- `ecmsInsertDocument`
- `ecmsInsertImage`
- `ecmsInsertMedia`
- `scayt`

### Same-name plugins that are customized in ECMS source
- `mentions` (ECMS version is custom business logic)
- `autogrow` (ECMS copy differs from repo copy)

## Migration Decisions

1. `mentions`:
- Do not overwrite core `plugins/mentions`.
- Import ECMS behavior as a new plugin name: `ecmsMentions`.
- Keep support for `<ecms_mention_user ...>` and current API flow.

2. `autogrow`:
- Keep repo/core `autogrow` unless ECMS behavior is proven necessary.
- If required, migrate only deltas as a patch plugin or config override.

3. `ecmsInsertDocument`, `ecmsInsertImage`, `ecmsInsertMedia`:
- Import as first-class custom plugins under `plugins/`.
- Keep command names and dialog behavior to avoid UI config churn.

4. `ecms` folder:
- Treat as integration layer, not CKEditor plugin.
- Split into:
  - editor config/profile module(s), and
  - backend picker endpoints/classes retained in ECMS repo.

5. `codemirror` and `scayt`:
- Import only if still required by active editor profiles.
- Pin version and retain their own licenses in plugin folders.

## Compatibility Contracts (must remain in ECMS repo)

### Media picker contract
Used by `ecmsInsertImage` / `ecmsInsertDocument` via iframe:
- Browser endpoint currently at `plugins/ecms/browser.php?type=img|doc`
- Parent callbacks expected:
  - `window.parent.setImageData(url, alt, title)`
  - `window.parent.setDocData(url, icon, title, info)`

### Mentions contract
Used by ECMS mentions implementation:
- User lookup endpoint: `GET /api/v1.0/ecugm/user/`
- Query args: `terms`, `auth_signature`
- Response shape expected:
  - `data: [{ userid, firstname, lastname, email, ... }]`
- Editor textarea data attr expected:
  - `data-mentions-auth`

### Image URL format contract
Used in ECMS image dialog logic:
- URL includes `sid=<width>x<height>`
- Resizing logic rewrites that `sid` query value.

## Phased Execution Plan

1. Baseline import
- Add `plugins/ecmsInsertDocument`, `plugins/ecmsInsertImage`, `plugins/ecmsInsertMedia`.
- Add `plugins/ecmsMentions` (renamed from ECMS `mentions`).
- Add profile config module(s) replacing legacy `ecms_config_*.js` usage.

2. Wiring
- Register custom plugins through `config.extraPlugins`.
- Preserve protected tags/source rules for:
  - `ecms_mention_user`
  - `ecms`, `ecms_function` families
  - `ecmsInsertMedia` wrapper markup

3. Backend compatibility validation
- Validate iframe picker opens and callback injection works.
- Validate mentions API, auth token, and mention insertion output.

4. Optional plugins
- Add `codemirror` and `scayt` only after explicit product decision.

5. Stabilization
- Add regression tests/manual scripts for insert-image, insert-doc, insert-media, mentions, source mode.

## Risks
- Tight coupling to ECMS PHP classes (`ECMS.inc.php`, category/media services).
- Global callback model (`setImageData`, `setDocData`) is brittle.
- Mentions plugin currently tightly coupled to DOM/JQuery patterns and custom tags.

## Exit Criteria
- All required custom insert/mention workflows function in this repo.
- No core plugin overrides required for ECMS behavior.
- ECMS backend contracts documented and verified.
