<?php ini_set('session.gc_maxlifetime', 18010);

session_set_cookie_params([
    'lifetime' => 18010,
    'path' => '/',
    'domain' => '',
    'secure' => true,        // must be true for SameSite=None
    'httponly' => true,
    'samesite' => 'None'
]);
session_name(md5("Live4VER_adminn00oo"));
session_start();


const dirr = __DIR__;
 
include(dirr . "/../template/_site.php");
Env::load();
include(dirr . "/../template/_functions.php");
include(dirr . "/../template/_db_.php");

function admin_is_logged_in(): bool
{
    return !empty($_SESSION["admin_logged_in"]);
}
 

$adminCurrentScript = basename((string) ($_SERVER["PHP_SELF"] ?? ""));
$adminPublicScripts = ["login.php", "logout.php"];

if (!in_array($adminCurrentScript, $adminPublicScripts, true) && !admin_is_logged_in()) {
    header("Location: /v2/admin/pages/login.php");
}
?>