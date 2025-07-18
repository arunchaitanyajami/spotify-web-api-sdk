# PHP SDK For Spotify Web Api

<p>requires php >= 7.2</p>

- Integrated Pagination
- Automated Token Refresh
- Separate Services Files For All Api References
- Guzzle Requests

## Installation
composer require arunchaitanyajami/spotify-web-api-sdk

## Doesnt have token?

### Option 1 - Get access token with client credentials

```
use SpotifyWebApiSdk\SpotifyWebApiSdk;

try {
    $spotifyWebApi = new SpotifyWebApiSdk();
    $token_obj = $spotifyWebApi->getAccessTokenWithCredentials(
        'CLIENT_ID',
        'CLIENT_SECRET'
    );
    echo $token_obj->access_token;
    // echo $token_obj->token_type;
    // echo $token_obj->expires_in;
} catch(\SpotifyWebAPI\SpotifyWebAPIException $e) {
    echo $e->getMessage();
}
```

### Option 2 - Get access token with code authorization (recommended)
Before make requests you must add yours Redirect URIs to https://developer.spotify.com/dashboard

Get redirect url for code:
```
use SpotifyWebApiSdk\SpotifyWebApiSdk;

try {
    $spotifyWebApi = new SpotifyWebApiSdk([
        'clientId' => 'CLIENT_ID',
        'clientSecret' => 'CLIENT_SECRET',
    ]);

    $callBackUrl = 'http://yoursite.com/callback';
    $url = $spotifyWebApi->getUrlForCodeToken($callBackUrl);
    header("Location: {$url}");
} catch(\SpotifyWebAPI\SpotifyWebAPIException $e) {
    echo $e->getMessage();
}
```

After signup in spotify you will be redirected back to provided above callback url (http://yoursite.com/callback) with parameter **$_GET['code']** with the code that can get token with following command:
```
use SpotifyWebApiSdk\SpotifyWebApiSdk;

try {
    $spotifyWebApi = new SpotifyWebApiSdk();
    $tokens = $spotifyWebApi->getAccessTokenWithCode(
        'YOUR_CODE',
        'http://yoursite.com/callback'
    );
} catch(\SpotifyWebAPI\SpotifyWebAPIException $e) {
    echo $e->getMessage();
}
```

And you will receive array with *accessToken* and *refreshToken* in the example above **$tokens**.

### Access/Refresh Tokens
Spotify tokens are valid 1 hour. If your token is expired and you make a call, the sdk auto renew access token with provided refresh token in every query (as there is no safe place to automatically save it).

If you set $spotifyWebApi->returnNewTokenIfIsExpired(true); before your request calls, if access token is expired will be returned from the query, object with the new access_token, then you can save it in database and recall request with a fresh Access token. 
You can also generate access token with refresh token manually with
```
use SpotifyWebApiSdk\SpotifyWebApiSdk;

try {
    $spotifyWebApi = new SpotifyWebApiSdk([
        'clientId' => 'CLIENT_ID',
        'clientSecret' => 'CLIENT_SECRET',
        'accessToken' => $oldAccessToken,
        'refreshToken' => 'REFRESH_TOKEN',
    ]);
    $result = $spotifyWebApi->refreshAccessToken();
} catch(\SpotifyWebAPI\SpotifyWebAPIException $e) {
    echo $e->getMessage();
}
```

and save final expire timestamp with  time() + $result->expires_in. You can manualy generate new access token every time when saved in your database expired time is end.

### Suggestions

It is good practise to add ip of the api that you call in the hosts file in yours server os because Guzzle sometime cannot resolve the dns.

Can increase your execution time of scripts 
ini_set('max_execution_time', XXX); and set_time_limit(XXX);
