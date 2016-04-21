<?php
namespace Cms\Helpers;

use Cache;
use Config;
use Cms\Models\Extension;
use Cms\Models\Content\ContentImage;

class Cms
{
    public static function render($blocks, $name)
    {
        if (!isset($blocks[$name])) return '<div id="'.$name.'"></div>';
        return '<div id="'.$name.'">'.$blocks[$name].'</div>';
    }


    public static function extension($type, $key)
    {
        $cacheKey = Config::get('site_id') . ':extension:' . $type . ':' . $key;

        if (!Cache::has($cacheKey)) {
            $ext = Extension::where('type', $type)
                ->where('key', $key)
                ->firstOrFail();


            Cache::put($cacheKey, $ext->value, 5);

            return $ext->value;
        }

        return Cache::get($cacheKey);
    }

    public static function image($image, $name = 'default') {

        if (!$image) return '';

        $contentImage = new ContentImage();
        return $contentImage->render(['image' => $image, 'name' => $name]);
    }
}