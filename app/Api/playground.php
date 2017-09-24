<?php
namespace App\Api;

/**
 * Playground section is used for testing
 */
$app->get('/api/playground', function ($request, $response, $args) {
    $data = [
      (object)[
        'productId' => 2,
        'productName' => 'Garden Cart',
        'productCode' => 'GDN-0023',
        'releaseDate' => 'March 18, 2016',
        'price' => 32.99,
        'starRating' => 4.2,
        'imageUrl' => 'http://handtrucks2go.com/images/P/400107.JPG'
      ],
      (object)[
        'productId' => 5,
        'productName' => 'Hammer',
        'productCode' => 'TBX-0048',
        'releaseDate' => 'May 21, 2016',
        'price' => 8.9,
        'starRating' => 4.5,
        'imageUrl' => 'http://s.hswstatic.com/gif/hammer-1.jpg'
      ],
      (object)[
        'productId' => 7,
        'productName' => 'Shovel',
        'productCode' => 'SH-0087',
        'releaseDate' => 'May 21, 2015',
        'price' => 15.4,
        'starRating' => 3.3,
        'imageUrl' => 'http://assets.academy.com/mgen/34/10095934.jpg'
      ]
    ];

    $newResponse = $response
            ->withHeader('Access-Control-Allow-Origin', 'http://localhost:4200')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');

    return code_200($newResponse, $data);
});
