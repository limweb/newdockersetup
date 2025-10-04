FROM php:8.3-fpm-bullseye

# ติดตั้ง dependencies ที่จำเป็นทั้งหมด
RUN apt-get update && apt-get install -y --no-install-recommends \
    libfreetype6-dev \
    libjpeg62-turbo-dev \   
    libpng-dev \
    libpq-dev \
    libicu-dev \
    libxml2-dev \
    libzip-dev \
    libsqlite3-dev \  
    curl \
    gnupg \
    ca-certificates \
    unixodbc-dev \
    && rm -rf /var/lib/apt/lists/*

# ติดตั้ง PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        gd \
        mysqli \
        pdo \
        pdo_mysql \
        pdo_pgsql \
        pdo_sqlite \
        opcache \
        intl \
        zip

# ========== Microsoft ODBC + sqlsrv ==========
RUN curl -fsSL https://packages.microsoft.com/keys/microsoft.asc | gpg --dearmor -o /usr/share/keyrings/microsoft-archive-keyring.gpg \
    && echo "deb [arch=amd64 signed-by=/usr/share/keyrings/microsoft-archive-keyring.gpg] https://packages.microsoft.com/debian/11/prod bullseye main" > /etc/apt/sources.list.d/mssql-release.list

RUN apt-get update && ACCEPT_EULA=Y apt-get install -y --no-install-recommends msodbcsql18 \
    && rm -rf /var/lib/apt/lists/*

# ติดตั้ง build tools ชั่วคราวสำหรับ pecl
RUN apt-get update && apt-get install -y --no-install-recommends \
        g++ make autoconf libc-dev pkg-config \
    && pecl install sqlsrv-5.12.0 pdo_sqlsrv-5.12.0 \
    && docker-php-ext-enable sqlsrv pdo_sqlsrv \
    && apt-get remove -y g++ make autoconf libc-dev pkg-config \
    && apt-get autoremove -y \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

WORKDIR /var/www/html