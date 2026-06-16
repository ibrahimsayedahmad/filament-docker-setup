FROM php:8.3-fpm

# 1. Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    libzip-dev \
    libpq-dev \
    libicu-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libwebp-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. Configure & Install Extensions in one step to ensure clean linking
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-configure intl \
    && docker-php-ext-install \
    pdo_mysql \
    mbstring \
    zip \
    exif \
    pcntl \
    bcmath \
    gd \
    intl

# 3. Install Redis
RUN pecl install redis && docker-php-ext-enable redis

# 4. Install New Relic PHP Agent
#RUN curl -L https://download.newrelic.com/php_agent/release/newrelic-php5-12.7.0.36-linux.tar.gz | tar -C /tmp -zx \
#    && export NR_INSTALL_USE_CP_NOT_LN=1 \
#    && export NR_INSTALL_SILENT=1 \
#    && /tmp/newrelic-php5-12.7.0.36-linux/newrelic-install install \
#    && rm -rf /tmp/newrelic-php5-* /tmp/nrinstall* \
#    && rm /usr/local/etc/php/conf.d/newrelic.ini


# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY php-custom.ini $PHP_INI_DIR/conf.d/php-custom.ini

WORKDIR /var/www/html
