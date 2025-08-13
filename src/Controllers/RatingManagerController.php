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
                ->filter($request->only(['keyword'])) 
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
            $label = ucfirst($rating->status);
            $btnClass = '';
            $tooltip = '';
            
            switch($rating->status) {
                case 'approved':
                    $btnClass = 'btn-success';
                    $tooltip = 'Rating is approved';
                    break;
                case 'rejected':
                    $btnClass = 'btn-danger';
                    $tooltip = 'Rating is rejected';
                    break;
                case 'pending':
                    $btnClass = 'btn-warning';
                    $tooltip = 'Rating is pending';
                    break;
                default:
                    $btnClass = 'btn-secondary';
                    $tooltip = 'Rating status';
            }

            $strHtml = '<button type="button" class="btn ' . $btnClass . ' btn-sm status-btn" data-id="' . $rating->id . '" data-status="' . $rating->status . '" data-url="' . route('admin.ratings.updateStatus') . '">' . $label . '</button>';

            return response()->json(['success' => true, 'message' => 'Status updated to ' . $label, 'strHtml' => $strHtml]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete record.', 'error' => $e->getMessage()], 500);
        }
    }
}
