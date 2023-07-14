#!/bin/bash
sudo phpdismod psr
php phpDocumentor.phar -d ./src -d ./tasks -d ./templates/project/app --setting=graphs.enabled=true -t ./docs
sudo phpenmod psr