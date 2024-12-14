<?php

namespace App\Http\Controllers;

use App\Models\Chirp;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ChirpController extends Controller
{
    /**
     * Affiche la liste des "chirps".
     */
    public function index(): View
    {
        // Récupérer tous les chirps, triés par date de création (du plus récent au plus ancien)
        $chirps = Chirp::latest()->get();

        // Passer les chirps à la vue 'home'
        return view('home', compact('chirps'));
    }

    /**
     * Stocke un nouveau "chirp" dans la base de données.
     */
    public function store(Request $request): RedirectResponse
    {
        // Validation des données d'entrée
        $validated = $request->validate([
            'message' => 'required|string|max:255',
        ]);

        // Associer le nouveau "chirp" à l'utilisateur authentifié
        $request->user()->chirps()->create($validated);

        // Redirection après création
        return redirect(route('chirps.index'))
            ->with('success', 'Votre chirp a été créé avec succès.');
    }

    /**
     * Affiche le formulaire pour modifier un "chirp" existant.
     */
    public function edit(Chirp $chirp): View
    {
        // Vérification d'autorisation (l'utilisateur doit être le propriétaire)
        Gate::authorize('update', $chirp);

        // Retourne la vue d'édition avec le "chirp" actuel
        return view('chirps.edit', compact('chirp'));
    }

    /**
     * Met à jour un "chirp" existant.
     */
    public function update(Request $request, Chirp $chirp): RedirectResponse
    {
        // Vérification d'autorisation
        Gate::authorize('update', $chirp);

        // Validation des données d'entrée
        $validated = $request->validate([
            'message' => 'required|string|max:255',
        ]);

        // Mise à jour du "chirp"
        $chirp->update($validated);

        // Redirection après mise à jour
        return redirect(route('chirps.index'))
            ->with('success', 'Votre chirp a été modifié avec succès.');
    }

    /**
     * Supprime un "chirp" de la base de données.
     */
    public function destroy(Chirp $chirp): RedirectResponse
    {
        // Vérification d'autorisation
        Gate::authorize('delete', $chirp);

        // Suppression du "chirp"
        $chirp->delete();

        // Redirection après suppression
        return redirect(route('chirps.index'))
            ->with('success', 'Votre chirp a été supprimé avec succès.');
    }
}
