<?php

namespace App\Http\Controllers;

use App\Models\Branche;
use Illuminate\Http\Request;

class BrancheController extends Controller
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
        Branche::create($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(Branche $branche)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Branche $branche)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Branche $branche)
    {
        //
    }
}
