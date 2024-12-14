<?php
namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Chirp;

class ChirpTest extends TestCase
{
    use RefreshDatabase;
    public function test_un_utilisateur_peut_creer_un_chirp()
    {
        $utilisateur = User::factory()->create();
        $this->actingAs($utilisateur);
    
        
        $reponse = $this->post('/chirps', ['message' => 'Mon premier chirp !']);
        dd($reponse->getContent(), $reponse->status());
        $reponse->assertStatus(201);
        $this->assertDatabaseHas('chirps', [
            'message' => 'Mon premier chirp !',
            'user_id' => $utilisateur->id,
        ]);
    }
    

    public function test_un_chirp_ne_peut_pas_avoir_un_contenu_vide()
    {
        $utilisateur = User::factory()->create();
        $this->actingAs($utilisateur);  
        $reponse = $this->post('/chirps', ['message' => '']);
        $reponse->assertSessionHasErrors(['message']);
    }
    
    public function test_un_chirp_ne_peut_pas_depasse_255_caracteres()
    {
        $utilisateur = User::factory()->create();
        $this->actingAs($utilisateur);  
        $longMessage = str_repeat('a', 256);  
        $reponse = $this->post('/chirps', ['message' => $longMessage]);
        $reponse->assertSessionHasErrors(['message']);
    }

    
    public function test_les_chirps_sont_affiches_sur_la_page_d_accueil()
    {
        
        $chirps = Chirp::factory()->count(3)->create();
        $reponse = $this->get('/');
        foreach ($chirps as $chirp) {
            $reponse->assertSee($chirp->message); 
        }
    }
    
   public function test_un_utilisateur_peut_modifier_son_chirp()
   {
    $utilisateur = User::factory()->create();
    $chirp = Chirp::factory()->create(['user_id' => $utilisateur->id]);
    $this->actingAs($utilisateur);
    $reponse = $this->patch("/chirps/{$chirp->id}", [
    'message' => 'Chirp modifié'
    ]);
    $reponse->assertStatus(302);
   
    $this->assertDatabaseHas('chirps', [
    'id' => $chirp->id,
    'message' => 'Chirp modifié',
    ]);
   }

   
public function test_un_utilisateur_peut_supprimer_son_chirp
()
{
 $utilisateur = User::factory()->create();
 $chirp = Chirp::factory()->create(['user_id' => $utilisateur->id]);
 $this->actingAs($utilisateur);
 $reponse = $this->delete("/chirps/{$chirp->id}");
 $reponse->assertStatus(302);
 $this->assertDatabaseMissing('chirps', [
    'id' => $chirp->id,
    ]);
   }


   public function test_un_utilisateur_ne_peut_pas_modifier_un_chirp_qui_ne_lui_appartient_pas()
   {

       $user1 = User::factory()->create();
       $user2 = User::factory()->create();
       $chirp = Chirp::factory()->create([
           'user_id' => $user1->id,
           'message' => 'Chirp de l\'utilisateur 1',
       ]);

     
       $this->actingAs($user2);


       $response = $this->patch("/chirps/{$chirp->id}", [
           'message' => 'Chirp modifié par utilisateur 2',
       ]);
       $response->assertStatus(403);
       $this->assertDatabaseHas('chirps', [
           'id' => $chirp->id,
           'message' => 'Chirp de l\'utilisateur 1',
       ]);
   }


   public function test_un_utilisateur_ne_peut_pas_supprimer_un_chirp_qui_ne_lui_appartient_pas()
   {
       $user1 = User::factory()->create();
       $user2 = User::factory()->create();

       
       $chirp = Chirp::factory()->create([
           'user_id' => $user1->id,
       ]);

       
       $this->actingAs($user2);

       
       $response = $this->delete("/chirps/{$chirp->id}");

       $response->assertStatus(403);

       
       $this->assertDatabaseHas('chirps', [
           'id' => $chirp->id,
       ]);
   }

   public function test_le_message_ne_peut_pas_etre_trop_long_lors_de_la_mise_a_jour()
{
    $user = User::factory()->create();
    $chirp = Chirp::factory()->create([
        'user_id' => $user->id,
        'message' => 'Ancien message',
    ]);

    $this->actingAs($user);

    $response = $this->patch(route('chirps.update', $chirp), [
        'message' => str_repeat('A', 256), 
    ]);

    $response->assertSessionHasErrors('message'); 
    $this->assertDatabaseHas('chirps', [
        'id' => $chirp->id,
        'message' => 'Ancien message', 
    ]);
}


public function test_un_utilisateur_ne_peut_pas_avoir_plus_de_10_chirps()
{
    $user = User::factory()->create();

    
    Chirp::factory()->count(10)->create(['user_id' => $user->id]);

    $this->actingAs($user);

    
    $response = $this->post(route('chirps.store'), [
        'message' => 'Ce chirp ne devrait pas être créé',
    ]);

    $response->assertStatus(400);
    $response->assertJson(['error' => 'Vous avez atteint le nombre maximum de chirps (10).']);
}

 

}
