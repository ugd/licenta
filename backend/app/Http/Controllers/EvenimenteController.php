<?php

namespace App\Http\Controllers;

use App\Models\Bilete;
use App\Models\Evenimente;
use App\Models\Intrari;
use App\Models\Invitatii;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Spatie\PdfToImage\Pdf;

class EvenimenteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Evenimente::orderBy('id', 'desc')->get();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $request->validate([
            'nume_eveniment' => 'required|string|max:255',
            'locatie' => 'required|string|max:255',
            'data_inceput' => 'required|date|before:data_sfarsit|max:255',
            'data_sfarsit' => 'required|date|after:data_inceput|max:255',
            'location_lat' => 'required|string|max:255',
            'location_long' => 'required|string|max:255',
            'invitation_background' => 'required|string|max:255',
            'appleWalletBackground' => 'required|string|max:255',
            'appleWalletThumbnail' => 'required|string|max:255',
            'appleWalletLogo' => 'required|string|max:255',
            'appleWalletIcon' => 'required|string|max:255',
            'main_act' => 'required|string|max:255',
            'performers' => 'required|array|max:255',
            'event_edition' => 'required|string|max:255',
            'event_location' => 'required|string|max:255',
            'venue_entrance' => 'required|string|max:255',
            'venue_room' => 'required|string|max:255',
            'uuid' => 'required|uuid|max:255',
        ]);

        $event_day_only = Carbon::parse($request->data_inceput)->format('d.m.Y');

        $eveniment = Evenimente::create([
            'nume_eveniment' => $request->nume_eveniment,
            'locatie' => $request->locatie,
            'data_inceput' => $request->data_inceput,
            'data_sfarsit' => $request->data_sfarsit,
            'location_lat' => $request->location_lat,
            'location_long' => $request->location_long,
            'invitation_background' => $request->invitation_background,
            'apple_wallet_background' => $request->appleWalletBackground,
            'apple_wallet_thumbnail' => $request->appleWalletThumbnail,
            'apple_wallet_logo' => $request->appleWalletLogo,
            'apple_wallet_icon' => $request->appleWalletIcon,
            'main_act' => $request->main_act,
            'event_day_only' => $event_day_only,
            'performers' => $request->performers,
            'event_edition' => $request->event_edition,
            'event_location' => $request->event_location,
            'venue_entrance' => $request->venue_entrance,
            'venue_room' => $request->venue_room,
            'uuid' => $request->uuid,
        ]);

        return response()->json(['eveniment' => $eveniment], 200);
    }

    /**
     * Generate UUID
     */
    public function generateUUID()
    {
        $uuid = Str::uuid();

        return response()->json(['uuid' => $uuid], 200);
    }

    /**
     * Upload files to event
     */
    public function uploadFilesToEvent(Request $request)
    {
        $request->validate([
            'file' => 'required|file',
            'container' => 'required|string|max:255',
            'uuid' => 'required|string',
        ]);

        $file = $request->file('file');
        $container = $request->container;
        $uuid = $request->uuid;

        $eventAssetsDirectory = 'eventAssets/' . $uuid;
        $appleWalletPath = $eventAssetsDirectory . '/apple_wallet';
        $invitationBackgroundPath = $eventAssetsDirectory . '/invitation_background';

        Storage::makeDirectory($appleWalletPath, 0775, true);
        Storage::makeDirectory($invitationBackgroundPath, 0775, true);

        $fileNameMapping = [
            'appleWalletLogo' => 'logo@2x.png',
            'appleWalletThumbnail' => 'thumbnail@2x.png',
            'appleWalletIcon' => 'icon@3x.png',
            'appleWalletBackground' => 'background@2x.png',
        ];

        if (array_key_exists($container, $fileNameMapping)) {
            $fileName = $fileNameMapping[$container];
            $filePath = $appleWalletPath;
        } else {
            $originalFileName = $file->getClientOriginalName();
            $fileName = Str::uuid() . '-' . $originalFileName;
            $filePath = $container === 'invitation_background' ? $invitationBackgroundPath : 'assets/' . $container;
        }

        $file->storeAs($filePath, $fileName, 'public');

        $internalPath = '/' . $filePath . '/' . $fileName;
        $fullPath = $filePath . '/' . $fileName;

        if ($file->extension() === 'pdf') {
            $pdfPath = storage_path('app/public' . $internalPath);
            $pngPath = storage_path('app/public/' . $filePath . '/' . Str::before($fileName, '.') . '.png');
            $pdf = new Pdf($pdfPath);
            $pdf->setOutputFormat('png')->saveImage($pngPath);

            return response()->json([
                'url' => asset(Storage::url($filePath . '/' . Str::before($fileName, '.') . '.png')),
                'internalPath' => $internalPath,
                'fileName' => Str::before($fileName, '.') . '.png',
            ], 200);
        } else {
            $image = Image::make($file)->orientate();
            $image->save(storage_path('app/public/' . $fullPath), 90);

            if ($container === 'appleWalletIcon') {
                $this->downsizeResize($file, 3, $fullPath);
                $this->downsizeResize($file, 1.5, $fullPath);
            } else {
                $this->downsizeResize($file, 2, $fullPath);
            }

            return response()->json([
                'url' => asset(Storage::url($fullPath)),
                'internalPath' => '/' . $filePath,
                'fileName' => $fileName,
            ], 200);
        }
    }

    /**
     * Downsize and resize the image
     */
    private function downsizeResize($file, $factor, $fullPath)
    {
        $newImg = Image::make($file)->orientate();
        $width = $newImg->width() / $factor;
        $height = $newImg->height() / $factor;
        switch ($factor) {
            case 3:
                $newImg->resize($width, $height)->save(storage_path('app/public' . Str::replaceLast('@3x', '', '/' . $fullPath)), 90);
                break;
            case 2:
                $newImg->resize($width, $height)->save(storage_path('app/public' . Str::replaceLast('@2x', '', '/' . $fullPath)), 90);
                break;
            case 1.5:
                $newImg->resize($width, $height)->save(storage_path('app/public' . Str::replaceLast('@3x', '@2x', '/' . $fullPath)), 90);
                break;
            default:
                break;
        }
    }

    /**
     * Display the specified resource.
     */
    public function getStatistics()
    {
        $evenimente = Evenimente::orderBy('id', 'desc')->get();
        $statistics = [];

        foreach ($evenimente as $eveniment) {
            $dataInceput = Carbon::parse($eveniment->data_inceput)->subHour();
            $dataSfarsit = Carbon::parse($eveniment->data_sfarsit)->addHour();
            $evenimentId = $eveniment->id;
            $evenimentNume = $eveniment->nume_eveniment;

            $hourlyRanges = $this->generateHourlyRanges($dataInceput, $dataSfarsit);
            $evenimentStatistics = [];
            $totalInvitatii = 0;
            $totalIntrari = 0;
            $totalBilete = 0;

            foreach ($hourlyRanges as $rangeLabel => $rangeTimes) {
                $rangeStart = Carbon::parse($rangeTimes['start']);
                $rangeEnd = Carbon::parse($rangeTimes['end']);

                $countInvitatii = Invitatii::where('eveniment_id', $evenimentId)
                    ->whereBetween('scan_time', [$rangeStart, $rangeEnd])
                    ->count();
                $countIntrari = Intrari::where('eveniment_id', $evenimentId)
                    ->whereBetween('scan_time', [$rangeStart, $rangeEnd])
                    ->count();
                $countBilete = Bilete::where('eveniment_id', $evenimentId)
                    ->whereBetween('scan_time', [$rangeStart, $rangeEnd])
                    ->count();

                $evenimentStatistics[$rangeLabel] = [
                    'invitatii' => $countInvitatii,
                    'intrari' => $countIntrari,
                    'bilete' => $countBilete,
                ];

                $totalInvitatii += $countInvitatii;
                $totalIntrari += $countIntrari;
                $totalBilete += $countBilete;
            }
            $totalGeneral = $totalInvitatii + $totalIntrari + $totalBilete;
            $totalCountInvitatii = Invitatii::where('eveniment_id', $evenimentId)->count();
            $totalCountBilete = Bilete::where('eveniment_id', $evenimentId)->count();

            $percentInvitatii = $totalCountInvitatii ? round(($totalInvitatii / $totalCountInvitatii) * 100, 2) : 0;
            $percentBilete = $totalCountBilete ? round(($totalBilete / $totalCountBilete) * 100, 2) : 0;
            $percentGeneral = ($totalCountInvitatii + $totalIntrari + $totalCountBilete) ? round(($totalGeneral / ($totalCountInvitatii + $totalIntrari + $totalCountBilete)) * 100, 2) : 0;

            $statistics[] = [
                'nume_eveniment' => $evenimentNume,
                'id' => $evenimentId,
                'statistici' => $evenimentStatistics,
                'totaluri' => [
                    'total_invitatii' => "{$totalInvitatii}/{$totalCountInvitatii} ({$percentInvitatii}%)",
                    'total_intrari' => "{$totalIntrari}",
                    'total_bilete_alte_canale' => "{$totalBilete}/{$totalCountBilete} ({$percentBilete}%)",
                    'total_general' => "{$totalGeneral}/" . ($totalCountInvitatii + $totalIntrari + $totalCountBilete) . " ({$percentGeneral}%)",
                ],
            ];
        }

        return response()->json(['statistics' => $statistics], 200);
    }

    /**
     * Display the specified resource.
     */
    private function generateHourlyRanges($dataInceput, $dataSfarsit)
    {
        $ranges = [];
        $currentHour = $dataInceput->copy()->startOfHour();
        $endHour = $dataSfarsit->copy()->startOfHour();

        while ($currentHour->lessThanOrEqualTo($endHour)) {
            $label = $currentHour->format('H') . '-' . $currentHour->copy()->addHour()->format('H');
            $ranges[$label] = [
                'start' => $currentHour->toDateTimeString(),
                'end' => $currentHour->copy()->addHour()->subSecond()->toDateTimeString(),
            ];
            $currentHour->addHour();
        }

        return $ranges;
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        if (!Evenimente::where('id', $id)->exists()) {
            return response()->json(['message' => 'Evenimentul nu există.'], 404);
        }

        return response()->json(Evenimente::find($id), 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nume_eveniment' => 'required|string|max:255',
            'locatie' => 'required|string|max:255',
            'data_inceput' => 'required|string|max:255',
            'data_sfarsit' => 'required|string|max:255',
            'location_lat' => 'required|string|max:255',
            'location_long' => 'required|string|max:255',
            'invitation_background' => 'string|max:255',
            'appleWalletBackground' => 'string|max:255',
            'appleWalletThumbnail' => 'string|max:255',
            'appleWalletLogo' => 'string|max:255',
            'appleWalletIcon' => 'string|max:255',
            'main_act' => 'required|string|max:255',
            'performers' => 'required|array|max:255',
            'event_edition' => 'required|string|max:255',
            'event_location' => 'required|string|max:255',
            'venue_entrance' => 'required|string|max:255',
            'venue_room' => 'required|string|max:255',
            'uuid' => 'required|uuid|max:255',
        ]);

        if (!Evenimente::where('id', $id)->exists()) {
            return response()->json(['message' => 'Evenimentul nu există.'], 404);
        }

        $event_day_only = Carbon::parse($request->data_inceput)->format('d.m.Y');

        $eveniment = Evenimente::where('id', $request->id)->update([
            'nume_eveniment' => $request->nume_eveniment,
            'locatie' => $request->locatie,
            'data_inceput' => $request->data_inceput,
            'data_sfarsit' => $request->data_sfarsit,
            'location_lat' => $request->location_lat,
            'location_long' => $request->location_long,
            'invitation_background' => $request->invitation_background,
            'apple_wallet_background' => $request->appleWalletBackground,
            'apple_wallet_thumbnail' => $request->appleWalletThumbnail,
            'apple_wallet_logo' => $request->appleWalletLogo,
            'apple_wallet_icon' => $request->appleWalletIcon,
            'main_act' => $request->main_act,
            'event_day_only' => $event_day_only,
            'performers' => $request->performers,
            'event_edition' => $request->event_edition,
            'event_location' => $request->event_location,
            'venue_entrance' => $request->venue_entrance,
            'venue_room' => $request->venue_room,
            'uuid' => $request->uuid,
        ]);

        return response()->json(['eveniment' => $eveniment], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        if (!Evenimente::where('id', $id)->exists()) {
            return response()->json(['message' => 'Evenimentul nu există.'], 404);
        }
        $eveniment = Evenimente::find($id);
        $eveniment->delete();

        return response()->json(['message' => 'Evenimentul a fost șters.'], 200);
    }
}
