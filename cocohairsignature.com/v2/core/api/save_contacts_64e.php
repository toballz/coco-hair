<?php
function api_save_contacts_64e(array $post)
{
    global $db;

    $u = array();
    if (!isset($post["contact_info"]) || !isset($post["order_detail"])) {
        return $u;
    }
    $contact_info = json_decode($post["contact_info"]);
    $order_detail = json_decode($post["order_detail"]);



    $purchase_ID = tools::generateRandomAlphanumeric(rand(5,30));



    $email = $contact_info->email ?? "";
    $phone = json_encode(["cc" => "+1", "number" => $contact_info->phone ?? ""]);
    $fullname = $contact_info->fullname ?? "";
    $date = $order_detail->date ?? "";
    $time = $order_detail->time ?? "";
    $orderTree =    $order_detail->treeId ?? "" ;
    $orderVariant = explode("-",$orderTree)[2];

    #  $category_id . "-" . $product_id . "-" . $variant_id;
# 


    $sql = "INSERT INTO `product_purchased` 
        (`id_gen`, `email`, `phonenumber`, `date_scheduled`, `time_scheduled`, `customername`,
         `product_variant_id_ref`  ) VALUES 
        ('$purchase_ID', '$email', '$phone', '$date', '$time', '$fullname',
            '$orderVariant'   );";


    $db->query($sql);

    $payLink = tools::stripe_Create_Dynamic_Link_for_payments($email, 50.00, $purchase_ID, $fullname);
    $u["code"] = 301;
    $u["link"] = $payLink;

    return $u;
}
?>