<?php


namespace App\AngryLadder;

use Illuminate\Http\Request;
use \League\OAuth2\Client\Provider\GenericProvider AS OAuth;
use Cache;

class Slack
{

    public function authorize( $request )
    {
        // https://github.com/thephpleague/oauth2-client

        session_start();

        $slackClientID = env('SLACK_CLIENT_ID');
        $slackClientSecret = env('SLACK_CLIENT_SECRET');

        if( empty($slackClientID) || empty($slackClientSecret) )
        {
            die( 'Slack client ID or client secret not configured');
        }

        //$provider = new \League\OAuth2\Client\Provider\GenericProvider([
        $provider = new OAuth([
            'clientId'                => $slackClientID,    // The client ID assigned to you by the provider
            'clientSecret'            => $slackClientSecret,   // The client password assigned to you by the provider
            'redirectUri'             => 'http://api.angryladder.dev/slack/authorize',
            'urlAuthorize'            => 'https://slack.com/oauth/authorize',
            'urlAccessToken'          => 'https://slack.com/api/oauth.access',
            'urlResourceOwnerDetails' => 'https://slack.com/api/users.list',
            'scopes'                  => 'users:read'
        ]);


        //$oauth2state = $request->session()->get('oauth2state');
        //$oauth2state = Cache::store('file')->get('oauth2state');

        // If we don't have an authorization code then get one
        if (!isset($_GET['code'])) {

            // Fetch the authorization URL from the provider; this returns the
            // urlAuthorize option and generates and applies any necessary parameters
            // (e.g. state).
            $authorizationUrl = $provider->getAuthorizationUrl();

            // Get the state generated for you and store it to the session.

            //$value = Cache::store('file')->put('oauth2state', $provider->getState(), 60);
            $_SESSION['oauth2state'] = $provider->getState();

            // Redirect the user to the authorization URL.
            header('Location: ' . $authorizationUrl);
            exit;

            // Check given state against previously stored one to mitigate CSRF attack
        //} elseif (empty($_GET['state']) || ($_GET['state'] !== $oauth2state)) {
        } elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {

            //Cache::store('file')->forget('oauth2state');
            unset($_SESSION['oauth2state']);
            exit('Invalid state');

        } else {

            try {

                // Try to get an access token using the authorization code grant.
                $accessToken = $provider->getAccessToken('authorization_code', [
                    'code' => $_GET['code']
                ]);

                // We have an access token, which we may use in authenticated
                // requests against the service provider's API.

                $token = $accessToken->getToken();

                if( !empty( $token ))
                {
                    Cache::store('file')->forever('slackAPIToken', $token);
                    echo 'token:'.$token . "\n";
                }

                echo 'token:'.$token . "\n";
                //echo 'Refresh Token:'.$accessToken->getRefreshToken() . "\n";
                //echo $accessToken->getExpires() . "\n";
                //echo ($accessToken->hasExpired() ? 'expired' : 'not expired') . "\n";

                // Using the access token, we may look up details about the
                // resource owner.
                $resourceOwner = $provider->getResourceOwner($accessToken);

                var_export($resourceOwner->toArray());


                // The provider provides a way to get an authenticated API request for
                // the service, using the access token; it returns an object conforming
                // to Psr\Http\Message\RequestInterface.
                /*
                $request = $provider->getAuthenticatedRequest(
                    'GET',
                    'http://brentertainment.com/oauth2/lockdin/resource',
                    $accessToken
                );
                */

            } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {

                // Failed to get the access token or user details.
                exit($e->getMessage());

            }

        }


    }


    public function getAccessToken(  )
    {




    }

    public function getToken(  )
    {




    }

}
