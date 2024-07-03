<?php

namespace App\Http\Controllers;

use App\DTO\EventInfoDTO;
use App\Imports\InvitatiiImport;
use App\Models\Evenimente;
use App\Models\Invitatii;
use App\Models\InviteType;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use setasign\Fpdi\Fpdi;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Thenextweb\PassGenerator;

class InvitatiiController extends Controller
{
    public function index()
    {
        $invitatii = Invitatii::join('staris', 'invitatiis.stare_id', '=', 'staris.id')
            ->join('invite_types', 'invitatiis.invite_type_id', '=', 'invite_types.id')
            ->join('evenimentes', 'invitatiis.eveniment_id', '=', 'evenimentes.id')
            ->select('invitatiis.*', 'staris.nume_stare as stare', 'invite_types.invite_type as tip_invitatie', 'evenimentes.nume_eveniment as eveniment')
            ->orderBy('invitatiis.id', 'desc')
            ->get();

        return response()->json($invitatii, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nume' => 'required|string|max:255',
            'prenume' => 'required|string|max:255',
            'adresa_email' => 'required|string|max:255',
            'telefon' => 'nullable|string|max:255',
            'eveniment_id' => 'required|int',
            'invite_type_id' => 'required|int',
        ]);

        $code = strtoupper(Str::random(6));

        while (Invitatii::where('cod_invitatie', $code)->exists()) {
            $code = strtoupper(Str::random(6));
        }

        $invitatii = new Invitatii();
        $invitatii->nume = $request->nume;
        $invitatii->prenume = $request->prenume;
        $invitatii->adresa_email = $request->adresa_email;
        $invitatii->telefon = $request->telefon;
        $invitatii->eveniment_id = $request->eveniment_id;
        $invitatii->invite_type_id = $request->invite_type_id;
        $invitatii->cod_invitatie = $code;
        $invitatii->stare_id = 1;
        $invitatii->save();

