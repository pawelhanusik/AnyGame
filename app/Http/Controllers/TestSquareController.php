<?php

namespace App\Http\Controllers;

use App\Models\TestSquare;
use Illuminate\Http\Request;

class TestSquareController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return inertia('TestSquare/Index', [
            'squares' => TestSquare::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TestSquare  $testSquare
     * @return \Illuminate\Http\Response
     */
    public function show(TestSquare $testSquare)
    {
        return inertia('TestSquare/Show', [
            'square' => $testSquare,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TestSquare  $testSquare
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TestSquare $testSquare)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TestSquare  $testSquare
     * @return \Illuminate\Http\Response
     */
    public function destroy(TestSquare $testSquare)
    {
        //
    }
}
