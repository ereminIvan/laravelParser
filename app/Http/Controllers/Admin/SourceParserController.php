<?php
/**
 * @author Eremin Ivan
 * @email coding.ebola@gmail.com
 */
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

use App\Models\ParserSource;
use App\Models\ParserNews;
use Illuminate\View\View;
use League\Flysystem\Exception;
use PhpParser\Parser;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SourceParserController extends AdminController
{
    /**
     * @var $layout View
     */
    protected $layout = 'layouts.panel';

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->beforeFilter('auth');
    }

    /**
     * @return View
     */
    public function sourceList()
    {
        return view('admin.sections.social-parser.source-list', ['sources' => ParserSource::all()]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function sourceAdd(Request $request)
    {
        if($request->isMethod('POST') && $request->ajax()) {
            $this->validate($request, [
                'sourceId'          => 'integer',
                'sourceType'        => 'required|in:twitter,facebook,rss',
                'sourceUri'         => 'required',
                'sourceKeywords'    => 'required',//|regex:/([\w\d\s\t]*);?/i',
                'sourceActive'      => 'in:on,NULL',
            ]);

            $data = [
                'type'      => $request->input('sourceType'),
                'uri'       => $request->input('sourceUri'),
                'keywords'  => $request->input('sourceKeywords'),
                'is_active' => (int) (bool) $request->input('sourceActive'),
            ];

            if($id = $request->input('sourceId')) {
                /** @var ParserSource $source */
                if($source = ParserSource::find($id)) {
                    $source->update($data);
                };
            } else {
                $data['user_id'] = \Auth::user()->id;
                $source = ParserSource::create($data);
            }

            return response()->json([
                'success'   => true,
                'id'        => $source->id,
            ]);
        }
        return response();
    }

    /**
     * @return View
     */
    public function newsList()
    {
        return view('admin.sections.social-parser.news-list', [
            'news' => ParserNews::where('is_archived', 0)->get()
        ]);
    }

    /**
     * @return View
     */
    public function newsArchiveList()
    {
        return view('admin.sections.social-parser.news-list', [
            'news' => ParserNews::where('is_archived', 1)->get()
        ]);
    }

    /**
     * @param $id
     *
     * @return View
     */
    public function news($id)
    {
        /** @var ParserNews $news */
        $news = ParserNews::find($id);
        if(!$news->user_id) {
            $news->user_id = \Auth::user()->id;
            $news->viewed_at = date('Y-m-d H:i:s');
            $news->is_viewed = 1;
            $news->save();
        }
        return view('admin.sections.social-parser.news', [
            'news'  => ParserNews::find($id)
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function newsToggleArchive(Request $request)
    {
        if($request->isMethod('POST') && $request->ajax()) {
            $this->validate($request, [
                'id'        => 'integer',
                'archive'   => 'in:1,0',
            ]);
            /** @var ParserNews $news */
            $news = ParserNews::find(\Input::get('id'));
            $news->is_archived = \Input::get('archive');
            $news->save();
            return response()->json([
                'success'   => true,
            ]);
        }
        return response();
    }
}