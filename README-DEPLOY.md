# Auto-deploy: GitHub -> cPanel

Every push to `main` uploads the changed files to the live site (GitHub Actions, FTP over TLS).

## One-time setup
1. **cPanel > FTP Accounts**: create an FTP account (e.g. `deploy@indogrills.com`) whose directory is the site's folder
   (usually `public_html`). Note the FTP server name shown there (often `ftp.indogrills.com` or the server host name).
2. **GitHub repo > Settings > Secrets and variables > Actions > New repository secret**. Add:
   - `FTP_HOST`     e.g. `ftp.indogrills.com`
   - `FTP_USER`     the FTP account name (full, e.g. `deploy@indogrills.com`)
   - `FTP_PASSWORD` the FTP account password
   - `FTP_DIR`      `/` if the FTP account is already limited to the site folder, otherwise `/public_html/`  (must end with `/`)
3. Push to `main` (or Actions tab > Deploy to cPanel > Run workflow). The first run uploads everything and takes a while.

## What is NOT deployed
`wp-config.php` (live DB login + mail password stay on the server), `database/` SQL files, `.github/`, card and catalogue folders.
The database is never touched by a deploy: import SQL changes manually in phpMyAdmin.

## Notes
- The first deploy over an existing install may re-upload everything; later deploys send only changed files.
- Product images uploaded through the WordPress admin on the live site are NOT in git. Don't use auto-deploy to delete them:
  deploy only syncs files that were in the repo.
