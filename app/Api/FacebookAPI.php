<?php
namespace App\Api;

use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\FacebookResponse;
use Facebook\FacebookSDKException;
use Facebook\FacebookRequestException;
use Facebook\FacebookAuthorizationException;
use Facebook\GraphObject;
use Facebook\Entities\AccessToken;
use Facebook\HttpClients\FacebookCurlHttpClient;
use Facebook\HttpClients\FacebookHttpable;

class FacebookAPI
{
    private static $session;

    const FACEBOOK_APP_ID = '1561883147377016';
    const FACEBOOK_APP_SECRET = '3f84181a1a7ddfd507015d846d78b9ca';

    const SCOPE_PUBLIC_PROFILE = 'public_profile';
    const SCOPE_READ_STREAM = 'read_stream';
    const SCOPE_USER_LIKES = 'user_likes';
    const SCOPE_EMAIL = 'email';

    public static function init() {
        FacebookSession::setDefaultApplication(self::FACEBOOK_APP_ID, self::FACEBOOK_APP_SECRET);
    }

    /**
     * @param $url
     *
     * @return \Facebook\FacebookRedirectLoginHelper
     */
    public static function getRedirect($url = NULL){
        self::init();
        $helper = new FacebookRedirectLoginHelper($url);
        return $helper;
    }


    /**
     * @return FacebookSession
     */
    public static function getSession()
    {
        if(!is_null(self::$session)) {
            return self::$session;
        }
        return self::$session = FacebookSession::newAppSession(self::FACEBOOK_APP_ID, self::FACEBOOK_APP_SECRET);
    }

    public static function execute($method = 'GET', $uri)
    {
        try {
            $response =  (new FacebookRequest(self::getSession(), $method, $uri))
                ->execute()
                ->getGraphObject();
        } catch(FacebookRequestException $e) {
            echo "Exception occured, code: " . $e->getCode();
            echo " with message: " . $e->getMessage();
            return null;
        }

        return $response;
    }
} 