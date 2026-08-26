# Deploying Danosla to AWS (Docker on EC2 + RDS + ElastiCache)

Architecture: one EC2 instance runs `docker-compose.prod.yml` (nginx, PHP-FPM app,
Reverb, queue worker, scheduler). The database and Redis are managed AWS services
(RDS MySQL, ElastiCache Redis) instead of containers, so your data survives if the
instance is ever rebuilt.

Region used below: **eu-north-1 (Stockholm)**, matching your console.

## ✅ Already deployed (2026-08-26)

The app is **live at http://13.53.241.191** (plain HTTP, no domain yet). Steps 1–7
below were done for you. Resource summary:

| Resource | Value |
|---|---|
| Security groups | `danosla-ec2-sg` (`sg-09e3d5c5d27939360`), `danosla-rds-sg` (`sg-0e0b9223b905419eb`), `danosla-redis-sg` (`sg-01825b67958af97b1`) |
| RDS MySQL | `danosla-db`, endpoint `danosla-db.cv6cosowyif2.eu-north-1.rds.amazonaws.com:3306`, db `danosla_db`. App connects as scoped user `danosla_app` (password lives only in the server's `~/danosla/.env`, not committed anywhere). Master user `admin`'s password was rotated during setup and used once to create that scoped user — reset it again via RDS → danosla-db → Modify if you ever need master access. |
| ElastiCache Redis | `danosla-redis`, endpoint `danosla-redis.w8yhhi.ng.0001.eun1.cache.amazonaws.com:6379`, `cache.t4g.micro`, single node, no TLS/auth (network-isolated only) |
| EC2 instance | `danosla-app` (`i-04f4751b804f2641`), Ubuntu 26.04 LTS, `t3.small`, 25 GB gp3 — code deployed to `~/danosla`, all 5 containers running |
| Elastic IP | **13.53.241.191** (associated with danosla-app) — **app is live here** |
| SSH key | `danosla-app-key.pem` — downloaded to your computer during creation. **Move it somewhere permanent and `chmod 400` it** — it's the only copy. |

Deployed from branch `feature/setup-frontend-build` — code was copied to the server
directly via `tar`/`scp` (not `git clone`) since that branch wasn't pushed to GitHub.
Once you push it (or merge to `main`), future redeploys can `git pull` instead of
re-uploading — see step 10.

**Known gotcha:** never run `php artisan route:cache` (or `artisan optimize`, which
calls it) on this app — it breaks mcamara/laravel-localization's dynamically
registered locale routes and every `/{locale}/...` page (login, dashboard, etc.)
404s. `config:cache` and `view:cache` are safe and already applied.

Skipped for now (do later, once you have a domain): Route53 A record, Let's Encrypt
cert, switching nginx from `default.http.conf` to `default.prod.conf` (step 8).

An unrelated pre-existing instance, **AYRADDE-VPS**, is also on this account — left untouched.

---

## 0. One-time account hygiene

1. Console → top-right account menu → **IAM** → create a non-root IAM user with
   `AdministratorAccess` (or a scoped policy later) and log in as that user instead
   of root for everything below.
2. Console → **Billing** → set a budget alert (you have AWS credits — a budget stops
   surprises). "Set up a cost budget using AWS Budgets" on your Console Home page
   does this in a couple of clicks.

## 1. Networking & security groups

Console → **EC2 → Security Groups → Create security group** (use the default VPC,
no need to create a new one for a single-instance deploy):

- `danosla-ec2-sg`
  - Inbound: `22/tcp` from **your IP only**, `80/tcp` from `0.0.0.0/0`, `443/tcp` from `0.0.0.0/0`
- `danosla-rds-sg`
  - Inbound: `3306/tcp` from source = `danosla-ec2-sg` (select the security group, not an IP)
- `danosla-redis-sg`
  - Inbound: `6379/tcp` from source = `danosla-ec2-sg`

This keeps the database and Redis unreachable from the public internet — only the
app server can talk to them.

## 2. RDS — MySQL

Console → **RDS → Create database**
- Engine: MySQL 8.0, Templates: **Free tier** (or "Dev/Test" once free tier is used up)
- DB instance identifier: `danosla-db`
- Master username: `admin`, auto-generate a strong password (save it)
- Instance class: `db.t3.micro` / `db.t4g.micro`
- Storage: 20 GiB gp3 is plenty to start
- Connectivity: default VPC, **public access = No**, VPC security group = `danosla-rds-sg`
- Initial database name: `danosla_db`
- Create database, wait for "Available", copy the **endpoint** hostname.

Later, connect once (from the EC2 box, see step 5) and create the app's own DB user
instead of using the master:
```sql
CREATE USER 'danosla_app'@'%' IDENTIFIED BY 'STRONG_PASSWORD';
GRANT ALL PRIVILEGES ON danosla_db.* TO 'danosla_app'@'%';
FLUSH PRIVILEGES;
```

## 3. ElastiCache — Redis

Console → **ElastiCache → Redis caches → Create Redis cache**
- Deployment: **Design your own cache** → Standalone (no replication needed to start)
- Name: `danosla-redis`
- Node type: `cache.t4g.micro`
- VPC: default VPC, security group = `danosla-redis-sg`, no public access
- Create, wait for "Available", copy the **primary endpoint** hostname.

## 4. EC2 — the app server

Console → **EC2 → Launch instance**
- Name: `danosla-app`
- AMI: **Ubuntu Server 24.04 LTS**
- Instance type: `t3.small` (t3.micro is too tight running 5 containers + build)
- Key pair: create/download one (you'll SSH with it)
- Network: default VPC, security group = `danosla-ec2-sg`
- Storage: 20–30 GiB gp3
- Launch, then **Elastic IPs → Allocate**, and associate it with this instance so
  the public IP doesn't change on reboot.

If you have a domain, point an A record at the Elastic IP now (Route53 or your
existing registrar) — you'll need it for HTTPS in step 8.

## 5. Bootstrap the EC2 box

SSH in (`ssh -i danosla-app-key.pem ubuntu@13.53.241.191`), then:

```bash
sudo apt-get update && sudo apt-get install -y docker.io docker-compose-plugin git
sudo usermod -aG docker $USER
newgrp docker

git clone <your-repo-url> danosla
cd danosla

cp .env.production.example .env
nano .env   # fill in RDS endpoint/user/password, ElastiCache endpoint,
            # domain, mail, S3 creds — see comments in the file
```

Generate the reverb credentials and app key while editing `.env`:
```bash
php -r "echo bin2hex(random_bytes(16)), PHP_EOL;"   # use for REVERB_APP_ID/KEY/SECRET
```

## 6. First build & migrate

```bash
docker compose -f docker-compose.prod.yml build

# App key (paste the output into .env's APP_KEY, then re-run build if needed)
docker compose -f docker-compose.prod.yml run --rm app php artisan key:generate --show

# Build frontend assets once (writes into public/build, served by nginx directly)
docker run --rm -v "$PWD":/var/www -w /var/www node:20-alpine sh -c "npm ci && npm run build"

# Bring the app up (nginx will fail SSL until step 8 — that's expected)
docker compose -f docker-compose.prod.yml up -d app reverb worker scheduler

docker compose -f docker-compose.prod.yml exec app php artisan migrate --force
docker compose -f docker-compose.prod.yml exec app php artisan storage:link
docker compose -f docker-compose.prod.yml exec app php artisan config:cache
docker compose -f docker-compose.prod.yml exec app php artisan view:cache
# Do NOT run `route:cache` — mcamara/laravel-localization registers locale-prefixed
# routes dynamically at boot; caching them serializes a broken set and every
# /{locale}/... route (login, dashboard, etc.) 404s. config:cache and view:cache are safe.
```

## 7. Start nginx over plain HTTP first (needed to issue the TLS cert)

Edit `docker/nginx/default.prod.conf`: replace `YOUR_DOMAIN` everywhere, and
temporarily delete/comment the whole `server { listen 443 ... }` block plus the
`return 301 https://...` line (nginx will fail to start referencing a cert that
doesn't exist yet).

```bash
docker compose -f docker-compose.prod.yml up -d web
```

Visit `http://YOUR_DOMAIN` — you should see the app over plain HTTP.

## 8. HTTPS with Let's Encrypt

```bash
sudo apt-get install -y certbot
sudo certbot certonly --webroot -w /home/ubuntu/danosla/public -d YOUR_DOMAIN
```

Now restore `docker/nginx/default.prod.conf` to its full version (the redirect +
the 443 server block, both with `YOUR_DOMAIN` filled in), then:

```bash
docker compose -f docker-compose.prod.yml up -d --force-recreate web
```

Set up renewal (certbot installs a systemd timer automatically on Ubuntu, but
nginx needs a reload after renewal):
```bash
echo '#!/bin/bash
docker compose -f /home/ubuntu/danosla/docker-compose.prod.yml exec web nginx -s reload' | sudo tee /etc/letsencrypt/renewal-hooks/deploy/reload-nginx.sh
sudo chmod +x /etc/letsencrypt/renewal-hooks/deploy/reload-nginx.sh
```

## 9. Verify

- `https://YOUR_DOMAIN` loads the app
- Log in, place/negotiate a bid — confirm the live update appears (Reverb through
  `wss://YOUR_DOMAIN/app`) without a page refresh
- `docker compose -f docker-compose.prod.yml logs -f worker` shows queued jobs
  (notifications, broadcasts) processing

## 10. Redeploying after future changes

**Day-to-day workflow:**
1. Edit code locally as normal (`composer run dev`, etc.), commit, and `git push`
   like you would for any change.
2. SSH into the server and pull + rebuild (full steps below).

Skip the `build`/asset-rebuild steps for changes that don't touch `composer.json`,
the Dockerfiles, or frontend source — e.g. a pure PHP logic change only needs
`git pull` + `up -d` (source is bind-mounted into the containers) plus a cache
clear if you touched config/views.

`~/danosla` on the server is now a real git repo tracking `origin/feature/setup-frontend-build`
(set up 2026-08-26 — it started as a `tar`/`scp` copy with no `.git`, so this was a
one-time bootstrap). One important detail: **the repo tracks a root `.env` file**
(pre-existing repo quirk, not something to rely on), which would normally get
clobbered by a `git pull`. That's prevented with:
```bash
git update-index --skip-worktree .env
```
already applied on the server — `git status`/`git pull` will silently leave the
server's real `.env` alone. Don't undo this (`--no-skip-worktree`) unless you
mean to let a pull overwrite it.

Normal redeploy, once your changes are committed and pushed from your machine:
```bash
ssh -i danosla-app-key.pem ubuntu@13.53.241.191
cd ~/danosla
git pull
docker compose -f docker-compose.prod.yml build
docker run --rm -v "$PWD":/var/www -w /var/www node:20-alpine sh -c "npm ci && npm run build"
docker compose -f docker-compose.prod.yml up -d
docker compose -f docker-compose.prod.yml exec app php artisan migrate --force
docker compose -f docker-compose.prod.yml exec app php artisan config:cache
docker compose -f docker-compose.prod.yml exec app php artisan view:cache
# `artisan optimize` runs route:cache internally — don't use it here, see the note above.
```

If a deploy changes `docker/php/Dockerfile`, `docker-compose.prod.yml`, or the
nginx configs, the `build`/`up -d` steps above already pick that up — no extra
step needed.

Consider turning this into a small `deploy.sh` script once the flow feels stable.

## Notes / things to revisit as this grows

- `restart: unless-stopped` on every service plus enabling the `docker` systemd
  service (`sudo systemctl enable docker`) means containers survive an instance
  reboot automatically — no extra process manager needed for a single box.
- Take RDS automated backups seriously: Console → RDS → your DB → Maintenance &
  backups → enable automated backups (on by default) and note the retention window.
- `.env.docker` (local dev) is currently committed to the repo with a real
  `APP_KEY` and DB credentials — harmless for local-only creds, but do **not**
  reuse any of those values in production, and never commit the real `.env`.
- If traffic outgrows one box, the next step is ECS Fargate + RDS/ElastiCache
  (same managed data layer, just swap the compute layer) rather than starting over.
