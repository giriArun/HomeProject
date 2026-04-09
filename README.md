# HomeProject

PHP Home Project

## Docker setup

This project is configured to run with:

- PHP 8.4 with Apache
- MySQL 8.4
- phpMyAdmin 5.2 (Apache image)

## Files added

- `Dockerfile`
- `docker-compose.yml`
- `.env.example`

## How to run

1. Copy the example environment file:

   ```powershell
   Copy-Item .env.example .env
   ```

2. Update `.env` with your preferred MySQL credentials.

3. Build and start the containers:

   ```powershell
   docker compose up --build -d
   ```

4. View running containers:

   ```powershell
   docker compose ps
   ```

5. Stop the containers when needed:

   ```powershell
   docker compose down
   ```

6. Stop the containers and remove the database volume too:

   ```powershell
   docker compose down -v
   ```

## URLs

- App: http://localhost:8080
- phpMyAdmin: http://localhost:8081

## Database connection details for your PHP app

- Host: `db`
- Port: `3306`
- Database: value of `MYSQL_DATABASE`
- Username: value of `MYSQL_USER`
- Password: value of `MYSQL_PASSWORD`

## Notes

- Your project root is mounted into `/var/www/html`, so you can keep editing files locally.
- MySQL data is persisted in the named Docker volume `mysql_data`.
- Containers communicate over the internal Docker network `homeproject-network`.
