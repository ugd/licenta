<?php

namespace App\Http\Controllers;

use App\Models\VendoriBilete;

class VendoriBileteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(
            VendoriBilete::all(),
            200
        );
    }
}
