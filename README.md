# Domain Monitor

A self-hosted domain uptime monitoring application. Add your domains, and the system checks them on a schedule, stores results, and sends notifications via Email or Telegram when something goes wrong — or on every check if you prefer.

**Features**

- Automatic domain availability checks (HTTP/HTTPS, configurable method and timeout)
- Check history with response codes, times, and response body preview
- Notification channels: Email, Telegram (opt-in per channel)
- Extensible channel system — adding a new channel requires only a single PHP class
- Real-time logs across all domains with pagination

---

## VPS Installation (Docker)

These instructions target a fresh Ubuntu 22.04/24.04 VPS.

### 1. Install Docker

```bash
curl -fsSL https://get.docker.com | sh
sudo usermod -aG docker $USER
newgrp docker
```

Verify:

```bash
docker --version
docker compose version
```

### 2. Install Certbot and obtain SSL certificates

Replace `yourdomain.com` with your actual domain. Make sure DNS A/AAAA records point to this server before running certbot.

```bash
sudo apt update
sudo apt install -y certbot

# Obtain a certificate (standalone mode — port 80 must be free)
sudo certbot certonly --standalone -d yourdomain.com -d www.yourdomain.com
```

Certificates are saved to `/etc/letsencrypt/live/yourdomain.com/`. The nginx container mounts this directory read-only, so certbot renewals on the host are picked up automatically.

Set up automatic renewal:

```bash
sudo systemctl enable --now certbot.timer
```

### 3. Clone the repository

```bash
git clone https://github.com/your-org/domain-monitor.git /var/www/
cd /var/www/
```

### 4. Configure environment

```bash
cp .env.example .env
```

Edit `.env` and set at minimum:

```dotenv
APP_URL=https://yourdomain.com

DB_DATABASE=monitor
DB_USERNAME=monitor
DB_PASSWORD=change_me_strong_password

MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your@email.com
MAIL_PASSWORD=your_mail_password
MAIL_FROM_ADDRESS=your@email.com
```

### 5. Configure nginx for your domain

Edit `_docker/nginx/conf.d/nginx.conf` and replace every occurrence of `yourdomain.com` with your actual domain:

```bash
sed -i 's/yourdomain.com/example.com/g' _docker/nginx/conf.d/nginx.conf
```

The file already contains the correct certificate path structure — just make sure the domain matches what you used with certbot.

### 6. Build and start containers

```bash
docker compose up -d --build
```

On first start the app container will automatically:
- Install Composer dependencies
- Build frontend assets
- Generate `APP_KEY` if missing
- Run database migrations

Check startup logs:

```bash
docker compose logs -f app
```

### 7. Create the first user

```bash
docker compose exec app php artisan tinker --execute '
\App\Models\User::create([
    "name"     => "Admin",
    "email"    => "admin@yourdomain.com",
    "password" => bcrypt("your_password"),
]);
'
```

Or use the registration page if it is enabled.

---

## Architecture

| Container | Role |
|-----------|------|
| `nginx` | Reverse proxy, SSL termination |
| `app` | PHP-FPM + Laravel Scheduler (cron) + Queue worker, all managed by Supervisor |
| `mysql` | MySQL 8.0, data persisted in `dbdata` Docker volume |

The scheduler and queue worker run inside the `app` container via Supervisor — no separate systemd services needed.

---

## Useful commands

```bash
# View all container logs
docker compose logs -f

# Run artisan commands
docker compose exec app php artisan <command>

# Restart the app container (e.g. after .env change)
docker compose restart app

# Stop everything
docker compose down

# Pull latest code and redeploy
git pull
docker compose up -d --build
```

## SSL certificate renewal

Certbot auto-renews certificates on the host. After renewal, reload nginx to pick up the new certs:

```bash
sudo certbot renew --deploy-hook "docker compose -f /var/www/docker-compose.yml exec nginx nginx -s reload"
```

Or add it to `/etc/letsencrypt/renewal-hooks/deploy/`:

```bash
sudo tee /etc/letsencrypt/renewal-hooks/deploy/reload-nginx.sh > /dev/null << 'EOF'
#!/bin/sh
docker compose -f /var/www/docker-compose.yml exec nginx nginx -s reload
EOF
sudo chmod +x /etc/letsencrypt/renewal-hooks/deploy/reload-nginx.sh
```
