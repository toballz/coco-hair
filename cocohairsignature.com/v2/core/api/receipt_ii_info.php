<?php
function api_receipt_ii_info(array $post)
{
    if (!isset($post["receiptIIinfo"])) {
        return array();
    }

    $tg = db::stmt("SELECT `price`,`time`,`hairstyle`,`email`,`phonne`,`customername`,`image` FROM `schedulee` WHERE `rida`='" . $post["receiptIIinfo"] . "' AND `haspaid`='1';");
    $u = mysqli_fetch_assoc($tg);
    return is_array($u) ? $u : array();
}
?>
