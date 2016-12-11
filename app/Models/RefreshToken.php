<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefreshToken extends Model
{

    public $incrementing = false;

    public $timestamps = false;

    protected $table = 'refresh_tokens';

    protected $primaryKey = 'id';

    protected $fillable = [
        'used'
    ];

    // Define relationship
    public function user()
    {
        // related model, foreign key (optional), parent table's custom key (optional)
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
}
