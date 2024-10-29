init: init-ci
init-ci: docker-down-clear \
	clear \
	docker-pull docker-build docker-up

up: docker-up
down: docker-down
restart: down up

#fast composer commands
co: composer-outdated
cu: composer-update

#check all
check: lint analyze #test

#Docker
docker-up:
	docker compose up -d

docker-down:
	docker compose down --remove-orphans

docker-down-clear:
	docker compose down -v --remove-orphans

docker-pull:
	docker compose pull

docker-build:
	docker compose build --pull

clear:
	docker run --rm -v ${PWD}/:/app -w /app alpine sh -c 'rm -rf var/cache/* var/log/* var/temp/* var/mysql/* var/test/*'
	docker run --rm -v ${PWD}/:/app -w /app alpine sh -c 'rm -rf frontend/runtime/cache/* console/runtime/cache/* backend/runtime/cache/* api/runtime/cache/*'
	docker run --rm -v ${PWD}/:/app -w /app alpine sh -c 'rm -rf frontend/runtime/logs/* console/runtime/logs/* backend/runtime/logs/* api/runtime/logs/*'

permissions:
	docker run --rm -v ${PWD}/:/app -w /app alpine chmod 777 var/cache var/log var/temp var/test var/mysql
	docker run --rm -v ${PWD}/:/app -w /app alpine chmod 777 frontend/runtime frontend/web/assets frontend/web/minify frontend/web/uploads

#Composer
composer-install:
	docker compose run --rm php-cli composer install

composer-update:
	docker compose run --rm php-cli composer update

composer-autoload:
	docker compose run --rm php-cli composer dump-autoload

composer-outdated:
	docker compose run --rm php-cli composer outdated


#Lint and analyze
lint:
	docker compose run --rm php-cli composer lint
	docker compose run --rm php-cli composer php-cs-fixer fix -- --dry-run --diff

cs-fix:
	docker compose run --rm php-cli composer php-cs-fixer fix

analyze:
	docker compose run --rm php-cli composer psalm


#Tests
test:
	docker compose run --rm php-cli composer test

test-coverage:
	docker compose run --rm php-cli composer test-coverage

test-unit:
	docker compose run --rm php-cli composer test -- --testsuite=unit

test-unit-coverage:
	docker compose run --rm php-cli composer test-coverage -- --testsuite=unit

test-functional:
	docker compose run --rm php-cli composer test -- --testsuite=functional

test-functional-coverage:
	docker compose run --rm php-cli composer test-coverage -- --testsuite=functional


#Migrations
migrations-new:
	docker compose run --rm php-cli php yii migrate/create z

migrations-migrate:
	docker compose run --rm php-cli php yii migrate


#Build
build:
	docker --log-level=debug build --pull --file=./docker/production/nginx/Dockerfile --tag=${REGISTRY}/api-nginx:${IMAGE_TAG} .
	docker --log-level=debug build --pull --file=./docker/production/php-fpm/Dockerfile --tag=${REGISTRY}/php-fpm:${IMAGE_TAG} .
	docker --log-level=debug build --pull --file=./docker/production/php-console/Dockerfile --tag=${REGISTRY}/api-php-console:${IMAGE_TAG} .

try-build:
	REGISTRY=localhost IMAGE_TAG=0 make build

push:
	docker push ${REGISTRY}/api-nginx:${IMAGE_TAG}
	docker push ${REGISTRY}/php-fpm:${IMAGE_TAG}
	docker push ${REGISTRY}/api-php-console:${IMAGE_TAG}

