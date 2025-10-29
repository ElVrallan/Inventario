#!/usr/bin/env bash
# Crear DB y ejecutar migraciones (bash)

read -p "DB user (default: root): " DB_USER
read -s -p "DB password (leave blank for none): " DB_PASS
echo
DB_USER=${DB_USER:-root}
DB_NAME="inventarioDB"

echo "Creando base de datos $DB_NAME..."
if [ -z "$DB_PASS" ]; then
  mysql -u"$DB_USER" -e "CREATE DATABASE IF NOT EXISTS \`$DB_NAME\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
else
  mysql -u"$DB_USER" -p"$DB_PASS" -e "CREATE DATABASE IF NOT EXISTS \`$DB_NAME\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
fi

echo "Ejecutando php artisan session:table (si aplica)..."
php artisan session:table

echo "Ejecutando migraciones..."
php artisan migrate --force

echo "Migraciones completadas."
