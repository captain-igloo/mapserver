FROM php:7.2.19-fpm AS build

COPY . /app

WORKDIR /app

RUN apt update \
    && apt install -y --no-install-recommends cmake libjpeg-dev libfreetype6-dev libgdal-dev libxml2-dev swig \
    && rm -rf /var/lib/apt/lists/* \
    && make

FROM php:7.2.19-fpm

COPY .docker/test.php /test.php

RUN apt update \
    && apt install -y --no-install-recommends libpng-dev libjpeg62-turbo libfreetype6 libproj12 libgdal20 libsqlite3-mod-spatialite:amd64 \
    && rm -rf /var/lib/apt/lists/*

COPY --from=build /app/build/libmapserver.so.7.7-dev /usr/lib
RUN ln -s /usr/lib/libmapserver.so.7.7-dev /usr/lib/libmapserver.so.2
RUN ln -s /usr/lib/libmapserver.so.2 /usr/lib/libmapserver.so

COPY --from=build /app/build/mapscript/php/php_mapscript.so /usr/local/lib/php/extensions/no-debug-non-zts-20170718/
RUN /usr/local/bin/docker-php-ext-enable php_mapscript

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini" \
    && echo "sqlite3.extension_dir=/usr/lib/x86_64-linux-gnu" > $PHP_INI_DIR/conf.d/sqlite.ini
