<?
# taken from wordpress
# https://developer.wordpress.org/reference/functions/human_time_diff/

function _n( $single, $plural, $number, $domain = 'default' )
{
    if ($number == 1) return $single;
    return $plural;
}

/*
 * Constants for expressing human-readable intervals
 * in their respective number of seconds.
 *
 * Please note that these values are approximate and are provided for convenience.
 * For example, MONTH_IN_SECONDS wrongly assumes every month has 30 days and
 * YEAR_IN_SECONDS does not take leap years into account.
 *
 * If you need more accuracy please consider using the DateTime class (https://www.php.net/manual/en/class.datetime.php).
 *
 * @since 3.5.0
 * @since 4.4.0 Introduced `MONTH_IN_SECONDS`.
 */
define( 'MINUTE_IN_SECONDS', 60 );
define( 'HOUR_IN_SECONDS', 60 * MINUTE_IN_SECONDS );
define( 'DAY_IN_SECONDS', 24 * HOUR_IN_SECONDS );
define( 'WEEK_IN_SECONDS', 7 * DAY_IN_SECONDS );
define( 'MONTH_IN_SECONDS', 30 * DAY_IN_SECONDS );
define( 'YEAR_IN_SECONDS', 365 * DAY_IN_SECONDS );

function human_time_diff( $from, $to = 0 ) {
    if ( empty( $to ) ) {
        $to = time();
    }

    $diff = (int) abs( $to - $from );

    if ( $diff < MINUTE_IN_SECONDS ) {
        $secs = $diff;
        if ( $secs <= 1 ) {
            $secs = 1;
        }
        /* translators: Time difference between two dates, in seconds. %s: Number of seconds. */
        $since = sprintf( _n( '%s second', '%s seconds', $secs ), $secs );
    } elseif ( $diff < HOUR_IN_SECONDS && $diff >= MINUTE_IN_SECONDS ) {
        $mins = round( $diff / MINUTE_IN_SECONDS );
        if ( $mins <= 1 ) {
            $mins = 1;
        }
        /* translators: Time difference between two dates, in minutes (min=minute). %s: Number of minutes. */
        $since = sprintf( _n( '%s minute', '%s minutes', $mins ), $mins );
    } elseif ( $diff < DAY_IN_SECONDS && $diff >= HOUR_IN_SECONDS ) {
        $hours = round( $diff / HOUR_IN_SECONDS );
        if ( $hours <= 1 ) {
            $hours = 1;
        }
        /* translators: Time difference between two dates, in hours. %s: Number of hours. */
        $since = sprintf( _n( '%s hour', '%s hours', $hours ), $hours );
    } elseif ( $diff < WEEK_IN_SECONDS && $diff >= DAY_IN_SECONDS ) {
        $days = round( $diff / DAY_IN_SECONDS );
        if ( $days <= 1 ) {
            $days = 1;
        }
        /* translators: Time difference between two dates, in days. %s: Number of days. */
        $since = sprintf( _n( '%s day', '%s days', $days ), $days );
    } elseif ( $diff < MONTH_IN_SECONDS && $diff >= WEEK_IN_SECONDS ) {
        $weeks = round( $diff / WEEK_IN_SECONDS );
        if ( $weeks <= 1 ) {
            $weeks = 1;
        }
        /* translators: Time difference between two dates, in weeks. %s: Number of weeks. */
        $since = sprintf( _n( '%s week', '%s weeks', $weeks ), $weeks );
    } elseif ( $diff < YEAR_IN_SECONDS && $diff >= MONTH_IN_SECONDS ) {
        $months = round( $diff / MONTH_IN_SECONDS );
        if ( $months <= 1 ) {
            $months = 1;
        }
        /* translators: Time difference between two dates, in months. %s: Number of months. */
        $since = sprintf( _n( '%s month', '%s months', $months ), $months );
    } elseif ( $diff >= YEAR_IN_SECONDS ) {
        $years = round( $diff / YEAR_IN_SECONDS );
        if ( $years <= 1 ) {
            $years = 1;
        }
        /* translators: Time difference between two dates, in years. %s: Number of years. */
        $since = sprintf( _n( '%s year', '%s years', $years ), $years );
    }

    return $since;
}

// with birthday paradox:
// for 2^48 values probability of alias collision in set of 100k users is (1 - 99.9982236767)
function sh_key2alias($key)
{
    return hexdec(substr(hash("sha256", $key), 0, 12)); # 12 hex digits = 48 bit
}

