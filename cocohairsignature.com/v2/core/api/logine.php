<?php
function api_logine(array $post)
{
    if (!isset($post["password"]) || !isset($post["email"])) {
        return array();
    }

    $emal = db::escapeDB(base64_decode($post["email"]));
    $pasw = md5(base64_decode($post["password"]));

    $yhd = "SELECT `id` FROM `availability` WHERE `accountEmail`='$emal' AND `accountPassword`='$pasw' AND `description`='--user';";
    $js = db::stmt($yhd);
    if (mysqli_num_rows($js) > 0) {
        return array(
            "code" => 200,
            "message" => "good"
        );
    }

    return array(
        "code" => 400,
        "message" => "username or password is wrong"
    );
}
?>
