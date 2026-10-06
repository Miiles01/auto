#!/bin/bash
# Déploie le thème PC Auto sur un WordPress Hostinger.
# Usage : deploy/setup.sh <utilisateur@ip> <dossier du site, ex. domains/exemple.com/public_html> [clé ssh]
set -e
H="$1"; WP="$2"; KEY="${3:-$HOME/.ssh/id_hostinger}"
[ -z "$H" ] || [ -z "$WP" ] && { echo "usage: $0 user@ip domains/site/public_html [clé]"; exit 1; }
cd "$(dirname "$0")/.."
S="ssh -i $KEY -p 65002 -o BatchMode=yes"
node deploy/export-vehicles.js
$S "$H" "mkdir -p pc-import/assets/img/autos"
rsync -az --delete -e "$S" theme/pcauto/ "$H:$WP/wp-content/themes/pcauto/"
rsync -az -e "$S" --exclude thumb assets/img/autos/ "$H:pc-import/assets/img/autos/"
rsync -az -e "$S" deploy/vehicles.json deploy/import.php deploy/pages.php "$H:pc-import/"
$S "$H" "cd $WP && wp language core install fr_CA --activate && wp plugin install elementor woocommerce --activate && wp language plugin install elementor fr_CA && wp language plugin install woocommerce fr_CA; \
  wp option update woocommerce_coming_soon yes && wp option update woocommerce_store_pages_only no; \
  for f in wp-content/themes/pcauto/*.php wp-content/themes/pcauto/inc/*.php wp-content/themes/pcauto/parts/*.php; do php -l \$f | grep -v 'No syntax'; done; \
  wp theme activate pcauto && wp eval-file ~/pc-import/pages.php && wp eval-file ~/pc-import/import.php && wp rewrite flush && wp litespeed-purge all || true"
