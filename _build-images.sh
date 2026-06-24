#!/bin/sh

registry=bruder:3000

# web
docker build -t ${registry}/heia.kim/frontend/web:latest .
docker push ${registry}/heia.kim/frontend/web:latest

# php
docker build -t ${registry}/heia.kim/frontend/php:latest ./docker/deploy/php
docker push ${registry}/heia.kim/frontend/php:latest
