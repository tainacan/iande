#!/bin/bash

if [ ! -f /installed ]; then
    wp --allow-root --path=/var/www/html core install --url=http://localhost/ \
		--admin_user=admin --admin_password=admin --admin_email=admin@localhost.local \
		--title='Iandé Development'

	wp --allow-root --path=/var/www/html plugin list

	wp --allow-root --path=/var/www/html plugin activate iande

	touch /installed
fi

exec "$@"
