<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Template;
use Illuminate\Support\Facades\Input;

class TemplateDashboardController extends Controller
{

    private $templates;

    public function __construct()
    {
        $templates = Template::all();
    }

    public function index()
    {
        $templates = Template::all();
        return view('pages.analytics.template.dashboard', compact('templates'));
    }

    public function create()
    {
        return view('pages.analytics.template.create_template');
    }

    /*
     *     public function show($id)
    {
        $analysis = $this->analysis->findOrFail($id);
        $analysis_result = null;

        if (!\Cache::has(Analysis::CACHE_KEY . $analysis->id))
            $analysis->refresh();
        $analysis_result = \Cache::get(Analysis::CACHE_KEY . $analysis->id);

        return view('pages.analytics.show', compact('analysis', 'analysis_result'));
    }
     */

    public function show($id)
    {
        $template = (new \App\Template)->findOrFail($id);

        if ($template != null) {
            return view('pages.analytics.template.show_template', compact('template'));
        }
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'query' => 'required',
        ]);

        $template = (new \App\Template)->findOrFail($id);

        $name = $request->input('name');
        $query = $request->input('query');

        if (!$template->update(['name' => $name,
            'query' => $query ])) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['The template has not been updated']);
        }

        $template->refresh();
        return redirect()->action('TemplateDashboardController@show', [$template['id']])
            ->with('success', 'The template has been updated');
    }


    public function destroy($id)
    {
        \Log::error("test");
        $template = \App\Template::find($id);
        $template->delete();

        //TODO: change route
        return redirect()->route('dashboard.index')
            ->with('success', 'Template verwijderd uit de database');
    }

}
