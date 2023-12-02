<?php

namespace App\Helpers;

use Exception;
use Illuminate\Support\Facades\Storage;

class StorageHelper
{

    /** 
     * DirPath(string) adalah base direktori file, lihat pada Config > constant.php
     * file(string) adalah nama file yang akan di hapus di storage
     * newFile(file) adalah file yang akan disimpan
    */
    public static function updateFile($dirPath, $file, $newFile)
    {
        try {
            if ($file != null) {
                self::deleteFile($dirPath, $file);
            }
    
            $path = self::saveFile($dirPath, $newFile);
            return $path;
        } catch (Exception $err) {
            throw $err;
        }
    }

    public static function saveFile($dirPath, $file)
    {
        try {
            return Storage::putFile($dirPath, $file);
        } catch (Exception $err) {
            throw $err;
        }
    }

    /** 
     * path file that want to save must be following format below
     * images/path/file_name.extension
    */
    public static function deleteFile($dirPath, $fileName)
    {
        try {
            $tmp = explode($dirPath, $fileName);
            $storagePath = $dirPath . end($tmp);
            if (Storage::disk()->exists($storagePath)) {
                Storage::delete($storagePath);
            } else {
                throw new Exception("File ($fileName) doesn't exist");
            }
        } catch (Exception $err) {
            throw $err;
        }
    }

    public static function getFileUrl($path)
    {
        if (Storage::disk()->exists($path)) {
            return url(Storage::url($path));
        } else {
            return url('images/image_not_found.jpg');
        }
    }

    /** ------------------------------------------------------------
     * Multiple Files
     * --------------------------------------------------------------
    */

    /** 
     * DirPath(string) adalah base direktori file, lihat pada Config > constant.php
     * files(array of fileName) adalah multiple nama file yang akan di hapus di storage
     * newFiles(array of file) adalah multiple file yang akan disimpan
    */
    public static function updateFiles($dirPath, $files, $newFiles)
    {
        try {
            if ($files != null) self::deleteFiles($dirPath, $files);
            $tmpImages = self::saveFiles($dirPath, $newFiles);
            return $tmpImages;
        } catch (Exception $err) {
            throw $err;
        }
    }

    public static function deleteFiles($dirPath, $files)
    {
        foreach ($files as $file) {
            self::deleteFile($dirPath, $file);
        }
    }

    public static function saveFiles($dirPath, $newFiles)
    {
        $pathFiles = [];
        foreach ($newFiles as $file) {
            $pathFiles[] = self::saveFile($dirPath, $file);
        }
        return $pathFiles;
    }

    /** ------------------------------------------------------------
     * Web
     * --------------------------------------------------------------
    */

    // adjust catch err for web
    public static function saveFileWeb($dirPath, $file)
    {
        try {
            return Storage::putFile($dirPath, $file);
        } catch (Exception $err) {
            throw $err;
        }
    }
}
