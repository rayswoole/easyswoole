<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2019-01-24
 * Time: 23:24
 */

namespace EasySwoole\EasySwoole\Command;


use EasySwoole\Utility\File;

class Utility
{
    public static function easySwooleLog()
    {
        return <<<LOGO
\033[44m  _____     ___ __    __ _____                            _     _____   \e[0m
\033[44m |  _  \   /   |\ \  / //  ___/ _           _____  _____ | |   |  ___|  \e[0m
\033[44m | |_| |  / /| | \ \/ / | |___ | |  __   __/  _  \/  _  \| |   | |__    \e[0m
\033[44m |  _  / / /_| |  \  /  \___  \| | /  | / /| | | || | | || |   |  __|   \e[0m
\033[44m | | \ \/ /__| |  / /    ___| || |/   |/ / | |_| || |_| || |___| |___   \e[0m
\033[44m |_|_ \__/   |_| /_/    /_____/|___/|___/  \_____/\_____/|_____|_____|  \e[0m
\033[44m   | |    | |     | |     | |      | |       | |    | |    | |   | |    \e[0m
\033[44m __|_|____|_|_____|_|_____|_|______|_|_______|_|____|_|____|_|___|_|__  \e[0m
\033[44m|                                                                     | \e[0m
\033[44m|HIGH CONCURRENCY ASYNCHRONOUS COROUTINE FRAMEWORK BASED ON EASYSWOOLE| \e[0m
\033[44m|_____________________________________________________________________| \e[0m
\033[44m                                                                        \e[0m

LOGO;
    }

    static function displayItem($name, $value)
    {
        if($value === true){
            $value = 'true';
        }else if($value === false){
            $value = 'false';
        }else if($value === null){
            $value = 'null';
        }
        return "\e[32m" . str_pad($name, 30, ' ', STR_PAD_RIGHT) . "\e[34m" . $value . "\e[0m";
    }

    public static function releaseResource($source, $destination)
    {
        clearstatcache();
        $replace = true;
        if (is_file($destination)) {
            $filename = basename($destination);
            echo "{$filename} has already existed, do you want to replace it? [ Y / N (default) ] : ";
            $answer = strtolower(trim(strtoupper(fgets(STDIN))));
            if (!in_array($answer, [ 'y', 'yes' ])) {
                $replace = false;
            }
        }
        if ($replace) {
            File::copyFile($source, $destination);
        }
    }

    public static function opCacheClear()
    {
        if (function_exists('apc_clear_cache')) {
            apc_clear_cache();
        }
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }
    }

}
