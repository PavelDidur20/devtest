1) docker build
2.1) docker compose -up -d
2) создать .env файл c 
MYSQL_ROOT_PASSWORD=password
MYSQL_DATABASE=yii2db
MYSQL_USER=yiiuser
MYSQL_PASSWORD=password


YII_ENV=dev

3) ./yii migrate запустить миграции

Запустить очереди 
3.1) ./yii queue/listen & 

4) должно работать, проверял в Postman :)


Проверить можно через Postman

POST http://localhost:8080/requests
connection keep-alive
Content-Type application/json

{
    "name": "Вася Пупкин",
    "email": "vasya@example.com",
    "message": "Привед"
}


PUT http://localhost:8080/requests/1
Basic auth
password: admin
username: admin 

connection keep-alive
Content-Type application/json
{
    "status": "Resolved",
    "comment" :" малаца"
}

Тесты не писал.