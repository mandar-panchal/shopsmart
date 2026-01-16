<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MarketingSource;

use DataTables;

class MarketingSourceController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = MarketingSource::select('*');

            return DataTables::of($data)
                ->addColumn('action', function ($row) {
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $row->id . '" data-original-title="Edit" data-bs-target="#updateSources" class="edit btn btn-primary btn-sm editMarketingSource">Edit</a>';
                    $btn .= '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->id . '" data-original-title="Delete" class="btn btn-danger btn-sm deleteMarketingSource" style="margin-left: 5px;">Delete</a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('marketing/marketingsources');
    }

    public function create()
    {
        return view('marketing_sources.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:150',
            'parent_branch' => 'exists:marketing_sources,id',
        ]);

        $marketingSource = MarketingSource::create($validatedData);

        return response()->json(['success' => 'Marketing Source added successfully']);
    }

    public function edit($id)
    {
        $marketingSource = MarketingSource::findOrFail($id);
        $parentBranches = MarketingSource::pluck('name', 'id');    
        return response()->json([
            'marketingSource' => $marketingSource,
            'parentBranches' => $parentBranches,
        ]);
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|exists:marketing_sources,id',
            'name' => 'required|string|max:150',
            'parent_branch' => 'required|exists:marketing_sources,id',
        ]);

        MarketingSource::findOrFail($request['id'])->update($validatedData);

        return response()->json(['success' => 'Marketing Source updated successfully']);
    }

    public function destroy(Request $request)
    {
        MarketingSource::findOrFail($request['id'])->delete();

        return response()->json(['success' => 'Marketing Source deleted successfully']);
    }
    public function TreeView()
    {
       $marketingSources = MarketingSource::all();
       return view('marketing/marketing_sources_tree', compact('marketingSources'));
    }    
    public function getParentBranches()
    {
        $parentBranches = MarketingSource::pluck('name', 'id');
        return response()->json($parentBranches);
    }

}
