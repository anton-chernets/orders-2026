#!/usr/bin/env bash
set -euo pipefail

# ─── Config ───────────────────────────────────────────────────────────────────
REPO_URL="git@github.com:anton-chernets/orders-2026.git"
DEPLOY_PATH="/var/www/orders-2026"
# ──────────────────────────────────────────────────────────────────────────────

GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

step()  { echo -e "\n${GREEN}▶ $1${NC}"; }
warn()  { echo -e "${YELLOW}⚠  $1${NC}"; }
error() { echo -e "${RED}✗  $1${NC}"; exit 1; }

[ "$(id -u)" -eq 0 ] || error "Run as root"

# ─── 1. System ────────────────────────────────────────────────────────────────
step "Updating system packages..."
apt-get update -q
apt-get upgrade -y -q
apt-get install -y -q git curl

# ─── 2. Docker ────────────────────────────────────────────────────────────────
step "Installing Docker..."
if ! command -v docker &>/dev/null; then
    curl -fsSL https://get.docker.com | sh
else
    warn "Docker already installed: $(docker --version)"
fi

docker compose version || error "Docker Compose v2 not found"

# ─── 3. Deploy SSH key ────────────────────────────────────────────────────────
step "Generating deploy SSH key..."
mkdir -p /root/.ssh
chmod 700 /root/.ssh

if [ ! -f /root/.ssh/deploy_key ]; then
    ssh-keygen -t ed25519 -C "hetzner-deploy" -f /root/.ssh/deploy_key -N ""
else
    warn "Deploy key already exists, skipping."
fi

cat >> /root/.ssh/config << 'EOF'

Host github.com
  IdentityFile /root/.ssh/deploy_key
  StrictHostKeyChecking no
EOF

echo ""
echo -e "${YELLOW}══════════════════════════════════════════════════════${NC}"
echo -e "${YELLOW}  Add this public key to GitHub:${NC}"
echo -e "${YELLOW}  Repo → Settings → Deploy keys → Add deploy key${NC}"
echo -e "${YELLOW}  (Read access is enough — do NOT check write access)${NC}"
echo -e "${YELLOW}══════════════════════════════════════════════════════${NC}"
echo ""
cat /root/.ssh/deploy_key.pub
echo ""
read -rp "Press Enter after adding the key to GitHub..."

# ─── 4. Clone repo ────────────────────────────────────────────────────────────
step "Cloning repository..."
mkdir -p "$(dirname "$DEPLOY_PATH")"

if [ ! -d "$DEPLOY_PATH/.git" ]; then
    git clone "$REPO_URL" "$DEPLOY_PATH"
else
    warn "Repository already exists at $DEPLOY_PATH"
fi

cd "$DEPLOY_PATH"

# ─── 5. Permissions ───────────────────────────────────────────────────────────
step "Setting storage permissions..."
mkdir -p storage/logs storage/framework/{cache,sessions,views} bootstrap/cache
chmod -R 775 storage bootstrap/cache

# ─── 6. Environment ───────────────────────────────────────────────────────────
step "Setting up .env..."
if [ ! -f .env ]; then
    cp .env.example .env
    sed -i 's/APP_ENV=local/APP_ENV=production/' .env
    sed -i 's/APP_DEBUG=true/APP_DEBUG=false/' .env
    warn ".env created — open it and fill in DB_PASSWORD, APP_URL, etc."
    echo ""
    read -rp "Press Enter to open .env in nano (Ctrl+X to save and exit)..."
    nano .env
else
    warn ".env already exists, skipping."
fi

# ─── 7. First start ───────────────────────────────────────────────────────────
step "Starting containers..."
docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d

step "Generating APP_KEY..."
docker compose exec app php artisan key:generate --force

step "Running migrations and seeds..."
docker compose exec app php artisan migrate --seed --force

step "Building frontend assets..."
docker run --rm \
    -v "$(pwd):/app" \
    -w /app \
    node:20-alpine \
    sh -c "npm ci && npm run build"

step "Warming caches..."
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache
docker compose exec app php artisan event:cache

# ─── 8. Disable password auth ─────────────────────────────────────────────────
step "Disabling SSH password authentication..."
sed -i 's/^#\?PasswordAuthentication.*/PasswordAuthentication no/' /etc/ssh/sshd_config
systemctl reload sshd
warn "Password auth disabled — SSH key access only from now on."

# ─── Done ─────────────────────────────────────────────────────────────────────
echo ""
echo -e "${GREEN}══════════════════════════════════════════════════════${NC}"
echo -e "${GREEN}  Server setup complete!${NC}"
echo -e "${GREEN}══════════════════════════════════════════════════════${NC}"
echo ""
echo "  App:    http://$(curl -s ifconfig.me)"
echo "  Admin:  http://$(curl -s ifconfig.me)/admin"
echo ""
echo "  Create admin user:"
echo "    docker compose exec app php artisan make:filament-user"
echo ""
