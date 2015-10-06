# laravel-reddit
Laravel Reddit-like Community

Still a work in progress...

# Installation

1. git clone https://github.com/Halnex/laravel-reddit projectname
2. composer install
3. php artisan migrate

Open AuthServiceProvider.php and add the following method to top of your class
```php
use Illuminate\Auth\Access\Gate;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
```
Now add the following method to the class in AuthServiceProvider.php
```php
public function boot(GateContract $gate)
{
    parent::registerPolicies($gate);

    $gate->define('update-post', function ($user, $post) {
        // Check if user is subreddit owner
        if ($user->id === $post->subreddit->user->id) {
            return true;
        }

        // Check if user is the post author
        if ($user->id === $post->user_id) {
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
}
```
