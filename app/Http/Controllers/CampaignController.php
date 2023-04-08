<?php
namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CampaignController extends Controller
{
    public function index()
    {
        $campaigns = Campaign::with('user', 'orders')->get();
        return response()->json(['campaigns' => $campaigns]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $campaign = new Campaign();
        $campaign->name = $request->input('name');
        $campaign->status = 'Ongoing'; 
        $campaign->budget = $request->input('budget');
        $campaign->user_id = $user->id;
        $campaign->description = $request->input('description');
        $campaign->stock_quantity = $request->input('stock_quantity');
        $campaign->save();

        return response()->json(['campaign' => $campaign]);
    }

    public function show(Campaign $campaign)
    {
        $campaignDetails = $campaign::with('users', 'orders')->get();

        return response()->json(['campaign' => $campaignDetails]);
    }

    public function update(Request $request, Campaign $campaign)
    {

        $user = Auth::user();

        if ($campaign->user_id != $user->id) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $campaign->name = $request->input('name');
        $campaign->budget = $request->input('budget');
        $campaign->description = $request->input('description');
        $campaign->stock_quantity = $request->input('stock_quantity');
        $campaign->save();

        return response()->json(['campaign' => $campaign]);
    }

    public function destroy(Campaign $campaign)
    {
        $user = Auth::user();

        if ($campaign->user_id != $user->id) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $campaign->delete();

        return response()->json(['message' => 'Campaign deleted successfully']);
    }

    public function updateStatus(Request $request, Campaign $campaign)
    {

        $user = Auth::user();

        if ($campaign->user_id != $user->id) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $status = $request->input('status');
        if ($status != 'Ongoing' && $status != 'Completed') {
            return response()->json(['error' => 'Invalid status'], 400);
        }

        $campaign->status = $status;
        $campaign->save();

        return response()->json(['campaign' => $campaign]);
    }

    public function join(Campaign $campaign, Request $request)
    {
        $user = Auth::user();
        $requested_quantity = $request->input('quantity');
    
        $order = Order::where('campaign_id', $campaign->id)
                    ->where('user_id', $user->id)
                    ->where('quantity', $requested_quantity)
                    ->first();
    
        if (!$order) {
            return response()->json(['message' => 'Please place an order with the requested quantity before joining the campaign']);
        }

        if (!$campaign->users()->where('user_id', $user->id)->exists()) {
            $campaign->users()->attach($user);
            return response()->json(['message' => 'Successfully joined the campaign']);
        } else {
            return response()->json(['message' => 'Already joined the campaign']);
        }
    }

    public function leave(Campaign $campaign)
    {
        $user = Auth::user();

        if ($campaign->users()->where('user_id', $user->id)->exists()) {
            $campaign->users()->detach($user);
            return response()->json(['message' => 'Successfully left the campaign']);
        } else {
            return response()->json(['message' => 'Not joined the campaign']);
        }
    }

}
