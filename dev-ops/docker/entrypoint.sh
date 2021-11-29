#!/bin/bash
if [ ! -f ContainerCreated ]; then
    if [ ! -f ./shopware-platform/composer.lock ]; then
        runuser -u www-data -- make --directory=/var/www/html shopware-platform-files
    fi
    while ! mysqladmin ping -h $DATABASE_HOST --silent; do
        sleep 1
    done
    runuser -u www-data -- make --directory=/var/www/html shopware-platform-db
    touch ContainerCreated
fi
apache2-foreground
