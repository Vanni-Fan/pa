# https://gitlab.com/twistersfury/docker-images/blob/master/phalcon/latest/Dockerfile
FROM php:7.4-fpm

RUN apt-get update && apt-get install -y \
        curl \
    && rm -rf /var/lib/apt/lists/*

ARG PHALCON_BRANCH=master

#Configs For Versions
ENV PHALCON_BRANCH=${PHALCON_BRANCH}
ENV PSR_BRANCH master

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
