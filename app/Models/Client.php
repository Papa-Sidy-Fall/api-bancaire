<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory, HasUuids;
    protected $keyType = 'string';
    public $incrementing = false;
     protected $fillable= [
        'user_id',
    ];
     public function User(){
        return $this->belongsTo(User::class);
    }
    public function Comptes(){
        return $this->belongsToMany(Compte::class);
    }
}
