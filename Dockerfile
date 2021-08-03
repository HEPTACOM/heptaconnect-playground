FROM heptacom/heptaconnect-development:php74-0.6.0

COPY --chown=www-data:www-data . /var/www/html
COPY --chown=www-data:www-data dev-ops/docker/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY --chown=www-data:www-data dev-ops/docker/entrypoint.sh /var/www/html/entrypoint.sh

RUN mkdir /var/www/.composer; \
    chown -R www-data:www-data /var/www/.composer

RUN runuser -u www-data -- make --directory=/var/www/html shopware-platform-files

CMD ["/var/www/html/entrypoint.sh"]
