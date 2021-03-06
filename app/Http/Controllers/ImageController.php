<?php

namespace App\Http\Controllers;

use App\Image;

class ImageController extends Controller
{
    /**
     * Displays the welcome page, with a random image.
     *
     * @return  Illuminate\Contracts\Routing\ResponseFactory
     */
    public function index()
    {
        $image = Image::inRandomOrder()->limit(1)->first();

        // Caches the total amount of images for 24 hours
        $total = app('cache')->remember('total', 60 * 24, function () {
            return Image::count();
        });

        $size = app('cache')->remember('size', 60 * 24, function () {
            $size = Image::sum('size');
            $base = log($size) / log(1024);
            $suffix = array('', ' KB', ' MB', ' GB', ' TB')[floor($base)];

            return number_format(pow(1024, $base - floor($base)), 2) . $suffix;
        });

        return view('welcome', compact('image', 'total', 'size'));
    }

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
     * Gets up to 10 images that fits the given type, if there is
     * more than 10 images, the results will be paginated.
     *
     * @param  string  $type
     * @return Illuminate\Contracts\Routing\ResponseFactory
     */
    public function type($type)
    {
        $images =  Image::where('type', $type)->paginate(10)->toArray();

        return response()->json([
            'status' => 200,
            'data'   => $images['data'],
            '_paginate' => [
                'first_page_url' => $images['first_page_url'],
                'from'           => $images['from'],
                'last_page'      => $images['last_page'],
                'last_page_url'  => $images['last_page_url'],
                'next_page_url'  => $images['next_page_url'],
                'path'           => $images['path'],
                'per_page'       => $images['per_page'],
                'prev_page_url'  => $images['prev_page_url'],
                'to'             => $images['to'],
                'total'          => $images['total'],
            ],
        ], 200);
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
                'reason' => 'Image was not found',
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'data'   => $image,
        ], 200);
    }
}
