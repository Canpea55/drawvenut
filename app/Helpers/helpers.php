<?php

use Carbon\Carbon;
use App\Models\Favorite;

if (!function_exists('calculateDuration')) {
    function calculateDuration($start, $end)
    {
        $diff = Carbon::parse($start)->diff(Carbon::parse($end));
        $output = "{$diff->y} ปี {$diff->m} เดือน {$diff->d} วัน {$diff->h} ชั่วโมง {$diff->i} นาที {$diff->s} วินาที";
        if($diff->y <= 0)
        {
            $output = "{$diff->m} เดือน {$diff->d} วัน";
            if($diff->m <= 0)
            {
                $output = "{$diff->d} วัน {$diff->h} ชั่วโมง {$diff->i} นาที";
                if($diff->d <= 0)
                {
                    $output = "{$diff->h} ชั่วโมง {$diff->i} นาที";
                    if($diff->h <= 0)
                    {
                        $output = "{$diff->i} นาที";
                        if($diff->s <= 0)
                        {
                            $output = "{$diff->s} วินาที";
                        }
                    }
                }
            }
        }
        return $output;
    }
}

if (!function_exists('convertTimezone')) {
    function convertTimezone($datetime, $timezone = 'Asia/Bangkok', $format = 'd-m-Y H:i:s')
    {
        if (!$datetime) {
            return null;
        }

        $dt = \Carbon\Carbon::parse($datetime)->timezone($timezone);
        return $dt->format($format);
    }
}

if(!function_exists('favoritesCount')) {
    function favoritesCount(){
        $user_id = Auth::id();
        if(!$user_id){
            return 0;
        }
        return Favorite::where('user_id', $user_id)->count();
    }
}
if(!function_exists('isFavorited')) {
    function isFavorited($product_id){
        $user_id = Auth::id();
        $favorite = Favorite::where([
            'user_id' => $user_id,
            'product_id' => $product_id,
        ])->first();
        if($favorite){
            return true;
        }
        return false;
    }
}
