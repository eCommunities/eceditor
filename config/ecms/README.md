# ECMS CKEditor Profiles

These profile files were migrated from the ECMS integration package and adapted for this repository.

Files:
- `config/ecms/full.js`
- `config/ecms/mini.js`
- `config/ecms/public.js`

Backend contract options used by custom plugins:
- `config.ecms_browserUrl`: media picker endpoint for image/document dialogs.
- `config.ecms_mentionsEndpoint`: mentions user lookup endpoint.
- `config.ecms_mentionsAuthAttribute`: textarea `data-*` auth attribute suffix.
- `config.ecms_mentionsProfileUrl`: profile URL template with `{uid}` placeholder.
