<?php

$connection = new PDO('sqlite:' . __DIR__ . '/blog.sqlite', null, null, [
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);

//Вставляем строку в таблицу пользователей
// $connection->exec(
//     "INSERT INTO users (first_name, last_name) VALUES ('Ivan', 'Nikitin')"
// );
