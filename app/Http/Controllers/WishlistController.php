<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function __construct(private Wishlist $wishlist)
    {
    }

    public function index()
    {
        return view('wishlist.index', ['items' => $this->wishlist->items()]);
    }

    public function toggle(Request $request)
    {
        $data = $request->validate(['product_id' => ['required', 'exists:products,id']]);
        $added = $this->wishlist->toggle((int) $data['product_id']);
        $name = Product::find($data['product_id'])->name;
        $message = $added ? "$name ajouté aux favoris." : "$name retiré des favoris.";

        if ($request->wantsJson()) {
            return response()->json([
                'added' => $added,
                'count' => $this->wishlist->count(),
                'message' => $message,
            ]);
        }

        return back()->with('status', $message);
    }

    public function remove(Request $request)
    {
        $this->wishlist->remove((int) $request->input('product_id'));

        return back()->with('status', 'Article retiré des favoris.');
    }
}
