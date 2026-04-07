<?php
include_once("../config.php");
header('Content-Type: application/json');


// header("Access-Control-Allow-Origin: *");
// header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
// header("Access-Control-Allow-Headers: Content-Type, Authorization");
?>

<?php 

 
$u=array();
if(isset($_POST['v']) && $_POST['v']=="1"){


    if(isset($_POST['getDatesAppointmentsSpecDate']) && isset($_POST['dateFrom'])){
        $tg=db::stmt("SELECT `hairstyle`,`image`,`hairstyle`,`rida`,`date`,`time` FROM schedulee WHERE `date` = '".$_POST['dateFrom']."' AND `haspaid`='1';");
 
        $i=0;
        while($rr=mysqli_fetch_assoc($tg)){
            $rd=DateTime::createFromFormat('Ymd', $rr['date']);
 
            $u[$i]['imageUrl']=  site::url_hostdir()."/img/".$rr['image'].".jpg?93jv"; 
            $u[$i]['datetime']= $rd->format('Y F, l jS')." ".$rr['time'];
            $u[$i]['hairname']=$rr['hairstyle'];
            $u[$i]['orderId']=$rr['rida'];
              
            $i++; 
        }
    }
    if(isset($_POST['getDatesAppointmentsMoreThanDate']) && isset($_POST['dateTo'])){
        $tg=db::stmt("SELECT `date` FROM schedulee WHERE `date` >= '".trim($_POST['dateTo'])."' AND `haspaid`='1' LIMIT 13;");
        $i=0;
        while($rr=mysqli_fetch_assoc($tg)){
            $rd=DateTime::createFromFormat('Ymd', $rr['date']);
            $u[$i]['year']=$rd->format('Y'); 
            $u[$i]['month']=$rd->format('m');
            $u[$i]['day']=$rd->format('j');
              
            $i++; 
        }
    }
    if(isset($_POST['getweeklyStatic']) && isset($_POST['had'])){
        $tg=db::stmt("SELECT `description` FROM `availability` WHERE `namer`='weekly' AND `id`='1';");
 
        while($rr=mysqli_fetch_assoc($tg)){
            $u=json_decode($rr['description']); 
               
        }
    }
    if(isset($_POST['updatesWeekly']) && isset($_POST['ajr'])){
        $yfs="UPDATE `availability` SET `description`='".mysqli_real_escape_string(db::conn(),$_POST['updatesWeekly'])."' WHERE `namer`='weekly';";
        $tg=db::stmt($yfs);
        //echo $yfs;
        $u=array('a'=>true);
    }
    
    if(isset($_POST['receiptIIinfo']) && isset($_POST['j'])){
        $tg=db::stmt("SELECT `price`,`time`,`hairstyle`,`email`,`phonne`,`customername`,`image`  FROM `schedulee` WHERE `rida`='".$_POST['receiptIIinfo']."' AND `haspaid`='1' ;");
        $u=mysqli_fetch_assoc($tg);
    }
    if(isset($_POST['getOverrideDates']) && isset($_POST['va'])){
        $tg=db::stmt("SELECT `description` FROM `availability` WHERE `namer`='override';");
        $u=json_decode(mysqli_fetch_assoc($tg)['description']);
    }


//
//
    if(isset($_POST['stats']) && isset($_POST['sg']) && isset($_POST['beginingOfThisMonth']) && isset($_POST['beginingOfLastMonth'])){
        $botm=trim($_POST['beginingOfThisMonth']);$botmbs=$botm+30;
        $bolm=trim($_POST['beginingOfLastMonth']); 
        $tg=db::stmt("SELECT 
        (SELECT COUNT(*) FROM `schedulee` WHERE `date` >= '$botm' AND `date` < '$botmbs' AND `haspaid`='1') AS beginingOfThisMonth,
        (SELECT COUNT(*) FROM schedulee WHERE `date` >= '$bolm' AND `date` < '$botm' AND `haspaid`='1') AS lastMonth,
        (SELECT COUNT(*) FROM schedulee WHERE `haspaid`='1') AS allToDate
            FROM schedulee; ");

            //
        $tg2=db::stmt("SELECT `hairstyle`,`image`, COUNT(*) AS appearance_count FROM schedulee  WHERE `haspaid`='1' GROUP BY `hairstyle` ORDER BY appearance_count DESC LIMIT 5");
            // 
        while($yts=mysqli_fetch_assoc($tg2)){
            $u['popularHairstyleBooked'][]=$yts; 
        }
        while($ys=mysqli_fetch_assoc($tg)){
            $u['beginingOfThisMonth']=$ys['beginingOfThisMonth'];
            $u['lastMonth']=$ys['lastMonth'];
            $u['allToDate']=$ys['allToDate'];
        }


        
    }

    //select date time
    if(isset($_POST['select_time_forDate']) &&  $_POST['getDate4Thd'] != ""){

        $thisDAte=trim($_POST['getDate4Thd']);
        
        $thisDAte_dayInWeek = strtolower(date('l', strtotime($thisDAte))); // Output: Friday

        //get override
        //[{"date": "20240510", "time": "1530"}, {"date": "20240512", "time": "1130"}]
        $tg1=db::stmt("SELECT `description` FROM `availability` WHERE `namer` = 'override' LIMIT 1");
        $overrided_fetch_assoc=json_decode(mysqli_fetch_assoc($tg1)['description']);
        //get if person booked this date
        $tg2=db::stmt("SELECT `time` FROM `schedulee` WHERE `date` = '".$thisDAte."' AND `haspaid`='1';");
        $Persons_AlreadyBookedFot_thisDate=array();
        while($lo=mysqli_fetch_assoc($tg2)){
                $Persons_AlreadyBookedFot_thisDate[]=$lo['time'];
        } 
        //regular schedules
        //{"sunday":"1233,3413","monday":"0830, 1230","tuesday":"0837, 1230","wednesday":"0830, 1230","thursday":"0830, 1230","friday":"0830, 1230","saturday":"0830, 1230"}
        $tg3=db::stmt("SELECT `description` FROM `availability` WHERE `namer` = 'weekly' LIMIT 1;");
        $reqgularSchedule_fetch_assoc=json_decode(strtolower(mysqli_fetch_assoc($tg3)['description']));
        



       //
       //
       //when day of week "monday" isset
       if (isset($reqgularSchedule_fetch_assoc->$thisDAte_dayInWeek)) {
            $times_to_show_from_weekly=array_map('trim',explode(",",$reqgularSchedule_fetch_assoc->$thisDAte_dayInWeek));
            //$times_to_show=$get_weekly_schedule;

            //override wekly
            foreach ($overrided_fetch_assoc as $ovrrd) {
                if ($ovrrd->date === $thisDAte) {
                    //override weekly
                    $times_to_show_from_weekly= array_map('trim',explode(",",$ovrrd->time));
                    break;
                }
            }


            //times to show from weekly
            foreach($times_to_show_from_weekly as $ki=>$times){
                if(empty($times)){
                    unset($times_to_show_from_weekly[$ki]); 
                }
                
                // if user already booked
                if(in_array($times, $Persons_AlreadyBookedFot_thisDate)){
                    //remove times from times to show
                    foreach($times_to_show_from_weekly as $k=>$a ){
                        if($a == $times){
                            unset($times_to_show_from_weekly[$k]);
                            break;
                        }
                    }
               }
            };
            
            $u=$times_to_show_from_weekly;
        }
    }
    


    //select date time
    if(isset($_POST['save_contacts_64e']) && isset($_POST['co']) && isset($_POST['ord'])){
        
        $ord = trim($_POST['ord']);
        $contactInfo=json_decode($_POST['co']);
        $ridaa=tools::generateRandomAlphanumeric(9);

        $hairFromJsonDb_img="";
        $hairFromJsonDb_title="";
        $hairFromJsonDb_price="";
        $hairFromJsonDb_timeRange="";

        // New format: categoryId-productId-variantId
        if (preg_match('/^\d+\-\d+\-\d+$/', $ord)) {
            $ids = array_map('intval', explode("-", $ord));
            $categoryId = $ids[0];
            $productId = $ids[1];
            $variantId = $ids[2];

            $q = db::stmt("SELECT 
                c.id_ai AS category_id, c.category_name,
                p.id_ai AS product_id, p.hair_name, p.time_range, p.hair_images,
                v.id_ai AS variant_id, v.name AS variant_name, v.price
                FROM product_category c
                JOIN product_lists p ON p.category = c.id_ai
                JOIN product_variant v ON v.product_list_id_ref = p.id_ai
                WHERE c.id_ai = '$categoryId' AND p.id_ai = '$productId' AND v.id_ai = '$variantId'
                LIMIT 1;");

            if (mysqli_num_rows($q) === 1) {
                $row = mysqli_fetch_assoc($q);
                $imgRef = (string)$row['product_id'];
                $imgArr = json_decode($row['hair_images'], true);
                if (is_array($imgArr) && isset($imgArr[0]) && trim((string)$imgArr[0]) !== "") {
                    $imgRef = (string)$imgArr[0];
                }

                $hairFromJsonDb_img = preg_replace('/[^0-9a-zA-Z_-]/', '', $imgRef);
                if ($hairFromJsonDb_img === "") {
                    $hairFromJsonDb_img = (string)$row['product_id'];
                }

                $hairFromJsonDb_title = trim($row['category_name']." - ".$row['hair_name']." - ".$row['variant_name']);
                $hairFromJsonDb_price = "$".number_format((float)$row['price'], 2);
                $hairFromJsonDb_timeRange = trim((string)$row['time_range']);
            }
        }

        // Legacy fallback format: imageId#optionIndex
        if ($hairFromJsonDb_title === "") {
            $hair=array_map('trim',explode("#",$ord));
            if (count($hair) >= 2) {
                foreach($haiecollection as $col=>$arr){
                    foreach($arr as $i=>$p){
                        if(isset($p[$hair[0]])){ $hairTitle=$i; $ProductInfo=$p[$hair[0]]; }
                    }
                }

                if (isset($ProductInfo)) {
                    $explodeInfo=explode("##",$ProductInfo);
                    $hairFromJsonDb_img=$hair[0];
                    $hairFromJsonDb_title=$hairTitle." ".$explodeInfo[0];
                    $hairFromJsonDb_price=isset($explodeInfo[$hair[1]]) ? $explodeInfo[$hair[1]] : "";
                    $hairFromJsonDb_timeRange=isset($explodeInfo[1]) ? (explode("Time - ",$explodeInfo[1])[1]) : "";
                }
            }
        }

        if ($hairFromJsonDb_title !== "") {
            $yhd="INSERT INTO `schedulee` 
            (`rida`, `email`, `phonne`, `date`, `time`, `customername`,
             `image`, `price`, `timeRange`, `hairstyle`, `haspaid`) VALUES 
             ('$ridaa', '".$contactInfo->email."', '".$contactInfo->phone."', '".$contactInfo->date."', '".$contactInfo->time."', '".$contactInfo->fullname."',
             '$hairFromJsonDb_img', '$hairFromJsonDb_price', '$hairFromJsonDb_timeRange', '$hairFromJsonDb_title', '0');";
            $js = db::stmt($yhd);

            $payLink=tools::stripe_Create_Dynamic_Link_for_payments($contactInfo->email, 50.00, $ridaa,$contactInfo->fullname);
            $u['code']=301;
            $u['link']=$payLink;
        } else {
            $u['code']=400;
            $u['message']="Invalid hairstyle selection.";
        }
    }

 //set override dates [{},{}]
 if(isset($_POST['updateOverrided']) && isset($_POST['cat']) && $_POST['cat'] != ""){
        $cat4=trim($_POST['cat']);
    $he=db::stmt("UPDATE `availability` SET `description` = '$cat4' WHERE `id` = '3' AND `namer`='override';");
    
    $u['code']=200;
    $u['message']="ok";

}
    //delete appointment date haspaid=14
    if(isset($_POST['deleteAppointment']) && isset($_POST['ksy']) && $_POST['ksy'] != ""){
        $cat4=trim($_POST['ksy']);
    $he=db::stmt("UPDATE `schedulee` SET `haspaid` = '14' WHERE `schedulee`.`rida` = '$cat4';");

    $u['code']=200;
    $u['message']="ok";

    }
    


//get message notification
if(isset($_POST['get_messageNotifiy']) && isset($_POST['a'])){
    $afa=db::stmt("SELECT `description` FROM `availability` WHERE `id` = '4' AND `namer`='message_notification';");

        $u=json_decode(mysqli_fetch_assoc($afa)['description']);
}



   //login    
   if(isset($_POST['logine']) && isset($_POST['password']) && isset($_POST['email'])){
  
    $emal=db::escapeDB(base64_decode($_POST['email']));
    $pasw=md5(base64_decode($_POST['password']));
    
    $yhd="SELECT `id` FROM `availability` WHERE `accountEmail`='$emal' AND `accountPassword`='$pasw' AND `description`='--user';";
    //echo $yhd;
    $js = db::stmt($yhd);
    if(mysqli_num_rows($js) > 0){
        $u['code']=200;
        $u['message']="good";
    }else{
        $u['code']=400;
        $u['message']="username or password is wrong";
    }
}

//check subscription
if(isset($_POST['subscribed']) && isset($_POST['subscribed1']) && isset($_POST['subscribedr'])){
    $yhd="SELECT `description` FROM `availability` WHERE `namer`='hasSubscribeMonthly' AND `id`='5';";
    //echo $yhd;
    $js = db::stmt($yhd);
    if(mysqli_fetch_assoc($js)['description'] == "true"){
        $u['code']=200;
        $u['message']="subscribed";
    }else{
        $u['code']=400;
        $u['message']="not subscribed";
    }
}



    
    echo json_encode($u);
}




?>
