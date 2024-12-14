<?php
namespace App\Providers;

use App\Models\Chirp;
use App\Policies\ChirpPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Chirp::class => ChirpPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
