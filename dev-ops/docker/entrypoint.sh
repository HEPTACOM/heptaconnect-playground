#!/bin/bash
if [ ! -f ContainerCreated ]; then
    while ! mysqladmin ping -h $DATABASE_HOST --silent; do
        sleep 1
    done
#    shopt -s globstar
    runuser -u www-data -- make --directory=/var/www/html shopware-platform-db
    touch ContainerCreated
fi
apache2-foreground
