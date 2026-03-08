<?php

namespace App\Http\Controllers;

use App\Models\StockMovement;
use Illuminate\Http\Request;

class StockMovementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'branch_id' => 'required|exists:branches,id',
            'type' => 'required|in:in,out',
            'quantity' => 'required|integer|min:1'
        ]);

        $stock = Stock::firstOrCreate(
            [
                'product_id' => $request->product_id,
                'branch_id' => $request->branch_id,
                'company_id' => auth()->user()->company_id,
            ],
            ['quantity' => 0]
        );

        if ($request->type === 'out' && $stock->quantity < $request->quantity) {
            return response()->json([
                'message' => 'Insufficient stock'
            ], 400);
        }

        $movement = StockMovement::create([
            'stock_id' => $stock->id,
            'type' => $request->type,
            'quantity' => $request->quantity,
            'user_id' => auth()->id(),
        ]);

        if ($request->type === 'in') {
            $stock->quantity += $request->quantity;
        } else {
            $stock->quantity -= $request->quantity;
        }

        $stock->save();

        return response()->json($movement, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(StockMovement $stockMovement)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StockMovement $stockMovement)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StockMovement $stockMovement)
    {
        //
    }
}
