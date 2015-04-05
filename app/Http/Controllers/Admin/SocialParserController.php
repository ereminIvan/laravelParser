<?php

namespace App\Http\Controllers\Admin;

use App;
use App\Api\FacebookAPI;
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

class SocialParserController extends AdminController
{
    /**
     * @var $layout \Illuminate\View\View
     */
    protected $layout = 'layouts.panel';

    public function addSource()
    {
        $source = new \App\Api\Search\Source('facebook', '/fczenit/feed?fields=id,message,picture&limit=250', ['one','two']);
        $parser = \App\Api\Search\ParserFactory::factory($source)->parse();
        return view('admin.sections.social-parser.add');
    }
}