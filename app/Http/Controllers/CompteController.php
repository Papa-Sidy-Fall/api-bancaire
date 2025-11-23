<?php

namespace App\Http\Controllers;

use App\Http\Resources\CompteRessource;
use Illuminate\Http\Request;
use App\Models\Compte;
use App\Http\Resources\MetaRessource;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Exceptions\CompteNotFoundException;


class CompteController extends Controller
{
        use ApiResponse;

    // fonction index pour 
 public function index(Request $request)
    {

        // return response()->json(['message' => 'test']);

        try {

            
            $user = Auth::user();

            if (!$user) {
                return $this->errorResponse('Non autorisé', 401);
            }

            $cacheKey =  'comptes_' . md5(json_encode($request->all()));

            $cacheData = Cache::get($cacheKey);

            // if ($cacheData) {
            //     return $this->successResponse($cacheData);
            // }


            $comptes = Compte::filtrerComptes($request->all(), $user)
                ->paginate(min($request->get('limit', 10), 100))
                ->appends($request->all());


            $data = [
                'data' => CompteRessource::collection($comptes),
                'meta' => new MetaRessource($comptes)
            ];

            Cache::put($cacheKey, $data, now()->addMinutes(10));

            return $this->successResponse($data, 'comptes recuperer avec succes ! ',$comptes->total(), $user->id );

        } catch (\Exception $e) {
            
            return $this->errorResponse('Erreur lors de la récupération des comptes: ' . $e->getMessage(), 500);
        }
    }
  public function show(Request $request, $id)
   {
       $user = Auth::user();

       if($user->isAdmin()){
           $compte = Compte::find($id);
       }else{
           $compte = Compte::where('id', $id)->where('user_id', $user->id)->first();
       }


       return $this->successResponse(new CompteRessource($compte), 'Compte récupéré avec succès', 1, $user->id);
   }
  public function store(Request $request)
{
    $user = Auth::user();

    if (!$user) {
        return $this->errorResponse('Non autorisé', 401);
    }

    // On valide les données imbriquées
    $data = $request->validate([
        'client.titulaire' => ['required', 'string', 'max:255'],
        'type' => ['required', 'in:epargne,cheque'],
        'devise' => ['required', 'string', 'max:3'],
    ]);

    $compte = Compte::create([
        'titulaire' => $data['client']['titulaire'],
        'type' => $data['type'],
        'devise' => $data['devise'],
        'user_id' => $user->id,
        'statut' => 'actif',
        'derniere_modification' => now(),
        'version' => 1,
    ]);

    return $this->successResponse(new CompteRessource($compte), 'Compte créé avec succès', 1, $user->id);
}

}