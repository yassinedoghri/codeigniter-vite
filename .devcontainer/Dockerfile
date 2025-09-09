# Extending https://github.com/devcontainers/images/blob/main/src/php
FROM "mcr.microsoft.com/devcontainers/php:8.2"

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# Add php intl extension + xdebug coverage mode
RUN install-php-extensions intl \
    && echo "xdebug.mode = debug,coverage" >> /usr/local/etc/php/conf.d/xdebug.ini
