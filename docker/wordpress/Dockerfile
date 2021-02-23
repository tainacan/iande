FROM wordpress

ENV TOOLS_DEPS \
        wget \
        curl \
        nano \
        git \
        unzip \
        libzip-dev \
        zip \
        default-mysql-client \
        ghostscript

RUN apt-get update && apt-get install -y $TOOLS_DEPS

RUN curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar && \
	chmod +x wp-cli.phar && \
	mv wp-cli.phar /usr/local/bin/wp

COPY docker/wordpress/entrypoint.sh /entrypoint.sh

RUN sed -i '$ d' /usr/local/bin/docker-entrypoint.sh

RUN cat /entrypoint.sh >> /usr/local/bin/docker-entrypoint.sh
