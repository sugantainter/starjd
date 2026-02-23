# Fixing 413 (Content Too Large) for Image Uploads

If category or other image uploads fail with **413 Content Too Large** or **ERR_HTTP2_PROTOCOL_ERROR**, increase these limits:

## 1. PHP

Ensure both are at least **8M** (so 2 MB images + form data are accepted):

- **upload_max_filesize** – max size of one uploaded file
- **post_max_size** – max size of the whole request body (must be ≥ upload_max_filesize)

**Option A – Project (when PHP reads `.user.ini`):**  
`public/.user.ini` is already set to 8M. If your PHP uses it, restart PHP-FPM after changing.

**Option B – php.ini:**  
Find `php.ini` (e.g. `php --ini`) and set:

```ini
upload_max_filesize = 8M
post_max_size = 8M
```

Then restart PHP (or PHP-FPM / web server).

## 2. Nginx (Laravel Valet, Forge, etc.)

If you use **Nginx**, set `client_max_body_size` to at least **8M** in the server block for this site.

**Laravel Valet** – create or edit the Nginx config for your site (e.g. in `~/.config/valet/Nginx/` or via `valet link`), and add inside the `server { ... }` block:

```nginx
client_max_body_size 8M;
```

Then run:

```bash
valet restart
```

**Other Nginx setups** – add the same line in the relevant `server { }` block and reload Nginx.

## 3. Frontend

The Categories admin form checks file size before upload: images over **2 MB** are rejected with a clear message. The app only accepts images up to 2 MB; server limits above are set higher so the request is not rejected before Laravel.
