#!/bin/bash
# php
sudo phpdismod psr
php phpDocumentor.phar -d ./apps --setting=graphs.enabled=true -t ./docs/php
sudo phpenmod psr
# js
sudo rm -rf docs/js
sudo jsdoc -p -r -t jsdoc/bower_components/jaguarjs-jsdoc -c jsdoc/conf.json -d docs/js
# css