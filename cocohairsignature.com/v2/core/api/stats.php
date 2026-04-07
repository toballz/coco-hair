<?php
function api_stats(array $post)
{
    $u = array();
    if (
        !isset($post["beginingOfThisMonth"]) ||
        !isset($post["beginingOfLastMonth"])
    ) {
        return $u;
    }

    $botm = trim($post["beginingOfThisMonth"]);
    $botmbs = $botm + 30;
    $bolm = trim($post["beginingOfLastMonth"]);

    $tg = db::stmt("SELECT 
        (SELECT COUNT(*) FROM `schedulee` WHERE `date` >= '$botm' AND `date` < '$botmbs' AND `haspaid`='1') AS beginingOfThisMonth,
        (SELECT COUNT(*) FROM schedulee WHERE `date` >= '$bolm' AND `date` < '$botm' AND `haspaid`='1') AS lastMonth,
        (SELECT COUNT(*) FROM schedulee WHERE `haspaid`='1') AS allToDate
        FROM schedulee;");

    $tg2 = db::stmt("SELECT `hairstyle`,`image`, COUNT(*) AS appearance_count FROM schedulee WHERE `haspaid`='1' GROUP BY `hairstyle` ORDER BY appearance_count DESC LIMIT 5");
    while ($yts = mysqli_fetch_assoc($tg2)) {
        $u["popularHairstyleBooked"][] = $yts;
    }
    while ($ys = mysqli_fetch_assoc($tg)) {
        $u["beginingOfThisMonth"] = $ys["beginingOfThisMonth"];
        $u["lastMonth"] = $ys["lastMonth"];
        $u["allToDate"] = $ys["allToDate"];
    }

    return $u;
}
?>
