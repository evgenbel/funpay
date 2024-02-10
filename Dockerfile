FROM php:8.3-cli

WORKDIR /var/www/html

RUN docker-php-ext-install mysqli

EXPOSE 8000

#CMD [ "php -i", " " ]
CMD [ "php", "./test.php" ]

