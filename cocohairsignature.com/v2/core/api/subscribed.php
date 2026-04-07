<?php
function api_subscribed(array $post)
{
    $yhd = "SELECT `description` FROM `availability` WHERE `namer`='hasSubscribeMonthly' AND `id`='5';";
    $js = db::stmt($yhd);
    $row = mysqli_fetch_assoc($js);
    $isSubscribed = is_array($row) && isset($row["description"]) && $row["description"] == "true";
    if ($isSubscribed) {
        return array(
            "code" => 200,
            "message" => "subscribed"
        );
    }

    return array(
        "code" => 400,
        "message" => "not subscribed"
    );
}
?>
