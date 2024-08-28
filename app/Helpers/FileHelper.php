<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class FileHelper
{
    public static function deleteFileFromStorage($path): bool
    {
        return Storage::disk('public')->delete($path);
    }

    public static function saveFileToStorage($folder, $file): bool|string
    {
        try {
            $filename = self::makeUnixFilename() . '.' . $file->getClientOriginalExtension();
            $result = Storage::disk('public')->putFileAs($folder, $file, $filename);

            return $result ? $filename : false;
        } catch (\Exception $e) {
            LogHelper::writeErrorLog($e);
            return false;
        }
    }

    public static function makeUnixFilename(): string
    {
        return time() . '_' . md5(uniqid());
    }
}
