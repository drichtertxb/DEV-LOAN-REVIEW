FROM php:7.2-apache

ARG DEBIAN_FRONTEND=noninteractive

# apt-get -y install realmd sssd sssd-tools samba-common krb5-user packagekit samba-common-bin samba-libs adcli ntp

RUN apt-get update && apt-get install -y \
    gnupg git zlib1g-dev libpng-dev libicu-dev libjpeg-dev libffi-dev wget zip \
    libxml2-dev libfreetype6 libfreetype6-dev g++ make autoconf \
    build-essential libfontconfig1-dev libssl-dev libx11-dev libxext-dev libxrender-dev \
    libldap2-dev libnss-ldap libpam-ldap ldap-utils \
    realmd sssd sssd-tools samba-common krb5-user packagekit samba-common-bin samba-libs adcli ntp \
    iputils-ping ntpdate krb5-user python vim nano \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && pecl install xdebug \
    && pecl install redis \
    && docker-php-ext-enable redis xdebug \
    && docker-php-ext-install pdo_mysql intl zip gd pcntl bcmath calendar ldap \
    && curl -sL https://deb.nodesource.com/setup_8.x | bash \
    && apt-get install -y nodejs

RUN a2enmod rewrite

COPY 000-default.conf /etc/apache2/sites-available/000-default.conf
COPY php.ini /usr/local/etc/php/

CMD mkdir /etc/dhcp;
CMD chmod 777 /etc/dhcp

CMD mkdir /etc/ldap;
CMD chmod 777 /etc/ldap

CMD mkdir /etc/sssd
CMD chmod 777 /etc/sssd

#CMD chmod 777 /etc/sssd/sssd.conf

#realm leave
#RUN realm --verbose join --user=admin_cbautista --computer-ou=OU=Computers_Unix,DC=pandgassociates,DC=com pandgassociates.com

# COPY xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

ARG XDEBUG_REMOTE_ENABLE
ARG XDEBUG_REMOTE_HOST
ARG XDEBUG_REMOTE_AUTO_START
ARG XDEBUG_REMOTE_CONNECT_BACK
ARG XDEBUG_IDEKEY

RUN echo "[xdebug]" > /usr/local/etc/php/conf.d/xdebug-dev.ini
RUN echo "xdebug.remote_enable=${XDEBUG_REMOTE_ENABLE}" >> /usr/local/etc/php/conf.d/xdebug-dev.ini
RUN echo "xdebug.remote_host=${XDEBUG_REMOTE_HOST}" >> /usr/local/etc/php/conf.d/xdebug-dev.ini
RUN echo "xdebug.remote_autostart=${XDEBUG_REMOTE_AUTO_START}" >> /usr/local/etc/php/conf.d/xdebug-dev.ini
RUN echo "xdebug.remote_connect_back=${XDEBUG_REMOTE_CONNECT_BACK}" >> /usr/local/etc/php/conf.d/xdebug-dev.ini
RUN echo "xdebug.idekey=${XDEBUG_IDEKEY}" >> /usr/local/etc/php/conf.d/xdebug-dev.ini

WORKDIR /var/www/html

CMD ["/usr/sbin/apache2ctl", "-D", "FOREGROUND"]




