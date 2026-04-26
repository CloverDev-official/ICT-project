<?php
namespace App\Helpers;


class DownloadFile
{
    // helper generic stream download
    public static function download($contentType, $filename, $callback)
    {
        return response()->streamDownload(function () use ($callback) {
            echo $callback();
        }, $filename, [
            'Content-Type' => $contentType,
        ]);
    }
}