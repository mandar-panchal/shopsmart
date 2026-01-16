<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CampaignDetail;
use DataTables;

class CampaignDetailController extends Controller
{
    // Display a listing of the resource.
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = CampaignDetail::with('marketingSource')->get();
            return DataTables::of($data)
                ->addColumn('action', function ($row) {
                    $btn = '<a href="'.route('campaign-details.show', $row->id).'" class="btn btn-info btn-sm">View</a>';
                    $btn .= ' <a href="'.route('campaign-details.edit', $row->id).'" class="btn btn-primary btn-sm">Edit</a>';
                    $btn .= ' <form action="'.route('campaign-details.destroy', $row->id).'" method="POST" style="display:inline;">
                                 '.csrf_field().'
                                 '.method_field('DELETE').'
                                 <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Are you sure?\')">Delete</button>
                             </form>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('campaign-details.index');
    }

    public function create()
    {
        
    }

    public function store(Request $request)
    {
       
    }


    public function show($id)
    {
        
    }


    public function edit($id)
    {
        
    }

    public function update(Request $request, $id)
    {
       
    }

    public function destroy($id)
    {
      
    }
}
