<?php

return [
    'endpoint_standard' => 'https://www.reddit.com',
    'endpoint_oauth' => 'https://oauth.reddit.com',
    
    'username' => env('REDDIT_USERNAME'),
    'password' => env('REDDIT_PASSWORD'),
    'app_id' => env('REDDIT_APP_ID'),
    'app_secret' => env('REDDIT_APP_SECRET'),
    
    'response_format' => 'STD', // STD | ARRAY
    
    'scopes' => 'save,modposts,identity,edit,flair,history,modconfig,modflair,modlog,modposts,modwiki,mysubreddits,privatemessages,read,report,submit,subscribe,vote,wikiedit,wikiread'
];