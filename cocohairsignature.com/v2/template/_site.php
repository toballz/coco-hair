<?php
$recache = "0x.3sf";
class site
{
    const name = "CocoHairSignature, LLC";
    const address = "2835 Belvidere Road Suite 314, Waukegan Illinois 60085";
    const phone = "+1(224) 440-1819";


    public static function url_domain()
    {
        // returns domain[:port]
        return $_SERVER["HTTP_HOST"];
    }
    public static function url_hostdir()
    {
        $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
        $protocol = $https ? 'https://' : 'http://';

        return $protocol . self::url_domain() . "/v2";
    }
    public static function url_fullUri($clean)
    {
        $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
        $protocol = $https ? 'https://' : 'http://';

        $uri = $_SERVER['REQUEST_URI'] ?? '/';

        if ($clean) {
            // remove query string and fragment
            $uri = parse_url($uri, PHP_URL_PATH);
        }

        return $protocol . self::url_domain() . $uri;

    }

    public static function url_s3Host($clean = true)
    {
        return "http://" . self::url_domain();
    }
}