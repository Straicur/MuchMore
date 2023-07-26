#!/bin/bash

pwd

if [ ! -f .env.test.local ]; then
  printf "File .env.test.local not exist\n"
  exit 1
fi

APP_ENV=test symfony console doctrine:database:drop --force
APP_ENV=test symfony console doctrine:database:create
echo | APP_ENV=test symfony console doctrine:migrations:migrate