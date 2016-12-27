<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPermissions extends Model
{
    public $incrementing = false;

    public $timestamps = false;

    protected $table = 'user_permissions';

    protected $primaryKey = 'id';

    // Define relationship
    public function user()
    {
        // related model, foreign key (optional), parent table's custom key (optional)
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
}
