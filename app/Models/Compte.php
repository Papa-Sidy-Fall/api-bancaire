<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Compte extends Model
{
    use HasFactory, HasUuids;
    protected $keyType = 'string';
    public $incrementing = false;

     protected $fillable= [
         'id',
        'numero_compte',
        'user_id',
        'titulaire',
        'type',
        'devise',
        'statut',
        'derniere_modification',
        'version',
        'code_expire_at'
    ];
    protected function numeroCompte(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value,
            set: fn ($value) => $value ?: 'ACC-' . strtoupper(Str::random(10))
        );
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($compte) {
            if (!$compte->numero_compte) {
                $compte->numero_compte = 'ACC-' . strtoupper(Str::random(10));
            }
        });
    }
    public function User(){
        return $this->belongsTo(User::class, 'user_id','id');
    }

    public function scopeFiltrerComptes(Builder $query, $filters = [], $user = null)
{
    $isAdmin = $user && method_exists($user, 'isAdmin') ? $user->isAdmin() : false;

    $query->whereIn('type', ['epargne', 'cheque'])
          ->where('statut', 'actif');

    if (!$isAdmin && $user) {
        $query->where('user_id', $user->id);
    }

    if (!empty($filters['type'])) {
        $query->where('type', $filters['type']);
    }

    if (!empty($filters['statut'])) {
        $query->where('statut', $filters['statut']);
    }

    if(!empty($filters['numero_compte'])) {
        $query->where('numero_compte', $filters['numero_compte']);
    }

    if (!empty($filters['search'])) {
        $search = $filters['search'];
        $query->where(function ($q) use ($search) {
            $q->where('titulaire', 'like', "%{$search}%")
              ->orWhere('numero_compte', 'like', "%{$search}%");
        });
    }

    $sortField = match ($filters['sort'] ?? null) {
        'dateCreation' => 'created_at',
        'solde' => 'solde',
        'titulaire' => 'titulaire',
        default => 'created_at',
    };

    $order = in_array($filters['order'] ?? '', ['asc', 'desc'])
        ? $filters['order']
        : 'desc';

    $query->orderBy($sortField, $order);


    return $query;
}
    
}
