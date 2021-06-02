### This is my Smart Tribune application submission

#### Installing the project:
git clone https://github.com/NFLorD/smart-tribune.git \
cd smart-tribune/infra

docker-compose build \
docker-compose up -d

docker exec -t infra_php_1 sh -c "composer install \
&& php /var/www/html/bin/console doctrine:database:create --if-not-exists \
&& php /var/www/html/bin/console doctrine:migrations:migrate --no-interaction"

#### Preparing the test environment:
docker exec -t infra_php_1 sh -c "php /var/www/html/bin/console --env=test doctrine:database:create --if-not-exists \
&& php /var/www/html/bin/console --env=test doctrine:migrations:migrate --no-interaction \
&& php /var/www/html/bin/console --env=test doctrine:fixtures:load --no-interaction"

#### Running the tests:
docker exec -t infra_php_1 sh -c "php ./vendor/bin/phpunit"

#### To asynchronously save QuestionHistory objects:
I'd add a RabbitMQ container, create a queue and send it messages in the OnQuestionUpdate listener.
The queue would then be consumed via another phpfpm container dedicated to processing the queues.