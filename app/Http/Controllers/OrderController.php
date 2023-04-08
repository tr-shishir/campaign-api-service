<?php
namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $orders = $user->orders()->get();
        return response()->json(['orders' => $orders]);
    }

    public function getCollection(Campaign $campaign)
    {
        $totalQuantity = $campaign->orders()->sum('quantity');
        $collectionPercentage = $campaign->stock_quantity > 0 ? ($totalQuantity / $campaign->stock_quantity) * 100 : 0;

        return response()->json([
            'total_quantity' => $totalQuantity,
            'collection_percentage' => $collectionPercentage,
        ]);
    }

    public function store(Request $request, Campaign $campaign)
    {
        $user = Auth::user();

        $stock_quantity = $campaign->stock_quantity;

        $requested_quantity = $request->input('quantity');
        if ($requested_quantity <= 0 || $requested_quantity > $stock_quantity) {
            return response()->json(['error' => 'Invalid quantity'], 400);
        }

        $order = new Order();
        $order->quantity = $requested_quantity;
        $order->campaign_id = $campaign->id;
        $order->user_id = $user->id;
        $order->save();

        $campaign->stock_quantity = $stock_quantity - $requested_quantity;
        $campaign->save();

        return response()->json(['order' => $order]);
    }

    public function update(Request $request, Campaign $campaign, Order $order)
    {
        $user = Auth::user();

        if ($order->user_id != $user->id || $campaign->status == 'Completed') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $stock_quantity = $campaign->stock_quantity;

        $requested_quantity = $request->input('quantity');
        $quantity_decrease = $request->input('decrease');

        if ($requested_quantity <= 0 || $requested_quantity > $stock_quantity) {
            return response()->json(['error' => 'Invalid quantity'], 400);
        }

        if(!$quantity_decrease){
            $order->quantity += $requested_quantity;
            $campaign->stock_quantity = $stock_quantity - $requested_quantity;
        }else{
            $order->quantity -= $requested_quantity;
            $campaign->stock_quantity = $stock_quantity + $requested_quantity;
        }

        $order->save();
        $campaign->save();

        return response()->json(['order' => $order]);
    }

    public function destroy(Campaign $campaign, Order $order)
    {
        $user = Auth::user();

        if ($order->user_id != $user->id || $campaign->status == 'Completed') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $campaign->stock_quantity += $order->quantity;
        $campaign->save();
        $order->delete();

        return response()->json(['message' => 'Order deleted successfully']);
    }
}
