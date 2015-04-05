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
        $result = [];
//        Facebook Sample
//        $source = new \App\Api\Search\Parser\Source('facebook', '/fczenit/feed?fields=id,message,picture&limit=250', ['метро']);
//        RSS Sample
//        $source = new \App\Api\Search\Parser\Source('rss', 'http://ria.ru/export/rss2/world/index.xml', ['НБУ', 'России'], 100);
//        Twitter Sample
        $source = new \App\Api\Search\Parser\Source('twitter', '', ['финансистов', 'украинской']);
        $result = \App\Api\Search\ParserFactory::factory($source)->parse();
        print_r($result);
        die;
        return view('admin.sections.social-parser.add');
    }
}