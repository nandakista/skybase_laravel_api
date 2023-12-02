<?php

namespace App\Helpers;

use Berkayk\OneSignal\OneSignalFacade;
use Exception;

class PushNotificationHelper
{
    # Full documentation :
    # https://github.com/berkayk/laravel-onesignal/blob/master/src/OneSignalClient.php#L523
    #

    const ICON_NAME = 'ic_stat_onesignal_default';
    const ICON_COLOR = 'E2212B';

    public static function sendToExternalId($id, $message, $data = null, $translation = null, $url = null, $buttons = null, $schedule = null, $headings = null)
    {
        try {
            $uid = self::getExternalUid($id);
            OneSignalFacade::setParam('small_icon', self::ICON_NAME)
                ->setParam('android_accent_color', self::ICON_COLOR)
                ->sendNotificationToExternalUser(
                    $message,
                    "$uid",
                    $url,
                    $data,
                    $buttons,
                    $schedule,
                    $headings,
                    $translation,
                );
        } catch (Exception $err) {
            throw new Exception($err->getMessage());
        }
    }

    public static function broadcast($message, $url = null, $data = null, $buttons = null, $schedule = null, $headings = null, $subtitle = null)
    {
        try {
            OneSignalFacade::setParam('small_icon', self::ICON_NAME)
                ->setParam('android_accent_color', self::ICON_COLOR)
                ->sendNotificationToSegment(
                    $message,
                    "Subscribed Users",
                    $url,
                    $data,
                    $buttons,
                    $schedule,
                    $headings,
                    $subtitle,
                );
        } catch (Exception $err) {
            throw new Exception($err->getMessage());
        }
    }

    public static function getExternalUid($id)
    {
        $uid = $id;
        if (env('APP_ENV') == 'local' || env('APP_ENV') == 'staging') {
            $uid = 'dev-' . $id;
        }

        return $uid;
    }
}
