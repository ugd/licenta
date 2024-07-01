<?php

namespace App\Http\Controllers;

use App\Imports\BileteImport;
use App\Models\Bilete;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class BileteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bilete = Bilete::join('evenimentes', 'evenimentes.id', '=', 'biletes.eveniment_id')
            ->join('staris', 'staris.id', '=', 'biletes.stare_id')
            ->join('vendori_biletes', 'vendori_biletes.id', '=', 'biletes.vendor_id')
            ->select('biletes.*', 'evenimentes.nume_eveniment as eveniment', 'staris.nume_stare as stare', 'vendori_biletes.nume_vendor as vendor')
            ->orderBy('biletes.id', 'desc')
            ->get();

        return response()->json(
            $bilete,
            200
        );
    }

    /**
     * Store a newly batch created resource in storage.
     */
    public function storeBatch(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,xls,xlsx',
            'eveniment_id' => 'required|int|max:255',
            'vendor_id' => 'required|int|max:255',
        ]);

        Excel::import(new BileteImport($request->eveniment_id, $request->vendor_id), $request->file('file'));

        return response()->json(['message' => 'Fisierul a fost incarcat cu succes!'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $bilet = Bilete::findOrFail($id);
        $bilet->stare_id = 4;
        $bilet->save();

        return response()->json(['message' => 'Biletul a fost anulat cu succes!'], 200);
    }
}
