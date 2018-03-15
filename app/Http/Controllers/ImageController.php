<?php

namespace App\Http\Controllers;

use App\Image;

class ImageController extends Controller
{
    /**
     * Gets a random image from the database.
     *
     * @return  Illuminate\Contracts\Routing\ResponseFactory
     */
    public function random()
    {
        return $this->buildResponse(Image::inRandomOrder()->limit(1)->first());
    }

    /**
     * Gets the image matching the given file name, or
     * a 404 response if the image was not found.
     *
     * @param  string  $file
     * @return Illuminate\Contracts\Routing\ResponseFactory
     */
    public function show($file)
    {
        return $this->buildResponse(Image::where('file', $file)->first());
    }

    /**
     * Builds the json response data for the given image object.
     *
     * @param  App\Image  $image
     * @return Illuminate\Contracts\Routing\ResponseFactory
     */
    protected function buildResponse($image)
    {
        if ($image == null) {
            return response()->json([
                'status' => 404,
                'reason' => 'Image was not found'
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'data'   => $image
        ], 200);
    }
}
