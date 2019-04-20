sudo rm /var/www/micropost && \
sudo ln -s /var/www/micropost_current /var/www/micropost && \
cd /var/www/micropost && \
sed -ri "s/^APP_ENV=.*$/APP_ENV=$APP_ENV/" .env && \
sed -ri "s/^DATABASE_URL=.*$/DATABASE_URL=$DATABASE_URL/" .env && \
sed -ri "s/^MAILER_URL=.*$/MAILER_URL=$MAILER_URL/" .env && \
sed -ri "s/^MAILER_FROM=.*$/MAILER_FROM=$MAILER_FROM/" .env && \
sudo php bin/console doctrine:migrations:migrate --no-interaction && \
sudo chown -R www-data:www-data /var/www/micropost_current && \
sudo chown -h www-data:www-data /var/www/micropost && \
sudo service apache2 restart
