# https://gitlab.com/twistersfury/docker-images/blob/master/phalcon/latest/Dockerfile
FROM php:7.4-fpm

RUN apt-get update && apt-get install -y \
        curl \
    && rm -rf /var/lib/apt/lists/*

ARG PHALCON_BRANCH=master

#Configs For Versions
ENV PHALCON_BRANCH=${PHALCON_BRANCH}
ENV PSR_BRANCH master

#安装 PDO_MYSQL
RUN docker-php-source extract \
    && docker-php-ext-install pdo_mysql \
    && docker-php-source delete

#Installing PSR
RUN curl -fsSL https://github.com/jbboehr/php-psr/archive/$PSR_BRANCH.tar.gz | tar xz \
    && cd php-psr-$PSR_BRANCH \
    && phpize \
    && ./configure \
    && make \
    && make test \
    && make install \
    && cd ../ \
    && docker-php-ext-enable --ini-name 0-psr.ini psr \
    && rm -rf php-psr-$PSR_BRANCH

#Installing Phalcon
RUN curl -fsSL https://github.com/phalcon/cphalcon/archive/$PHALCON_BRANCH.tar.gz | tar xz \
    && cd cphalcon-$PHALCON_BRANCH \
    && cd build \
    && ./install \
    && cd ../../ \
    && docker-php-ext-enable phalcon \
    && rm -rf cphalcon-$PHALCON_BRANCH

RUN curl -fsSL http://pecl.php.net/get/redis-5.2.1.tgz | tar xz; cd redis-5.2.1; phpize; ./configure; make; make install; docker-php-ext-enable redis; cd ../; rm -rf redis-5.2.1