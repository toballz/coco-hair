<?php
function api_get_message_notifiy(array $post)
{
    $afa = db::stmt("SELECT `description` FROM `availability` WHERE `id` = '4' AND `namer`='message_notification';");
    $row = mysqli_fetch_assoc($afa);
    if (!is_array($row) || !isset($row["description"])) {
        return array();
    }

    $decoded = json_decode($row["description"]);
    return ($decoded !== null) ? $decoded : array();
}
?>
