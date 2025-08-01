<?php

namespace admin\ratings\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use admin\ratings\Models\Rating;

class RatingManagerController extends Controller
{

    public function __construct()
    {
        $this->middleware('admincan_permission:ratings_manager_list')->only(['index']);
        $this->middleware('admincan_permission:ratings_manager_create')->only(['create', 'store']);
        $this->middleware('admincan_permission:ratings_manager_edit')->only(['edit', 'update']);
        $this->middleware('admincan_permission:ratings_manager_view')->only(['show']);
        $this->middleware('admincan_permission:ratings_manager_delete')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $ratings = Rating::query()
                ->filter($request->query('keyword'))
                ->filterByStatus($request->query('status'))
                ->sortable()
                ->latest()
                ->paginate(Rating::getPerPageLimit())
                ->withQueryString();

            return view('rating::admin.index', compact('ratings'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to load ratings: ' . $e->getMessage());
        }
    }

    /**
     * show rating details
     */
    public function show(Rating $rating)
    {
        try {
            return view('rating::admin.show', compact('rating'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to load ratings: ' . $e->getMessage());
        }
    }

    public function destroy(Rating $rating)
    {
        try {
            $rating->delete();
            return response()->json(['success' => true, 'message' => 'Record deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete record.', 'error' => $e->getMessage()], 500);
        }
    }

    public function updateStatus(Request $request)
    {
        try {
            $rating = Rating::findOrFail($request->id);
            $rating->status = $request->status;
            $rating->save();

            // create status html dynamically        
            $dataStatus = $rating->status == '1' ? '0' : '1';
            $label = $rating->status == '1' ? 'Active' : 'InActive';
            $btnClass = $rating->status == '1' ? 'btn-success' : 'btn-warning';
            $tooltip = $rating->status == '1' ? 'Click to change status to inactive' : 'Click to change status to active';

            $strHtml = '<a href="javascript:void(0)"'
                . ' data-toggle="tooltip"'
                . ' data-placement="top"'
                . ' title="' . $tooltip . '"'
                . ' data-url="' . route('admin.ratings.updateStatus') . '"'
                . ' data-method="POST"'
                . ' data-status="' . $dataStatus . '"'
                . ' data-id="' . $rating->id . '"'
                . ' class="btn ' . $btnClass . ' btn-sm update-status">' . $label . '</a>';

            return response()->json(['success' => true, 'message' => 'Status updated to ' . $label, 'strHtml' => $strHtml]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete record.', 'error' => $e->getMessage()], 500);
        }
    }
}
