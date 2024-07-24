# Orangescrum Docker Setup

This guide will help you set up and run Orangescrum using Docker. Follow the steps below to ensure everything is configured correctly.

## Prerequisites

- Docker
- Docker Compose

## Step-by-Step Guide

### Step 1: Create a Dockerfile

Create a `Dockerfile` in the root of your project directory with the following content:

```Dockerfile
FROM php:7.2-apache

# Install required PHP extensions
RUN apt-get update && apt-get install -y \
libpng-dev \
libjpeg-dev \
libfreetype6-dev \
libxml2-dev \
libicu-dev \
libcurl4-openssl-dev \
zlib1g-dev \
libssl-dev \
mariadb-client \
&& docker-php-ext-install -j$(nproc) iconv \
&& docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
&& docker-php-ext-install -j$(nproc) gd \
&& docker-php-ext-install pdo pdo_mysql mysqli \
&& docker-php-ext-install mbstring \
&& docker-php-ext-install intl \
&& docker-php-ext-install curl \
&& docker-php-ext-install soap \
&& docker-php-ext-install zip

# Enable Apache modules
RUN a2enmod rewrite headers

# Set working directory
WORKDIR /var/www/html

# Copy Orangescrum files
COPY . /var/www/html

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
&& chmod -R 775 /var/www/html/app/Config \
&& chmod -R 775 /var/www/html/app/tmp \
&& chmod -R 775 /var/www/html/app/webroot

# Suppress Apache ServerName warning
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Expose port 80
EXPOSE 80

# Start Apache server
CMD ["apache2-foreground"]
```

### Step 2: Create a Docker Compose File

Create a `docker-compose.yml` file in the root of your project directory with the following content:

```yaml
version: '3.8'

services:
app:
build: .
ports:
- "8080:80"
volumes:
- .:/var/www/html
depends_on:
- db

db:
image: mariadb:latest
environment:
MYSQL_ROOT_PASSWORD: rootpassword
MYSQL_DATABASE: orangescrum
MYSQL_USER: orangescrum_user
MYSQL_PASSWORD: password
volumes:
- db_data:/var/lib/mysql
- $PWD/database.sql:/docker-entrypoint-initdb.d/database.sql
ports:
- "3307:3306"

phpmyadmin:
image: phpmyadmin/phpmyadmin
environment:
PMA_HOST: db
MYSQL_ROOT_PASSWORD: rootpassword
ports:
- "8081:80"

mysql-client:
image: mysql:latest
command: sleep infinity
depends_on:
- db
networks:
- default

volumes:
db_data:
```

### Step 3: Prepare the Database Initialization File

Ensure your `database.sql` file is in the root of your project directory. This file will be used to initialize the database.

### Step 4: Build and Start the Docker Containers

Run the following commands to build and start your Docker containers:

```bash
docker-compose down
docker-compose up -d
```

### Step 5: Verify Apache Configuration

1. **Access the `app` container:**
   ```bash
   docker exec -it orangescrum-app-1 bash
   ```

2. **Enable the `mod_headers` module:**
   ```bash
   a2enmod headers
   service apache2 restart
   ```

3. **Check that the module is loaded:**
   ```bash
   apache2ctl -M | grep headers
   ```

   You should see `headers_module (shared)` in the output.

### Step 6: Access phpMyAdmin

1. **Open your browser and navigate to `http://localhost:8081`.**
2. **Log in using the following credentials:**
    - **Server:** db
    - **Username:** root
    - **Password:** rootpassword

3. **Verify the `orangescrum` database and tables are correctly set up.**

### Step 7: Access Orangescrum Application

Open your browser and navigate to `http://localhost:8080` to access the Orangescrum application.

### Step 8: Monitor Logs and Performance

To monitor the logs for troubleshooting or performance insights:

- **View Apache logs:**
  ```bash
  docker logs orangescrum-app-1
  ```

- **View MariaDB logs:**
  ```bash
  docker logs orangescrum-db-1
  ```

### Step 9: Set Up Backup and Restore

To safeguard your data, set up regular backups of your MySQL database:

- **Backup Command:**
  ```bash
  docker exec orangescrum-db-1 mysqldump -u root -p orangescrum > backup.sql
  ```

- **Restore Command:**
  ```bash
  docker exec -i orangescrum-db-1 mysql -u root -p orangescrum < backup.sql
  ```

## Troubleshooting

If you encounter any issues, ensure the following:

- The `mod_headers` module is enabled and loaded.
- The `ServerName` directive is set to suppress warnings.
- File permissions are correctly set for the application files and directories.
