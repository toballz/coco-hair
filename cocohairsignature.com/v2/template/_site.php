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

class Env
{
    public static $STRIPE_API_KEY;
    public static $STRIPE_WEBHOOK_SECRET;

    public static $SMTP_HOST;
    public static $SMTP_PORT;
    public static $SMTP_USERNAME;
    public static $SMTP_PASSWORD;
    public static $SMTP_AUTH;

    public static $ADMIN_USERNAME;
    public static $ADMIN_PASSWORD;

    PUBLIC STATIC $DB_HOST;
    PUBLIC STATIC $DB_USERNAME;
    PUBLIC STATIC $DB_PASSWORD;
    PUBLIC STATIC $DB_NAME;




    private static function get($key, $default = null)
    {
        return $_ENV[$key] ?? getenv($key) ?? $default;
    }

    public static function load()
    {
        self::$STRIPE_API_KEY = self::get('STRIPE_SECRET_KEY');
        echo self::$STRIPE_API_KEY;
        self::$STRIPE_WEBHOOK_SECRET = self::get('STRIPE_WEBHOOK_SECRET');

        self::$SMTP_HOST = self::get('SMTP_HOST');
        self::$SMTP_PORT = (int) self::get('SMTP_PORT', 587);
        self::$SMTP_USERNAME = self::get('SMTP_USERNAME');
        self::$SMTP_PASSWORD = self::get('SMTP_PASSWORD');
        self::$SMTP_AUTH = self::get('SMTP_AUTH', 'true') === 'true';

        self::$ADMIN_USERNAME = self::get('ADMIN_USERNAME', 'admin');
        self::$ADMIN_PASSWORD = self::get('ADMIN_PASSWORD', 'password');

        self::$DB_HOST = self::get('DB_HOST');
        self::$DB_USERNAME = self::get('DB_USERNAME');
        self::$DB_PASSWORD = self::get('DB_PASSWORD');
        self::$DB_NAME = self::get('DB_NAME');
    }
}