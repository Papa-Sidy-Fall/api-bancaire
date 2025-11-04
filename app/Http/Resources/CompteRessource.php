<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompteRessource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $depot = $this->depot_sum ?? 0;
        $retrait = $this->retrait_sum ?? 0;
        $solde = $depot - $retrait;

        return [
            'id' => $this->id,
            'numeroCompte' => $this->numero_compte,
            'titulaire' => $this->titulaire,
            'type' => $this->type,
            'solde' => (float) $solde,
            'devise' => $this->devise,
            'dateCreation' => $this->created_at,
            'statut' => $this->statut,
            'metadata' => [
                'derniereModification' => $this->updated_at,
                'version' => $this->version,
            ]
        ];
    }
}
