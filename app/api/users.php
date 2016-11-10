<?php

//use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Book;

$app->get('/api/users', function () {

    // https://laravel.com/docs/5.3/database
    // https://www.youtube.com/watch?v=m5Jmh9JKnyQ
    // https://www.youtube.com/watch?v=lEZ8cnVGVZE
    // 
    //$users = User::all();
    //$users = DB::select('select * from users where id = ?', ['344c0ffac81941df9c7949c026e7ac44']);
    
    var_dump($users);
    exit;
    
    header('Content-Type: application/json'); // prevent data caching
    return $users->toJson(JSON_UNESCAPED_UNICODE);
});

$app->get('/api/users/{id}', function ($request) {

    $id = $request->getAttribute('id');
    $user = User::where('id', $id)->first();
    
    var_dump($user);
    echo '<br>';
    echo '<br>';
    var_dump($user->toJson(JSON_UNESCAPED_UNICODE));
    exit;

    if (isset($user))
    {
        header('Content-Type: application/json');
        return $user->toJson();
    }
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