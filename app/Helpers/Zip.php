<?php

namespace App\Helpers;

class Zip
{
    public function create($source, $destination)
    {
        try {
            $zip = new \ZipArchive;
            $zip->open($destination, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
            $path = str_replace('\\', '/', realpath($source));
            // Extracting folder name with pathinfo
            $folder_name = pathinfo($destination, PATHINFO_FILENAME);

            if (is_dir($path) === true) {
                $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path), \RecursiveIteratorIterator::SELF_FIRST);
                foreach ($files as $name => $file) {
                    if (! $file->isDir()) {
                        $filePath = $file->getRealPath();
                        $zip->addFile($filePath, $folder_name.'/'.pathinfo($filePath, PATHINFO_BASENAME));
                    }
                }
            }
            $zip->close();

            return true;
        } catch (\Exception $e) {
            return false;
        }

    }
}

?>    