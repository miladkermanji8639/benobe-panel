<?php

namespace App\Http\Controllers\Dr\Panel\NoskheElectronic\Favorite\Templates;

use Illuminate\Http\Request;
use App\Http\Controllers\Dr\Controller;

class FavoriteTemplatesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view("dr.panel.noskhe-electronic.favorite.templates.index");
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("dr.panel.noskhe-electronic.favorite.templates.create");

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
