<?php

namespace App\Console\Commands;

use App\Image;
use Illuminate\Console\Command;
use Symfony\Component\Finder\Finder;

class SyncImagesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'image:sync';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Syncs the images located in the public/c/ folder to the database.';
    
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->output->write("\nLoading images from disk............................");
        $images = $this->loadImagesFromDisk();

        $this->output->write('Found "' . sizeof($images) . '" images, syncing images to the database...', false);
        $this->syncImagesToDatabase($images);

        app('cache')->forget('total');
        app('cache')->forget('size');
    }

    /**
     * Loads the images from disk.
     *
     * @return array
     */
    protected function loadImagesFromDisk()
    {
        $imageFolder = __DIR__ . '/../../../public/c/';

        $finder = new Finder();
        $finder->files()->in($imageFolder);

        $images = [];

        foreach ($finder as $file) {
            $parts     = explode('.', $file->getRelativePathname());
            $extension = mb_strtolower(end($parts));

            if (! in_array($extension, ['jpg', 'png', 'gif', 'gifv'])) {
                continue;
            }

            unset($parts[sizeof($parts) - 1]);

            $images[] = [
                'type' => $extension,
                'size' => filesize($file),
                'file' => implode('.', $parts)
            ];
        }

        $this->info('Done!');

        return $images;
    }

    /**
     * Syncs the given images to the database, truncating the table
     * first to clear out any images not existing in the array.
     *
     * @param  array  $images
     * @return void
     */
    protected function syncImagesToDatabase($images)
    {
        Image::query()->truncate();

        foreach ($images as $image) {
            Image::create($image);
        }

        $this->info('Done!');
    }
}
