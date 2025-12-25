<?php

namespace App\Services;

use Illuminate\Support\Facades\File;

class ImageService
{
    public function upload($file, $productName): string
    {
        $path = public_path('images');
        $name = $productName . '.' . $file->extension();
        File::makeDirectory($path, $mode = 0777, true, true);
        $file->move($path, $name);
        return basename($path . '/' . $name);
    }
    public function delete($file): true
    {
        $path = public_path('images');
        File::delete($path . '/' . $file);
        return true;
    }
}
