1) docker build .
   


2) создать .env файл c
   
MYSQL_ROOT_PASSWORD=password

MYSQL_DATABASE=yii2db

MYSQL_USER=yiiuser

MYSQL_PASSWORD=password

YII_ENV=dev


3) docker compose -up -d


4) ./yii migrate запустить миграции (внутри контейнера yii2)


Запустить очереди 

5) ./yii queue/listen & 


 Должно работать.



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
