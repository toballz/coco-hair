<?php ini_set('session.gc_maxlifetime', 18010);

session_set_cookie_params([
    'lifetime' => 18010,
    'path' => '/',
    'domain' => '',
    'secure' => true,        // must be true for SameSite=None
    'httponly' => true,
    'samesite' => 'None'
]);
session_name(md5("Live4VER"));
session_start();


const dirr = __DIR__;

include(dirr . "/template/_env.php");
include(dirr . "/template/_db.php");
include(dirr . "/template/_site.php");
include(dirr . "/template/_functions.php");
?>