<?php

$app->get('/api/books', function () {
    require_once('db_connect.php');

    $query = "SELECT * FROM books ORDER BY id";
    $result = $mysqli->query($query);

    while ($row = $result->fetch_assoc())
    {
        $data[] = $row;
    }

    if (isset($data))
    {
        header('Content-Type: application/json'); // prevent data caching
        return json_encode($data);
    }
});

$app->get('/api/books/{id}', function ($request) {
    require_once('db_connect.php');
    
    $id = $request->getAttribute('id');
    $query = "SELECT * FROM books WHERE id=$id";
    $result = $mysqli->query($query);
    $data[] = $result->fetch_assoc();

    header('Content-Type: application/json');
    return json_encode($data);
});

$app->post('/api/books', function ($request) {
    require_once('db_connect.php');
    
    $query = "INSERT INTO `books` (`title`, `author`, `url`) VALUES (?,?,?)";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("sss", $title, $author, $url);

    $title = $request->getParsedBody()['title'];
    $author = $request->getParsedBody()['author'];
    $url = $request->getParsedBody()['url'];

    $stmt->execute();
});

$app->put('/api/books/{id}', function ($request) {
    require_once('db_connect.php');
    
    $id = $request->getAttribute('id');
    $query = "UPDATE `books` SET `title` = ?, `author` = ?, `url` = ? WHERE `books`.`id` = $id";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("sss", $title, $author, $url);

    $title = $request->getParsedBody()['title'];
    $author = $request->getParsedBody()['author'];
    $url = $request->getParsedBody()['url'];

    $stmt->execute();
});

$app->delete('/api/books/{id}', function ($request) {
    require_once('db_connect.php');
    
    $id = $request->getAttribute('id');
    $query = "DELETE FROM books WHERE id = $id";
    $result = $mysqli->query($query);
});
