FROM php:8.2-apache

RUN apt-get update && apt-get upgrade -y
RUN apt-get install -y libldap2-dev
RUN docker-php-ext-configure ldap --with-ldap
RUN docker-php-ext-install mysqli pdo pdo_mysql ldap

COPY ./src /var/www/html/