deploy:
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'docker login -u=${DOCKERHUB_USER} -p=${DOCKERHUB_PASSWORD} ${REGISTRY}'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'rm -rf ${PROJECT_NAME}/v_${BUILD_NUMBER}'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'mkdir -p ${PROJECT_NAME}/v_${BUILD_NUMBER}'

	scp -o StrictHostKeyChecking=no -P ${PORT} ${COMPOSE_FILE} deploy@${HOST}:${PROJECT_NAME}/v_${BUILD_NUMBER}/docker-compose.yml
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd ${PROJECT_NAME}/v_${BUILD_NUMBER} && echo "DOCKER_NETWORK_MTU=${DOCKER_NETWORK_MTU}" >> .env'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd ${PROJECT_NAME}/v_${BUILD_NUMBER} && echo "COMPOSE_PROJECT_NAME=api" >> .env'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd ${PROJECT_NAME}/v_${BUILD_NUMBER} && echo "REGISTRY=${REGISTRY}" >> .env'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd ${PROJECT_NAME}/v_${BUILD_NUMBER} && echo "IMAGE_TAG=${IMAGE_TAG}" >> .env'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd ${PROJECT_NAME}/v_${BUILD_NUMBER} && echo "APP_ENV=${APP_ENV}" >> .env'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd ${PROJECT_NAME}/v_${BUILD_NUMBER} && echo "APP_DEBUG=${APP_DEBUG}" >> .env'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd ${PROJECT_NAME}/v_${BUILD_NUMBER} && echo "OPENAPI_PATH=${OPENAPI_PATH}" >> .env'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd ${PROJECT_NAME}/v_${BUILD_NUMBER} && echo "OPENAPI_MODULES_PATH=${OPENAPI_MODULES_PATH}" >> .env'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd ${PROJECT_NAME}/v_${BUILD_NUMBER} && echo "SENTRY_DSN=${SENTRY_DSN}" >> .env'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd ${PROJECT_NAME}/v_${BUILD_NUMBER} && echo "SCHEME=${SCHEME}" >> .env'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd ${PROJECT_NAME}/v_${BUILD_NUMBER} && echo "DOMAIN=${DOMAIN}" >> .env'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd ${PROJECT_NAME}/v_${BUILD_NUMBER} && echo "DB_HOST=${DB_HOST}" >> .env'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd ${PROJECT_NAME}/v_${BUILD_NUMBER} && echo "DB_PORT=${DB_PORT}" >> .env'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd ${PROJECT_NAME}/v_${BUILD_NUMBER} && echo "DB_USER=${DB_USER}" >> .env'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd ${PROJECT_NAME}/v_${BUILD_NUMBER} && echo "DB_PASSWORD=${DB_PASSWORD}" >> .env'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd ${PROJECT_NAME}/v_${BUILD_NUMBER} && echo "DB_NAME=${DB_NAME}" >> .env'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd ${PROJECT_NAME}/v_${BUILD_NUMBER} && echo "MAILER_HOST=${MAILER_HOST}" >> .env'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd ${PROJECT_NAME}/v_${BUILD_NUMBER} && echo "MAILER_PORT=${MAILER_PORT}" >> .env'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd ${PROJECT_NAME}/v_${BUILD_NUMBER} && echo "MAILER_ENCRYPTION=${MAILER_ENCRYPTION}" >> .env'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd ${PROJECT_NAME}/v_${BUILD_NUMBER} && echo "MAILER_USERNAME=${MAILER_USERNAME}" >> .env'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd ${PROJECT_NAME}/v_${BUILD_NUMBER} && echo "MAILER_PASSWORD=${MAILER_PASSWORD}" >> .env'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd ${PROJECT_NAME}/v_${BUILD_NUMBER} && echo "MAILER_FROM_EMAIL=${MAILER_FROM_EMAIL}" >> .env'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd ${PROJECT_NAME}/v_${BUILD_NUMBER} && echo "MAILER_FROM_NAME=${MAILER_FROM_NAME}" >> .env'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd ${PROJECT_NAME}/v_${BUILD_NUMBER} && echo "CENTRIFUGO_HOST=${CENTRIFUGO_HOST}" >> .env'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd ${PROJECT_NAME}/v_${BUILD_NUMBER} && echo "CENTRIFUGO_WS=${CENTRIFUGO_WS}" >> .env'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd ${PROJECT_NAME}/v_${BUILD_NUMBER} && echo "CENTRIFUGO_API_KEY=${CENTRIFUGO_API_KEY}" >> .env'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd ${PROJECT_NAME}/v_${BUILD_NUMBER} && echo "CENTRIFUGO_SECRET=${CENTRIFUGO_SECRET}" >> .env'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd ${PROJECT_NAME}/v_${BUILD_NUMBER} && echo "RABBIT_MQ_HOST=${RABBIT_MQ_HOST}" >> .env'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd ${PROJECT_NAME}/v_${BUILD_NUMBER} && echo "RABBIT_MQ_PORT=${RABBIT_MQ_PORT}" >> .env'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd ${PROJECT_NAME}/v_${BUILD_NUMBER} && echo "RABBIT_MQ_USER=${RABBIT_MQ_USER}" >> .env'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd ${PROJECT_NAME}/v_${BUILD_NUMBER} && echo "RABBIT_MQ_PASSWORD=${RABBIT_MQ_PASSWORD}" >> .env'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd ${PROJECT_NAME}/v_${BUILD_NUMBER} && echo "SMART_CAPTCHA_PRIVATE_KEY=${SMART_CAPTCHA_PRIVATE_KEY}" >> .env'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd ${PROJECT_NAME}/v_${BUILD_NUMBER} && echo "SMART_CAPTCHA_ENABLED=${SMART_CAPTCHA_ENABLED}" >> .env'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd ${PROJECT_NAME}/v_${BUILD_NUMBER} && echo "SMSRU_TOKEN=${SMSRU_TOKEN}" >> .env'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd ${PROJECT_NAME}/v_${BUILD_NUMBER} && echo "REDSMS_FROM=${REDSMS_FROM}" >> .env'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd ${PROJECT_NAME}/v_${BUILD_NUMBER} && echo "REDSMS_LOGIN=${REDSMS_LOGIN}" >> .env'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd ${PROJECT_NAME}/v_${BUILD_NUMBER} && echo "REDSMS_TOKEN=${REDSMS_TOKEN}" >> .env'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd ${PROJECT_NAME}/v_${BUILD_NUMBER} && echo "DADATA_TOKEN=${DADATA_TOKEN}" >> .env'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd ${PROJECT_NAME}/v_${BUILD_NUMBER} && echo "DADATA_SECRET=${DADATA_SECRET}" >> .env'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd ${PROJECT_NAME}/v_${BUILD_NUMBER} && echo "GREEN_API_ID=${GREEN_API_ID}" >> .env'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd ${PROJECT_NAME}/v_${BUILD_NUMBER} && echo "GREEN_API_TOKEN=${GREEN_API_TOKEN}" >> .env'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd ${PROJECT_NAME}/v_${BUILD_NUMBER} && echo "GREEN_API_PHONE=${GREEN_API_PHONE}" >> .env'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd ${PROJECT_NAME}/v_${BUILD_NUMBER} && echo "WHATSAPP_GROUP_BOT_NAME=${WHATSAPP_GROUP_BOT_NAME}" >> .env'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd ${PROJECT_NAME}/v_${BUILD_NUMBER} && echo "TELEGRAM_GROUP_TOKEN=${TELEGRAM_GROUP_TOKEN}" >> .env'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd ${PROJECT_NAME}/v_${BUILD_NUMBER} && echo "TELEGRAM_GROUP_BOT_NAME=${TELEGRAM_GROUP_BOT_NAME}" >> .env'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd ${PROJECT_NAME}/v_${BUILD_NUMBER} && echo "ST_HOST=${ST_HOST}" >> .env'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd ${PROJECT_NAME}/v_${BUILD_NUMBER} && echo "ST_SIGNATURE_KEY=${ST_SIGNATURE_KEY}" >> .env'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd ${PROJECT_NAME}/v_${BUILD_NUMBER} && echo "S3_KEY=${S3_KEY}" >> .env'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd ${PROJECT_NAME}/v_${BUILD_NUMBER} && echo "S3_SECRET=${S3_SECRET}" >> .env'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd ${PROJECT_NAME}/v_${BUILD_NUMBER} && echo "S3_ENDPOINT=${S3_ENDPOINT}" >> .env'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd ${PROJECT_NAME}/v_${BUILD_NUMBER} && echo "S3_BUCKET=${S3_BUCKET}" >> .env'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd ${PROJECT_NAME}/v_${BUILD_NUMBER} && echo "S3_SECRET_BUCKET=${S3_SECRET_BUCKET}" >> .env'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd ${PROJECT_NAME}/v_${BUILD_NUMBER} && echo "BANK_REDIRECT_URI=${BANK_REDIRECT_URI}" >> .env'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd ${PROJECT_NAME}/v_${BUILD_NUMBER} && echo "MAIL_REDIRECT_URI=${MAIL_REDIRECT_URI}" >> .env'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd ${PROJECT_NAME}/v_${BUILD_NUMBER} && echo "MAIL_YANDEX_CLIENT_ID=${MAIL_YANDEX_CLIENT_ID}" >> .env'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd ${PROJECT_NAME}/v_${BUILD_NUMBER} && echo "MAIL_YANDEX_CLIENT_SECRET=${MAIL_YANDEX_CLIENT_SECRET}" >> .env'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd ${PROJECT_NAME}/v_${BUILD_NUMBER} && echo "MAIL_MAILRU_CLIENT_ID=${MAIL_MAILRU_CLIENT_ID}" >> .env'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd ${PROJECT_NAME}/v_${BUILD_NUMBER} && echo "MAIL_MAILRU_CLIENT_SECRET=${MAIL_MAILRU_CLIENT_SECRET}" >> .env'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd ${PROJECT_NAME}/v_${BUILD_NUMBER} && echo "MAIL_GOOGLE_CLIENT_ID=${MAIL_GOOGLE_CLIENT_ID}" >> .env'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd ${PROJECT_NAME}/v_${BUILD_NUMBER} && echo "MAIL_GOOGLE_CLIENT_SECRET=${MAIL_GOOGLE_CLIENT_SECRET}" >> .env'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd ${PROJECT_NAME}/v_${BUILD_NUMBER} && echo "TELEGRAM_SUPPORT_BOT_TOKEN=${TELEGRAM_SUPPORT_BOT_TOKEN}" >> .env'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd ${PROJECT_NAME}/v_${BUILD_NUMBER} && echo "TELEGRAM_SUPPORT_CHAT_ID=${TELEGRAM_SUPPORT_CHAT_ID}" >> .env'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd ${PROJECT_NAME}/v_${BUILD_NUMBER} && echo "TELEGRAM_SUPPORT_THREAD_ID=${TELEGRAM_SUPPORT_THREAD_ID}" >> .env'

	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'mkdir -p ${PROJECT_NAME}/v_${BUILD_NUMBER}/secrets'
	scp -o StrictHostKeyChecking=no -P ${PORT} public.key deploy@${HOST}:${PROJECT_NAME}/v_${BUILD_NUMBER}/secrets/public.key
	scp -o StrictHostKeyChecking=no -P ${PORT} private.key deploy@${HOST}:${PROJECT_NAME}/v_${BUILD_NUMBER}/secrets/private.key

	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd ${PROJECT_NAME}/v_${BUILD_NUMBER} && docker compose pull'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd ${PROJECT_NAME}/v_${BUILD_NUMBER} && docker compose up --build --remove-orphans -d'

	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'rm -f ${PROJECT_NAME}/${PROJECT_NAME}'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'ln -sr ${PROJECT_NAME}/v_${BUILD_NUMBER} ${PROJECT_NAME}/${PROJECT_NAME}'

upd:
	git pull
	/opt/php81/bin/php yii migrate
	/opt/php81/bin/php composer.phar install
	rm -rf frontend/runtime/cache/* frontend/runtime/logs/*
	rm -rf backend/runtime/cache/* backend/runtime/logs/*
	rm -rf console/runtime/cache/* console/runtime/logs/*
	rm -rf var/cache/* var/log/* var/temp/*

pay:
	docker compose run --rm php-cli php yii cron/account-pay/execute

one-test:
	docker compose run --rm php-cli php yii dev/one-c-test/execute

bank:
	docker compose run --rm php-cli php yii cron/bank-transaction/execute
