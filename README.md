# PSP Student Portal

Mini Project 2 for DFP40443 Full Stack Web Development.

## Local setup

1. Create a MySQL database by importing `database/psp_student_portal.sql`.
2. Update `config/database.php` with the database credentials.
3. Set the web server document root to the `public` folder.
4. Sign in with student number `10DIT24F1184` and password `password`. Change the password before deployment.

## Security included

- Authenticated upload route and CSRF token validation
- Server-side MIME and image-content validation
- JPG and PNG only, with a 2 MB limit
- Random 32-character filenames using `random_bytes()`
- Prepared SQL statements and password hashes
- Old profile images are removed only after a successful replacement

## GitHub commands

```bash
git init
git add .
git commit -m "Create PSP student portal with secure profile upload"
git branch -M main
git remote add origin YOUR_GITHUB_REPOSITORY_URL
git push -u origin main
```

## Hosting

Upload the project files to a PHP/MySQL host. Make the host point to `public/`, import the SQL file using phpMyAdmin, and update `config/database.php` with the live database credentials.
