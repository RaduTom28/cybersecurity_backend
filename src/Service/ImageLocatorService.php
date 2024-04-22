<?php

namespace App\Service;

use Symfony\Component\Finder\Finder;

class ImageLocatorService
{

    public function __construct(private readonly string $rootPath)
    {
    }

    public function locateImage(string $imageName)
    {
        $finder = new Finder();

        $finder->files()->in($this->rootPath."*")->name('*'.$imageName.'*');

        if ($finder->hasResults()) {
            $files=[];
            foreach ($finder as $file) {
                $files[] = ['name' => $file->getBasename(), 'content' => mb_convert_encoding($file->getContents(), 'UTF-8', 'UTF-8')];
            }
            
            return $files;
        }

        return [];
    }
}