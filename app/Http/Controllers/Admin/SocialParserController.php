<?php
/**
 * @author Eremin Ivan
 * @email coding.ebola@gmail.com
 */
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\ParserSource;

class SocialParserController extends AdminController
{
    /**
     * @var $layout \Illuminate\View\View
     */
    protected $layout = 'layouts.panel';

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
                'type'      => \Input::get('sourceType'),
                'uri'       => \Input::get('sourceUri'),
                'keywords'  => \Input::get('sourceKeywords'),
                'active'    => (int)(bool) \Input::get('sourceActive'),
            ];

            if($id = \Input::get('sourceId')) {
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
     * @return \Illuminate\View\View
     */
    public function sourceList()
    {
        return view('admin.sections.social-parser.source-list', ['sources' => ParserSource::all()]);
    }
}