        return response()->json(['message' => 'Invitatia a fost creata cu succes!'], 200);
    }

    public function storeBatch(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,xls,xlsx',
            'eveniment_id' => 'required|int',
        ]);

        Excel::import(new InvitatiiImport($request->eveniment_id), $request->file('file'));

        return response()->json(['message' => 'Fisierul a fost incarcat cu succes!'], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nume' => 'required|string|max:255',
            'prenume' => 'required|string|max:255',
            'adresa_email' => 'required|string|max:255',
            'telefon' => 'nullable|string|max:255',
            'eveniment_id' => 'required|int',
            'invite_type_id' => 'required|int',
        ]);

        $invitatii = Invitatii::findOrFail($id);
        $invitatii->nume = $request->nume;
        $invitatii->prenume = $request->prenume;
        $invitatii->adresa_email = $request->adresa_email;
        $invitatii->telefon = $request->telefon;
        $invitatii->eveniment_id = $request->eveniment_id;
        $invitatii->invite_type_id = $request->invite_type_id;
        $invitatii->save();

        return response()->json(['message' => 'Invitatia a fost actualizata cu succes!'], 200);
    }

    /**
     * Invalidate the specified resource from storage.
     */
    public function destroy($id)
    {
        $invitatii = Invitatii::findOrFail($id);
        $invitatii->stare_id = 4;
        $invitatii->save();

        return response()->json(['message' => 'Invitatia a fost anulata cu succes!'], 200);
    }

    private function generatePdfPass($code, $prenume, $nume, $invitation_background)
    {
        $width = 1688;
        $height = 638;
        $pdf = new Fpdi();

        $template = public_path('/storage' . $invitation_background);
        $font1 = 'gui.php';
        $font2 = 'mm.php';
        $pdf->setSourceFile($template);

        $tplIdx = $pdf->importPage(1);

        $pdf->AddPage('L', [$width, $height]);
        $pdf->useTemplate($tplIdx, 0, 0, $width, $height);
        $image_saved = QrCode::format('png')->size(150)->generate($code, public_path('/invitatii-export/qrpng/' . $code . '.png'));
        $pdf->AddFont('GothamUltra-Italic', '', $font1);
        $pdf->AddFont('Montserrat-Medium', '', $font2);
        $image = public_path('/invitatii-export/qrpng/' . $code . '.png');

        $h = 150;
        $w = 150;
        $x = 964;
        $y = 398;
        $pdf->Image($image, $x, $y, $w, $h);

        $pdf->SetFont('GothamUltra-Italic', '', 150);
        $pdf->SetXY(92, 235);
        $pdf->Write(0, $prenume . ' ' . $nume);

        $pdf->SetFont('GothamUltra-Italic', '', 100);
        $pdf->SetXY(92, 309);
        $pdf->Write(0, 'General Access');

        $pdf->SetFont('Montserrat-Medium', '', 70);
        $pdf->SetXY(964, 130);
        $pdf->Write(0, '#' . strtoupper($code));

        $pdf->Output(public_path('/invitatii-export/pdf/' . $code . '.pdf'), 'F');

        unlink(public_path('/invitatii-export/qrpng/' . $code . '.png'));

        return '/invitatii-export/pdf/' . $code . '.pdf';
    }

    private function generateApplePass(EventInfoDTO $eventInfoDTO)
    {
        $lat = $eventInfoDTO->lat;
        $long = $eventInfoDTO->long;
        $guest_name = $eventInfoDTO->guest_name;
        $event_name = $eventInfoDTO->event_name;
        $event_day_only = $eventInfoDTO->event_day_only;
        $event_date = $eventInfoDTO->event_date;
        $event_end_date = $eventInfoDTO->event_end_date;
        $venue_name = $eventInfoDTO->venue_name;
        $venue_entrance = $eventInfoDTO->venue_entrance;
        $venue_room = $eventInfoDTO->venue_room;
        $event_location = $eventInfoDTO->event_location;
        $event_edition = $eventInfoDTO->event_edition;
        $ticket_number = $eventInfoDTO->ticket_number;
        $invite_type = $eventInfoDTO->invite_type;
        $performers = $eventInfoDTO->performers;
        $main_act = $eventInfoDTO->main_act;
        $apple_wallet_background = $eventInfoDTO->apple_wallet_background;
        $apple_wallet_thumbnail = $eventInfoDTO->apple_wallet_thumbnail;
        $apple_wallet_logo = $eventInfoDTO->apple_wallet_logo;
        $apple_wallet_icon = $eventInfoDTO->apple_wallet_icon;

        $pass_identifier = $ticket_number;

        $pass = new PassGenerator($pass_identifier, true);

        $pass_definition = [
            'description' => env('PASS_DESCRIPTION'),
            'formatVersion' => 1,
            'organizationName' => env('PASS_ORGANIZATION_NAME'),
            'passTypeIdentifier' => env('PASS_TYPE_IDENTIFIER'),
            'teamIdentifier' => env('PASS_TEAM_IDENTIFIER'),
            'serialNumber' => $ticket_number,
            'maxDistance' => (int) env('PASS_MAX_DISTANCE'),
            'foregroundColor' => 'rgb(230, 230, 230)',
            'backgroundColor' => 'rgb(20, 20, 20)',
            'labelColor' => 'rgb(255, 255, 255)',
            'relevantDate' => $event_date,
            'expirationDate' => $event_end_date,
            'semantics' => [
                'eventType' => 'PKEventTypeLivePerformance',
                'eventStartDate' => $event_date,
                'eventEndDate' => $event_end_date,
                'eventName' => $event_name,
                'genre' => 'Drum&Bass',
                'venueName' => $venue_name,
                'venueEntrance' => $venue_entrance,
                'venueRoom' => $venue_room,
                'venueLocation' => [
                    'latitude' => $lat,
                    'longitude' => $long,
                ],
                'location' => [
                    'latitude' => $lat,
                    'longitude' => $long,
                ],
                'performerNames' => $performers,
            ],
            'locations' => [
                [
                    'latitude' => $lat,
                    'longitude' => $long,
                    'relevantText' => 'Present this pass at the checkpoint along with your ID.',
                    'semantics' => [
                        'venueName' => $venue_name,
                        'venueEntrance' => $venue_entrance,
                        'venueRoom' => $venue_room,
                        'venueLocation' => [
                            'latitude' => $lat,
                            'longitude' => $long,
                        ],
                    ],
                ],
            ],

            'barcode' => [
                'message' => $ticket_number,
                'format' => 'PKBarcodeFormatPDF417',
                'altText' => $ticket_number,
                'messageEncoding' => 'iso-8859-1',
            ],
            'eventTicket' => [
                'headerFields' => [
                    [
                        'key' => 'eventDate',
                        'label' => 'Event Date',
                        'value' => $event_day_only,
                        'semantics' => [
                            'eventStartDate' => $event_date,
                            'eventEndDate' => $event_end_date,
                        ],
                    ],
                ],
                'primaryFields' => [
                    [
                        'key' => 'eventName',
                        'label' => 'Event',
                        'value' => $main_act,
                        'semantics' => [
                            'eventName' => $event_name,
                            'performerNames' => $performers,
                        ],
                    ],
                ],
                'secondaryFields' => [
                    [
                        'key' => 'location',
                        'label' => 'Location',
                        'value' => $venue_name,
                        'semantics' => [
                            'venueName' => $venue_name,
                            'venueEntrance' => $venue_entrance,
                            'venueRoom' => $venue_room,
                            'venueLocation' => [
                                'latitude' => $lat,
                                'longitude' => $long,
                            ],
                        ],
                    ],

                ],
                'auxiliaryFields' => [
                    [
                        'key' => 'name',
                        'label' => 'Name',
                        'value' => $guest_name,
                    ],
                    [
                        'key' => 'eventEdition',
                        'label' => 'Event Edition',
                        'value' => $event_edition,
                    ],
                ],
                'backFields' => [
                    [
                        'key' => 'ticketNumber',
                        'label' => 'Ticket Number',
                        'value' => $ticket_number,
                    ], [
                        'key' => 'name',
                        'label' => 'Name',
                        'value' => $guest_name,
                    ], [
                        'key' => 'inviteType',
                        'label' => 'Invitation Type',
                        'value' => $invite_type,
                    ],
                    [
                        'key' => 'eventLocationAddress',
                        'label' => 'Event Address',
                        'value' => $event_location,
                        'semantics' => [
                            'venueName' => $venue_name,
                            'venueLocation' => [
                                'latitude' => $lat,
                                'longitude' => $long,
                            ],
                        ],
                    ],
                ],

            ],
        ];

        $pass->setPassDefinition($pass_definition);

        if ($apple_wallet_background) {
            $pass->addAsset(public_path('/storage' . $apple_wallet_background . '/background@2x.png'));
            $pass->addAsset(public_path('/storage' . $apple_wallet_background . '/background.png'));
        } else {
            $pass->addAsset(base_path('resources/assets/wallet/background@2x.png'));
            $pass->addAsset(base_path('resources/assets/wallet/background.png'));
        }
        if ($apple_wallet_thumbnail) {
            $pass->addAsset(public_path('/storage' . $apple_wallet_thumbnail . '/thumbnail@2x.png'));
            $pass->addAsset(public_path('/storage' . $apple_wallet_thumbnail . '/thumbnail.png'));
        } else {
            $pass->addAsset(base_path('resources/assets/wallet/thumbnail@2x.png'));
            $pass->addAsset(base_path('resources/assets/wallet/thumbnail.png'));
        }
        if ($apple_wallet_logo) {
            $pass->addAsset(public_path('/storage' . $apple_wallet_logo . '/logo@2x.png'));
            $pass->addAsset(public_path('/storage' . $apple_wallet_logo . '/logo.png'));
        } else {
            $pass->addAsset(base_path('resources/assets/wallet/logo@2x.png'));
            $pass->addAsset(base_path('resources/assets/wallet/logo.png'));
        }
        if ($apple_wallet_icon) {
            $pass->addAsset(public_path('/storage' . $apple_wallet_icon . '/icon@3x.png'));
            $pass->addAsset(public_path('/storage' . $apple_wallet_icon . '/icon@2x.png'));
            $pass->addAsset(public_path('/storage' . $apple_wallet_icon . '/icon.png'));
        } else {
            $pass->addAsset(base_path('resources/assets/wallet/icon@3x.png'));
            $pass->addAsset(base_path('resources/assets/wallet/icon@2x.png'));
            $pass->addAsset(base_path('resources/assets/wallet/icon.png'));
        }

        $pkpass = $pass->create();

        Storage::disk('public')->put('passes/' . $ticket_number . '.pkpass', $pkpass);

        return $ticket_number . '.pkpass';
    }

    public function getEventNames()
    {
        $evenimente = Evenimente::orderBy('id', 'desc')->get(['id', 'nume_eveniment']);

        return response()->json($evenimente, 200);
    }

    public function getInviteTypes()
    {
        $invite_types = InviteType::get(['id', 'invite_type']);

        return response()->json($invite_types, 200);
    }

    public function sendMail(Request $request)
    {
        try {
            $request->validate(['invite_id' => 'required|int']);
            $invite = Invitatii::with('eveniment')->with('inviteType')->findOrFail($request->invite_id);
            $event = json_decode($invite->eveniment);
            $invite_type = json_decode($invite->inviteType);
            $data_inceput = Carbon::createFromFormat('Y-m-d H:i', $event->data_inceput)->toIso8601String();
            $data_sfarsit = Carbon::createFromFormat('Y-m-d H:i', $event->data_sfarsit)->toIso8601String();

            $pass_data = new EventInfoDTO([
                'lat' => $event->location_lat,
                'long' => $event->location_long,
                'guest_name' => $invite->prenume . ' ' . $invite->nume,
                'event_name' => $event->nume_eveniment,
                'event_day_only' => $event->event_day_only,
                'event_date' => $data_inceput,
                'event_end_date' => $data_sfarsit,
                'venue_name' => $event->locatie,
                'venue_entrance' => $event->venue_entrance,
                'venue_room' => $event->venue_room,
                'event_location' => $event->event_location,
                'event_edition' => $event->event_edition,
                'ticket_number' => $invite->cod_invitatie,
                'invite_type' => $invite_type->invite_type,
                'performers' => $event->performers,
                'main_act' => $event->main_act,
                'apple_wallet_background' => $event->apple_wallet_background,
                'apple_wallet_thumbnail' => $event->apple_wallet_thumbnail,
                'apple_wallet_logo' => $event->apple_wallet_logo,
                'apple_wallet_icon' => $event->apple_wallet_icon,
            ]);

            $invite_pdf_path = $this->generatePdfPass($invite->cod_invitatie, $invite->prenume, $invite->nume, $event->invitation_background);
            $apple_pass_path = $this->generateApplePass($pass_data);

            $this->callListMonkApi($invite->adresa_email, $event->nume_eveniment, $invite->prenume, $invite_pdf_path, $apple_pass_path);

            $invite->stare_id = 2;
            $invite->save();

            return response()->json(['message' => 'Invitatia a fost trimisa cu succes!'], 200);
        } catch (\Exception $e) {
            report($e->getMessage());

            return response()->json(['message' => 'A aparut o eroare la trimiterea invitatiei!'], 500);
        }
    }

    public function sendBatchMail(Request $request)
    {
        try {
            $request->validate(['invite_ids' => 'required|array']);
            $invites = Invitatii::with('eveniment')->with('inviteType')->whereIn('id', $request->invite_ids)->get();

            foreach ($invites as $invite) {
                $event = json_decode($invite->eveniment);
                $invite_type = json_decode($invite->inviteType);
                $data_inceput = Carbon::createFromFormat('Y-m-d H:i', $invite->eveniment->data_inceput)->toIso8601String();
                $data_sfarsit = Carbon::createFromFormat('Y-m-d H:i', $invite->eveniment->data_sfarsit)->toIso8601String();

                $pass_data = new EventInfoDTO([
                    'lat' => $event->location_lat,
                    'long' => $event->location_long,
                    'guest_name' => $invite->prenume . ' ' . $invite->nume,
                    'event_name' => $event->nume_eveniment,
                    'event_day_only' => $event->event_day_only,
                    'event_date' => $data_inceput,
                    'event_end_date' => $data_sfarsit,
                    'venue_name' => $event->locatie,
                    'venue_entrance' => $event->venue_entrance,
                    'venue_room' => $event->venue_room,
                    'event_location' => $event->event_location,
                    'event_edition' => $event->event_edition,
                    'ticket_number' => $invite->cod_invitatie,
                    'invite_type' => $invite_type->invite_type,
                    'performers' => $event->performers,
                    'main_act' => $event->main_act,
                    'apple_wallet_background' => $event->apple_wallet_background,
                    'apple_wallet_thumbnail' => $event->apple_wallet_thumbnail,
                    'apple_wallet_logo' => $event->apple_wallet_logo,
                    'apple_wallet_icon' => $event->apple_wallet_icon,
                ]);

                $invite_pdf_path = $this->generatePdfPass($invite->cod_invitatie, $invite->prenume, $invite->nume, $event->invitation_background);
                $apple_pass_path = $this->generateApplePass($pass_data);

                $this->callListMonkApi($invite->adresa_email, $event->nume_eveniment, $invite->prenume, $invite_pdf_path, $apple_pass_path);

                $invite->stare_id = 2;
                $invite->save();
            }

            return response()->json(['message' => 'Invitatiile au fost trimise cu succes!'], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json(['message' => 'A aparut o eroare la trimiterea invitatiilor.'], 500);
        }
    }

    protected function callListMonkApi($email, $event_name, $invitation_name, $file, $apple_pass_url)
    {
        $client = new Client();
        $body = [
            'recipient_email' => $email,
            'template_id' => (int) env('LISTMONK_TEMPLATE_ID'),
            'data' => [
                'eventName' => $event_name,
                'name' => $invitation_name,
                'applePassUrl' => env('APP_API_URL') . 'api/apple-pass/' . $apple_pass_url,
            ],
            'content_type' => 'html',
        ];
        try {
            $response = $client->request('POST', env('LISTMONK_API_BASE_URL') . '/tx/external', [
                'auth' => [env('LISTMONK_API_USERNAME'), env('LISTMONK_API_PASSWORD')],
                RequestOptions::MULTIPART => [
                    [
                        'name' => 'file',
                        'contents' => file_get_contents(public_path($file)),
                        'filename' => 'Your-Invitation-Pass.pdf',
                    ],
                    [
                        'name' => 'data',
                        'contents' => json_encode($body),
                    ],
                ],
            ]);
            $responseData = json_decode($response->getBody()->getContents(), true);

            return $responseData;
        } catch (\Exception $e) {
            report($e->getMessage());

            return response()->json(['message' => 'A aparut o eroare la trimiterea invitatiei!'], 500);
        }
    }
}
