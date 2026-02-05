<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Don extends Model
{
    use HasFactory;

    protected $table = 'dons';

    protected $fillable = [
        'donateur',
        'type_don',
        'montant',
        'description',
        'date_don',
        'mode_paiement',
        'statut',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'date_don' => 'date',
    ];

    public function canBeEdited(): bool
    {
        // Vérifier si moins de 10 minutes se sont écoulées depuis la création
        if (!$this->created_at) {
            return true; // Si pas de created_at, autoriser la modification
        }
        return $this->created_at->diffInMinutes(now()) < 10;
    }

    public function depenses_dons()
    {
        return $this->hasMany(DepenseDon::class, 'don_id');
    }
}
