<?php
function api_delete_appointment(array $post)
{
    if (!isset($post["ksy"]) || $post["ksy"] === "") {
        return array();
    }

    $cat4 = trim($post["ksy"]);
    db::stmt("UPDATE `schedulee` SET `haspaid` = '14' WHERE `schedulee`.`rida` = '$cat4';");

    return array(
        "code" => 200,
        "message" => "ok"
    );
}
?>
