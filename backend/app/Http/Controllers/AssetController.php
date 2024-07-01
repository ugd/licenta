<?php

namespace App\Http\Controllers;

use Thenextweb\PassGenerator;

class AssetController extends Controller
{
    public function getAsset($filename)
    {
        $path = storage_path('app/public/assets/'.$filename);

        if (! file_exists($path)) {
            return response()->json(['message' => 'File not found.'], 404);
        }

        return response()->file($path);
    }

    public function getApplePass($suffixPath)
    {
        $path = storage_path('app/public/passes/'.$suffixPath);

        if (! file_exists($path)) {
            return response()->json(['message' => 'File not found.'], 404);
        }

        $pkpass = file_get_contents($path);

        $headers = [
            'Content-Transfer-Encoding' => 'binary',
            'Content-Description' => 'File Transfer',
            'Content-Disposition' => 'attachment; filename="pass.pkpass"',
            'Content-Length' => strlen($pkpass),
            'Content-Type' => PassGenerator::getPassMimeType(),
            'Pragma' => 'no-cache',
        ];

        return response()->download($path, 'pass.pkpass', $headers);
    }
}
