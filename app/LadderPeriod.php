<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
//use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class LadderPeriod extends Model
{
    protected $fillable = array('active', 'period_start', 'period_end');

    public static function getTime( $time )
    {
        if( (int) $time === 0 ) {
            return time();
        }
        return (int) $time;
    }

    public static function getCurrentStartDate( $ladder, $currentTime = 0 )
    {
        $currentTime = self::getTime( $currentTime );

        if( !isset($ladder['period']) )
        {
            return null;
        }

        switch( $ladder['period'] )
        {
            case 'alltime':
                return date('Y-m-d', 0);
                break;
            case 'weekly':

                return self::getWeeklyCurrentStartDate( $ladder );
                break;
            case 'monthly':

                return self::getMonthlyCurrentStartDate( $ladder );
                break;
        }
    }
    public static function getNextEndDate( $ladder, $currentTime = 0 )
    {
        $currentTime = self::getTime( $currentTime );

        if( !isset($ladder['period']) )
        {
            return null;
        }

        switch( $ladder['period'] )
        {
            case 'alltime':
                return '9999-01-01 00:00:00';
                break;
            case 'weekly':

                return self::getWeeklyNextEndDate( $ladder );
                break;
            case 'monthly':

                return self::getMonthlyNextEndDate( $ladder );
                break;
        }
    }




    public static function getWeeklyCurrentStartDate( $ladder, $currentTime = 0 )
    {
        $currentTime = self::getTime( $currentTime );

        if( date( 'N' ) >= $ladder['reset_day'] )
        {
            echo 'last monday' . ($ladder['reset_day'] > 1 ? ' + ' . $ladder['reset_day'] -1 . ' days' : '');
            //echo date("M-d-y", strtotime('last monday', strtotime('next week', time())));
            $time = strtotime('last monday' . ($ladder['reset_day'] > 1 ? ' + ' . $ladder['reset_day'] -1 . ' days' : '') );
            //$time = strtotime('last week + '.$ladder['reset_day'].' days');
        }
        else {
            echo 'next monday' . ($ladder['reset_day'] > 1 ? ' + ' . $ladder['reset_day'] -1 . ' days' : '');
            //echo date("M-d-y", strtotime('last monday', strtotime('next week', time())));
            $time = strtotime('next monday' . ($ladder['reset_day'] > 1 ? ' + ' . $ladder['reset_day'] -1 . ' days' : '') );
            /*
            $time = strtotime( '+'. $ladder['reset_day'] - date( 'w' ).' days' );
            $time = strtotime( '+'. $ladder['reset_day'] - date( 'w' ).' days' );
            */
        }

        echo PHP_EOL;

        return date('Y-m-d', $time).' '.$ladder['reset_time'];
    }

    public static function getWeeklyNextEndDate( $ladder, $currentTime = 0 )
    {
        $currentTime = self::getTime( $currentTime );

        if( date( 'w' ) >= $ladder['reset_day'] )
        {
            echo 'next monday' .($ladder['reset_day'] > 1 ? ' + ' . $ladder['reset_day'] -1 . ' days' : '');
            $time = strtotime('next week'.($ladder['reset_day'] > 1 ? ' + ' . $ladder['reset_day'] -1 . ' days' : ''), $currentTime );
        }
        else {
            echo 'last sunday +'. $ladder['reset_day'] .' days';
            $time = strtotime( 'last sunday +'. $ladder['reset_day'] .' days', $currentTime );
        }

        echo PHP_EOL;
        return date('Y-m-d', $time).' '.$ladder['reset_time'];
    }

    public static function getMonthlyCurrentStartDate( $ladder, $currentTime = 0 )
    {
        $currentTime = self::getTime( $currentTime );

        if( date( 'j' ) >= (int) $ladder['reset_day'] )
        {
            $time = mktime(0, 0, 0, date('n', $currentTime), $ladder['reset_day'], date('Y', $currentTime) );
        }
        else {
            $time = mktime(0, 0, 0, date('n', $currentTime) - 1, $ladder['reset_day'], date('Y', $currentTime) );
        }

        return date('Y-m-d', $time).' '.$ladder['reset_time'];
    }
    public static function getMonthlyNextEndDate( $ladder, $currentTime = 0 )
    {
        $currentTime = self::getTime( $currentTime );

        if( date( 'j' ) >= (int) $ladder['reset_day'] )
        {
            $time = mktime(0, 0, 0, date('n', $currentTime) + 1, $ladder['reset_day'], date('Y', $currentTime));
        }
        else {
            $time = strtotime( '+'. $ladder['reset_day'] - date( 'j' ).' days' );
        }

        return date('Y-m-d', $time).' '.$ladder['reset_time'];
    }

    public static function compareTime( $ladder )
    {
        $currentTime = explode(':', date('h:i:s'));

        $sumTime = explode(':', $ladder['reset_time']);

        if( $currentTime < $sumTime )
        {

        }

    }


    public static function getCurrent( $ladder )
    {


    }

    public function game()
    {
        return $this->belongsTo('App\Match');
    }
}
