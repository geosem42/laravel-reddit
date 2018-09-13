<p align="center">
<img src="http://i.imgur.com/tHNVDSX.png">
</p>

# Plebbit: a reddit clone made in Laravel

Plebbit is a reddit clone build in Laravel 5.5. <br>

## Features
- Subreddits
- Moderation only deleting (more moderation soon... I think)
- Youtube, vimeo, image and video nicely displayed
- Searching global and in subplebbits
- Customizig of your subplebbits (images, custom CSS and more)
- Upvoting and downvoting
- Alerts
- Private messaging
- And a lot more...just see for yourself

## Still needs to be added
- More moderation.
- Need to make a fancier front-end.
- You tell me...

## Requirements
- PHP 7
- Composer [get composer here](http://getcomposer.org)
- Sql database compatible with Laravel
- Google captcha keys [Get captcha keys](https://www.google.com/recaptcha/intro/invisible.html)

## Installation
Pretty straight forward if you're familiar with Laravel but see the instructions below if you're not familiar with the workflow.

```
git clone https://github.com/Michael-J-Scofield/plebbit.git
```
make sure you are in the project folder then run
```
composer install
```
create a .env file and copy the contents from .env.example and modify it with your info.
```
php artisan migrate
```
That's it! plebbit should now be up and running!

Feel free to contribute to plebbit by submitting issues or creating pull requests.
