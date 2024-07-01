<?php

namespace App\Http\Controllers;

use App\Models\Bilete;
use App\Models\Intrari;
use App\Models\Invitatii;
use App\Models\PermisiuniEveniment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IntrariController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function apiScan(Request $request)
    {
        if ($request->validate([
            'code' => 'required|string',
        ])) {
            $code = $request->input('code');
            if (strlen($code) == 6) {
                try {
                    $invitatii = Invitatii::where('cod_invitatie', $code)->firstOrFail();
                    if ($invitatii->stare_id == 2 || $invitatii->stare_id == 1) {
                        $lastState = $invitatii->stare_id;
                        $invitatii->stare_id = 3;
                        $invitatii->scan_time = now();
                        $invitatii->save();
                        $invitatii->lastState = $lastState;
                        $invitatii->stdName = $invitatii->nume . ' ' . $invitatii->prenume;
                        $invitatii->stdEmail = $invitatii->adresa_email;
                        $invitatii->stdCode = $invitatii->cod_invitatie;
                        $invitatii->vendor = 'Intern';

                        return response()->json([
                            'message' => 'Code is valid',
                            'data' => $invitatii,
                            'type' => 1,
                        ], 202);
                    } elseif ($invitatii->stare_id == 4) {
                        $lastState = $invitatii->stare_id;
                        $invitatii->lastState = $lastState;
                        $invitatii->stdName = $invitatii->nume . ' ' . $invitatii->prenume;
                        $invitatii->stdEmail = $invitatii->adresa_email;
                        $invitatii->stdCode = $invitatii->cod_invitatie;
                        $invitatii->vendor = 'Intern';

                        return response()->json([
                            'message' => 'Code is refused',
                            'data' => $invitatii,
                            'type' => 3,
                        ], 200);
                    } else {
                        $invitatii->stdName = $invitatii->nume . ' ' . $invitatii->prenume;
                        $invitatii->stdEmail = $invitatii->adresa_email;
                        $invitatii->stdCode = $invitatii->cod_invitatie;
                        $lastState = $invitatii->stare_id;
                        $invitatii->lastState = $lastState;
                        $invitatii->vendor = 'Intern';

                        return response()->json([
                            'message' => 'Code is already used',
                            'data' => $invitatii,
                            'type' => 3,
                        ], 200);
                    }
                } catch (\Exception $e) {
                    return response()->json([
                        'message' => 'Code is not valid in Bilete or Invitatii',
                        'data' => $e,
                        'type' => 4,
                    ], 200);
                }
            } else {
                try {
                    $bilete = Bilete::where('cod_bilet', $code)->firstOrFail();
                    if ($bilete->stare_id == 2 || $bilete->stare_id == 1) {
                        $lastState = $bilete->stare_id;
                        $vendor = json_decode($bilete->vendor_object);
                        $bilete->stare_id = 3;
                        $bilete->scan_time = now();
                        $bilete->save();
                        $bilete->stdName = $bilete->nume_prenume;
                        $bilete->stdEmail = $bilete->email;
                        $bilete->stdCode = $bilete->cod_bilet;
                        $bilete->lastState = $lastState;
                        $bilete->vendor = $vendor->nume_vendor;

                        return response()->json([
                            'message' => 'Code is valid',
                            'data' => $bilete,
                            'type' => 2,
                        ], 202);
                    } elseif ($bilete->stare_id == 4) {
                        $vendor = json_decode($bilete->vendor_object);
                        $bilete->stdName = $bilete->nume_prenume;
                        $bilete->stdEmail = $bilete->email;
                        $bilete->stdCode = $bilete->cod_bilet;
                        $lastState = $bilete->stare_id;
                        $bilete->lastState = $lastState;
                        $bilete->vendor = $vendor->nume_vendor;

                        return response()->json([
                            'message' => 'Code is refused',
                            'data' => $bilete,
                            'type' => 3,
                        ], 200);
                    } else {
                        $vendor = json_decode($bilete->vendor_object);
                        $bilete->stdName = $bilete->nume_prenume;
                        $bilete->stdEmail = $bilete->email;
                        $bilete->stdCode = $bilete->cod_bilet;
                        $lastState = $bilete->stare_id;
                        $bilete->lastState = $lastState;
                        $bilete->vendor = $vendor->nume_vendor;

                        return response()->json([
                            'message' => 'Code is already used',
                            'data' => $bilete,
                            'type' => 3,
                        ], 200);
                    }
                } catch (\Exception $e) {
                    return response()->json([
                        'message' => 'Code is not valid in Bilete or Invitatii',
                        'data' => $e,
                        'type' => 4,
                    ], 200);
                }
            }
        }
    }

    public function eventsAccess(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $authUser = Auth::user();

        $user = User::find($authUser->id);

        $events = $user->evenimente()->with('users')->get();

        return response()->json(['events' => $events], 200);
    }

    public function getEventsForUser(Request $request, $id)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $events = $user->evenimente()->with('users')->get();

        return response()->json(['events' => $events], 200);
    }

    public function updateEventPermissions(Request $request, $id)
    {

        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'event_ids' => 'array',
            'event_ids.*' => 'integer|exists:evenimentes,id',
        ]);

        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $existingEventIds = PermisiuniEveniment::where('utilizator_id', $id)->pluck('eveniment_id')->toArray();

        $eventIds = $request->input('event_ids');
        $eventsToAdd = array_diff($eventIds, $existingEventIds);
        $eventsToDelete = array_diff($existingEventIds, $eventIds);

        foreach ($eventsToAdd as $eventId) {
            PermisiuniEveniment::create([
                'utilizator_id' => $id,
                'eveniment_id' => $eventId,
            ]);
        }

        PermisiuniEveniment::where('utilizator_id', $id)
            ->whereIn('eveniment_id', $eventsToDelete)
            ->delete();

        return response()->json(['message' => 'Event permissions updated successfully'], 200);
    }

    public function addTicket(Request $request, $id)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $intrare = new Intrari();
        $intrare->eveniment_id = $id;
        $intrare->scan_time = now();

        $intrare->save();

        return response()->json(['intrare' => $intrare], 201);
    }

    public function index()
    {
        $intrari = Intrari::join('evenimentes', 'intraris.eveniment_id', '=', 'evenimentes.id')
            ->select('intraris.*', 'evenimentes.nume_eveniment as nume_eveniment')
            ->orderBy('intraris.id', 'desc')
            ->get();

        return response()->json($intrari, 200);
    }
}
