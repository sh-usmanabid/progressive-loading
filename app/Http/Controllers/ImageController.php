<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ImageController extends Controller
{
    public function lowRes($filename)
    {   
        try {
            $lowResPath = storage_path("app/public/images/low_res_{$filename}");

            if (file_exists($lowResPath)) {
                return response()->json($lowResPath);
            }

            $manager = new ImageManager(new Driver());

            $path = storage_path("app/public/images/{$filename}");

            if (!file_exists($path)) {
                abort(404);
            }

            $image = $manager->read($path)->resize(100, 100, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            $quality = 20;
            $image->save($lowResPath, $quality);

            return response()->json($lowResPath);
        } catch (Exception $e) {
            return response()->json(['error' => 'An error occurred while processing the image: ' . $e->getMessage()], 500);
        }
    }
}
