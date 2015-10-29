# laravel-reddit
Reddit clone built with Laravel 5

Demo: http://maghnatis.com

# Packages Used
1. ["intervention/image"](https://github.com/Intervention/image)
2. ["oscarotero/Embed"](https://github.com/oscarotero/Embed)
3. ["mohankumaranna/comment"](https://github.com/mohankumaranna/comment)

# Features
1. Login/Register
2. Subreddits
3. Posts (link and text)
4. Moderators
5. Search
6. Threaded Comments with inline editing
7. Upvote/Downvote
8. User Profiles

# To-Do
1. Sorting

# Installation
1. git clone https://github.com/Halnex/laravel-reddit projectname
2. composer install
3. php artisan migrate

Open AuthServiceProvider.php and import the following classes.
```php
use Illuminate\Auth\Access\Gate;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
```
Now add the following method to the class
```php
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
```
