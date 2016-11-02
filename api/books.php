<?php

$app->get('/api/books', function () {
    //$response->getBody()->write('Welcome to books!');
    //return $response;
    //echo 'Welcome!';
    require_once('dbconnect.php');

    $query = "SELECT * FROM books ORDER BY id";
    $result = $mysqli->query($query);

    while ($row = $result->fetch_assoc())
    {
        $data[] = $row;
    }

    if (isset($data))
    {
        header('Content-Type: application/json'); // prevent data caching
        echo json_encode($data);
    }
});
