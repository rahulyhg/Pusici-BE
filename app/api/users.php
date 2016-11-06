<?php

use App\Models\User;

$app->get('/api/users', function () {

    $users = User::all();
    
    header('Content-Type: application/json'); // prevent data caching
    return $users->toJson();
});

//$user = User::find('6d0fa2fed83d408b9293d77128ceeb9a');
//$user = User::where('email', 'libor.drapal@email.cz')->first();

$app->post('/api/users', function () {
    
//    var_dump(com_create_guid());
// insert into users values(unhex(replace(uuid(),'-','')), 'Andromeda');
    
    User::create([
        'id' => com_create_guid(),
        'first_name' => 'Albus',
        'last_name' => 'Dumbledore',
        'email' => 'albus.dumbledore@email.cz',
        'password' => '1234',
    ]);
});