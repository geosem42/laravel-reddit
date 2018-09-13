<?php
namespace App\Providers;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Auth\Access\Gate;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];
    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
public function boot(GateContract $gate)
{
    parent::registerPolicies($gate);

    $gate->define('update-post', function ($user, $post, $isModerator) {
        if ($user->id === $post->subreddit->user->id) {
            return true;
        }

        if ($user->id === $post->user_id) {
            return true;
        }

        if ($isModerator) {
            return true;
        }

        return false;
    });

    $gate->define('update-sub', function($user, $subreddit) {
        if($user->id === $subreddit->user->id) {
            return true;
        }

        return false;
    });

    $gate->define('update-comment', function($user, $comment, $isModerator) {
        if($user->id === $comment->user_id) {
            return true;
        }

        if ($isModerator) {
            return true;
        }
    });
}



}

