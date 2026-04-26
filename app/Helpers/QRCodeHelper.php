<?php
namespace App\Helpers;

use Illuminate\Support\Facades\Crypt;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QRCodeHelper
{
    public static function generate($data)
    {
        return QrCode::format('png')->size(500)->margin(3)->generate($data);;
    }
}