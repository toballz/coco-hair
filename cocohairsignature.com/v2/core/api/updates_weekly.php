<?php
function api_updates_weekly(array $post)
{
    if (!isset($post["updatesWeekly"])) {
        return array();
    }

    $escaped = mysqli_real_escape_string(db::conn(), $post["updatesWeekly"]);
    $sql = "UPDATE `availability` SET `description`='" . $escaped . "' WHERE `namer`='weekly';";
    db::stmt($sql);

    return array("a" => true);
}
?>
