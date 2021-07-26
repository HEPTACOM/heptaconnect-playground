FROM heptacom/heptaconnect-development:php74-0.6.0

COPY --chown=www-data:www-data . /var/www/html
COPY dev-ops/docker/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY dev-ops/docker/entrypoint.sh /var/www/html/entrypoint.sh

RUN runuser -u www-data -- make --directory=/var/www/html shopware-platform-files

CMD ["/var/www/html/entrypoint.sh"]
