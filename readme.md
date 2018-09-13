
## Installation on an Ubuntu 16.04 VPS / virtual box


1. Run "git clone https://github.com/Project-Oblio/laravel-reddit.git; cd laravel-reddit; ./install.sh". There are some manual configurations needed:

2. Inside the first file that opens, keep "server_name 127.0.0.1" for localhost/docker. Change to  "server_name {{public-ip-address}}" or "server_name {{domain name}}" if running on a VPS.

3. Make the mysql password this: y78tyutftret. Keep entering it every time it asks. When asked, turn off all test accounts and test databases. When asked, install phpmyadmin as an apache server. 

4. Upon completion to http://{{server_name}}/phpmyadmin. user "root" password "y78tyutftret". Click "Databases". Create a database called "forge" with collection "utf8_unicode_ci". 

5. Run cd /var/www/laravel/; php artisan migrate

6. Open AuthServiceProvider.php and import the following classes.
```php
use Illuminate\Auth\Access\Gate;use Illuminate\Contracts\Auth\Access\Gate as GateContract;use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
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

### Adding SSL to VPS

1. First edit the "installCerts.sh" command to provide your domain and email. The domain name must be connected to your VPS's IP address. 

2. Run ./installCerts.sh from the cloned directory.

3. In the opened file, replace all instances of "poster.projectoblio.com" with your domain name. There are at least 4 occurrences, use a find function 

4. If you get an error, just run it a second time =)

## Post-Installation Modifications 
### Adding OAuth2.0 tokens for the API
1. Create an account called admin@projectoblio.com.

2. Assign it "Advanced" type account.

3. You will now be able to add OAuth2.0 priviledges at the bottom of the left sidebar. Use the tokens it generates in your secondary site

4. In laravel/public/oauth.php, update the api domain name with your domain

### Adding Google Auth login, recaptcha tokens, Twilio APIs...
1. Check the ./laravel/.env file for Google Auth and Recaptcha tokens. Google Auth ("sign-in with gmail!") is currently disabled but can be re-enabled as needbe. 

2. Twilio API key is located where? 

