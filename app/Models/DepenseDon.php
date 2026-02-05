<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepenseDon extends Model
{
    use HasFactory;

    protected $table = 'depense_dons';

    protected $fillable = [
        'don_id',
        'motif',
        'montant',
        'description',
        'date_depense',
        'nom_responsable',
        'prenom_responsable',
        'statut',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'date_depense' => 'date',
    ];

    public function canBeEdited(): bool
    {
        // Vérifier si moins de 10 minutes se sont écoulées depuis la création
        if (!$this->created_at) {
            return true; // Si pas de created_at, autoriser la modification
        }
        return $this->created_at->diffInMinutes(now()) < 10;
    }

    public function don()
    {
        return $this->belongsTo(Don::class, 'don_id');
    }
}
