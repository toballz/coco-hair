<?php include_once("../config.php");

$sql = "
SELECT 
c.id_ai AS category_id,
c.category_name,
p.id_ai AS product_id,
p.hair_name,
p.description,
p.time_range,
p.hair_images,
v.id_ai AS variant_id,
v.name AS variant_name,
v.price,
v.description AS variant_description
FROM product_category c
JOIN product_lists p ON p.category = c.id_ai
LEFT JOIN product_variant v ON v.product_list_id_ref = p.id_ai
ORDER BY c.id_ai ASC, p.id_ai ASC, v.id_ai ASC
";

$result = $db->query($sql);

$db_category = [];

while ($row = $result->fetch_assoc()) {

    $category_id = (int) $row['category_id'];
    $product_id = (int) $row['product_id'];
    $category_name = $row['category_name'];

    if (!isset($db_category[$category_id])) {
        $db_category[$category_id] = [
            "id" => $category_id,
            "name" => $category_name,
            "items" => []
        ];
    }

    if (!isset($db_category[$category_id]["items"][$product_id])) {
        $images = json_decode($row['hair_images'], true);
        $image_ref = $product_id;
        if (is_array($images) && isset($images[0]) && trim((string) $images[0]) !== "") {
            $image_ref = $images[0];
        }

        $db_category[$category_id]["items"][$product_id] = [
            "id" => $product_id,
            "name" => $row['hair_name'],
            "description" => $row['description'],
            "time" => $row['time_range'],
            "image" => $image_ref,
            "variants" => []
        ];
    }

    if ($row['variant_id']) {
        $db_category[$category_id]["items"][$product_id]["variants"][] = [
            "id" => (int) $row['variant_id'],
            "name" => $row['variant_name'],
            "price" => $row['price'],
            "description" => $row['variant_description']
        ];
    }
}

?>
<!DOCTYPE html>
<html>

<head>
    <?php include(dirr . "/template/head.php"); ?>
    <title>Set Appointment | CocoHairSignature</title>

    <link rel="stylesheet" type="text/css"
        href="<?php echo site::url_hostdir(); ?>/3rdparty/datepicker4/css/pignose.calendar.min.css" />
    <script type="text/javascript"
        src="<?php echo site::url_hostdir(); ?>/3rdparty/datepicker4/js/pignose.calendar.full.min.js?<?php echo $recache; ?>"></script>

    <style>
        .pignose-calendar {
            font-family: 'Lato', 'Open Sans', sans-serif;
            font-size: 14px !important;
            max-width: 100% !important;
            width: 100%;
        }

        .time_sel_xewqctorbox {
            position: relative;
            text-align: center;
            max-width: 100% !important;
            font-weight: 600;
            padding: 20px 1.6em 0;
            background-color: #fafafa;
            border: 1px solid #d8d8d8;
            box-shadow: 0 4px 12px rgba(0, 0, 0, .25);
            -o-box-shadow: 0 4px 12px rgba(0, 0, 0, .25);
            -moz-box-shadow: 0 4px 12px rgba(0, 0, 0, .25);
            -webkit-box-shadow: 0 4px 12px rgba(0, 0, 0, .25);
            overflow: hidden;
            color: #000
        }

        .time_sel_xewqctorbox>ul {
            display: block;
            padding: 0;
            margin: 25px 0 0;
            ;
            max-height: 372px;
            overflow-y: scroll;
        }

        .time_sel_xewqctorbox>ul>li {
            width: 95%;
            list-style-type: none;
        }

        .time_sel_xewqctorbox>ul>li>label {
            width: 100%;
        }

        .time_sel_xewqctorbox>ul>li>label>div {
            width: 100%;
            margin: 4px 2px;
            cursor: pointer;
            padding: 12px;
            box-shadow: 2px 2px 4px rgb(0 0 0 / 51%)
        }

        .time_sel_xewqctorbox>ul>li>label>input.selector_timer_checked {
            display: none;
        }

        .time_sel_xewqctorbox>ul>li>label>input.selector_timer_checked:checked+div {
            background: #94db94;
            color: #700d0d;
        }





        .pickerdateClassModal_parent {
            display: none;
            width: 100%;
            height: 100%;
            position: fixed;
            background: rgb(0 0 0 / 69%);
            top: 0;
            z-index: 1001;
            left: 0;
            justify-content: center;
            align-items: center;
        }

        .pickerdateClassModal {
            width: 100%;
            padding: 23px;
            max-width: 800px;
            display: flex;
            justify-content: center;
        }

        .pickerdateClassModal>div:first-child {
            width: 65%;
        }

        .pickerdateClassModal>div:last-child {
            width: 45%;
        }


        .contact_cutstomer_vbg {
            position: relative;
            z-index: 123123;
            display: flex;
            justify-content: center;
            align-items: center;
            position: fixed;
            top: 0;
            width: 100%;
            left: 0;
            height: 100%;
            background: rgb(0 0 0 / 74%);
        }

        .contact_cutstomer_vbg>div {
            padding: 15px;
            max-width: 580px;
            width: 100%;
            background: #fff;
        }

        .contact_cutstomer_vbg>div h2 {
            text-align: center;
        }

        .contact_cutstomer_vbg>div>label {
            width: 100%;
            padding: 14px;
            display: block;
            font-size: 15px
        }

        .contact_cutstomer_vbg>div>label>input {
            width: 100%;
            padding: 15px;
        }

        .contact_cutstomer_vbg>div>label>p {
            margin: 2px 0 4px;
            font-weight: 600;
        }

        .close_btnn {
            position: absolute;
            top: 0;
            right: 0;
            padding: 2px 11px;
            background: red;
            cursor: pointer;
            color: #fff;
        }

        @media screen and (max-width:650px) {
            .pickerdateClassModal {
                display: block;
                overflow-y: scroll;
                height: 96%;
            }

            .pickerdateClassModal>div {
                max-width: 400px !important;
                width: 100% !important;
                margin: auto
            }
        }
    </style>


    <style>
        .qyrbry {
            display: flex;
            flex-direction: column;
            text-align: center;
        }

        @media screen and (max-width: 500px) {
            .qyrbry {
                font-size: 10px;
            }

            .afudf {
                font-size: 10px
            }
        }

        .qyrbry h4 {
            font-weight: 800;
            font-size: 14px;
            color: #bebebe;
            text-decoration: underline;
        }

        .qyrbry-box {
            position: relative;
            min-width: 100px;
            border: 2px solid #f9f07f;
            margin: 2px;
            padding: 24px 6px 0 0;
            border-radius: 15px;
            color: #e5c0b2;
            background: #000
        }

        .gsjht {
            padding: 10px;
            width: 43px;
            position: absolute;
            top: -24px;
            left: 50%;
            transform: translateX(-50%);
            background: #000;
            border-radius: 50px;
            border: 2px solid #f9f07f;
            color: #e5c0b2;
        }

        .gsjht svg {
            width: 100%
        }
    </style>


    <style>
        #categories .category-shell {
            background: #121212;
            border: 1px solid #2f2f2f;
            border-radius: 1rem;
            box-shadow: 0 10px 24px rgb(0 0 0 / 35%);
        }

        #categories .category-title {
            color: #fff;
            letter-spacing: 0.4px;
        }

        #categories .category-subtitle {
            color: #d4d4d4;
            font-size: 15px;
            margin-top: 8px;
        }

        #categories .category-card {
            border: 1px solid #383838;
            border-radius: 0.9rem;
            overflow: hidden;
            background: #1a1a1a;
            margin-bottom: 0.85rem;
        }

        #categories .category-toggle {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            width: 100%;
            padding: 14px 18px;
            color: #f3d2c5;
            text-align: left;
            background: linear-gradient(90deg, #1e1e1e, #232323);
            border: 0;
            cursor: pointer;
        }

        #categories .category-toggle:hover {
            color: #fff;
            background: linear-gradient(90deg, #282828, #2f2f2f);
        }

        #categories .category-toggle[aria-expanded="true"] {
            color: #fff;
            background: linear-gradient(90deg, #2b2b2b, #343434);
        }

        #categories .category-name {
            margin: 0;
            font-size: 1rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        #categories .chevron {
            display: inline-block;
            font-size: 1.15rem;
            line-height: 1;
            transition: transform 0.2s ease;
        }

        #categories .category-toggle[aria-expanded="true"] .chevron {
            transform: rotate(180deg);
        }

        #categories .category-body {
            background: #121212;
            padding: 16px;
        }

        #categories .jkwj4n5 {
            display: grid;
            grid-template-columns: minmax(120px, 280px) 1fr;
            gap: 16px;
            border: 1px solid #2f2f2f;
            border-radius: 0.8rem;
            padding: 14px;
            background: #1b1b1b;
            margin-bottom: 12px;
            align-items: start;
        }

        #categories .jkwj4n5-1,
        #categories .jkwj4n5-2 {
            width: auto !important;
            min-width: 0;
        }

        #categories .jkwj4n5-1 img {
            width: 100%;
            border-radius: 0.65rem;
            object-fit: cover;
        }

        #categories .jkwj4n5-2 {
            padding: 0;
        }

        #categories .style-meta {
            margin-bottom: 10px;
            color: #fff;
        }

        #categories .style-meta .style-name {
            display: block;
            font-size: 1.45rem;
            font-weight: 700;
            text-transform: capitalize;
            line-height: 1.2;
        }

        #categories .style-meta .style-desc {
            display: block;
            font-size: 0.95rem;
            color: #c8c8c8;
            margin-top: 2px;
        }

        #categories .style-option {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            cursor: pointer;
            color: #efefef;
            background: #222;
            border: 1px solid #333;
            border-radius: 0.65rem;
            padding: 9px 11px;
            margin-bottom: 8px;
            transition: background 0.2s ease, border-color 0.2s ease;
        }

        #categories .style-option:hover {
            background: #2a2a2a;
            border-color: #4a4a4a;
        }

        #categories .style-radio {
            accent-color: #f773c1;
            margin-top: 2px;
            transform: scale(1.15);
        }

        #categories .category-footer {
            margin-top: 16px;
            border: 1px solid #383838;
            border-radius: 0.9rem;
            background: #1b1b1b;
            padding: 16px;
            color: #eec7b8;
        }

        #categories .schedule-note {
            color: #f1d7cd;
            margin-bottom: 12px;
            font-size: 0.95rem;
        }

        #categories .book-btn {
            width: 100%;
            padding: 14px 0;
            font-weight: 700;
            letter-spacing: 0.3px;
        }

        @media (max-width: 768px) {
            #categories .jkwj4n5 {
                grid-template-columns: 1fr;
            }

            #categories .style-meta .style-name {
                font-size: 1.2rem;
            }
        }

        .agddhahah {
            color: #000;
            position: relative;
        }
    </style>
</head>




<body>
    <?php include(dirr . "/template/header.php"); ?>
    <section style="background:#000; ">
        <div style="background:#eec7b8;" class="container">
            <div class="row justify-content-center pt-3 mt-5">
                <div class="col-md-12 col-lg-10" style="padding: 0">

                    <div class="mb-5"><img src="https://cocohairsignature.com/img/n/aaaaa.png" style="width:100%" />
                    </div>

                    <div class="qyrbry">
                        <div style="display:flex">
                            <div class="qyrbry-box" style="flex:1">
                                <div class="gsjht"><svg width="800px" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M5.875 12.5729C5.30847 11.2498 5 9.84107 5 8.51463C5 4.9167 8.13401 2 12 2C15.866 2 19 4.9167 19 8.51463C19 12.0844 16.7658 16.2499 13.2801 17.7396C12.4675 18.0868 11.5325 18.0868 10.7199 17.7396C9.60664 17.2638 8.62102 16.5151 7.79508 15.6"
                                            stroke="#eec7b8" stroke-width="1.5" stroke-linecap="round" />
                                        <path
                                            d="M14 9C14 10.1046 13.1046 11 12 11C10.8954 11 10 10.1046 10 9C10 7.89543 10.8954 7 12 7C13.1046 7 14 7.89543 14 9Z"
                                            stroke="#eec7b8" stroke-width="1.5" />
                                        <path
                                            d="M20.9605 15.5C21.6259 16.1025 22 16.7816 22 17.5C22 18.4251 21.3797 19.285 20.3161 20M3.03947 15.5C2.37412 16.1025 2 16.7816 2 17.5C2 19.9853 6.47715 22 12 22C13.6529 22 15.2122 21.8195 16.5858 21.5"
                                            stroke="#eec7b8" stroke-width="1.5" stroke-linecap="round" />
                                    </svg></div>
                                <h4>ADDRESS</h4>
                                <div>I’m located in Grayslake, Illinois. Address will be sent after deposit is paid.
                                </div>
                            </div>
                            <div class="qyrbry-box" style="flex:1">
                                <div class="gsjht"><svg width="1000px" viewBox="3 6 17 14" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M14.5 9C14.5 9 13.7609 8 11.9999 8C8.49998 8 8.49998 12 11.9999 12C15.4999 12 15.5 16 12 16C10.5 16 9.5 15 9.5 15"
                                            stroke="#eec7b8" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path d="M12 7V17" stroke="#eec7b8" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                    </svg></div>
                                <h4>DEPOSITS</h4>
                                <div>A NON-REFUNDABLE DEPOSIT of $50 is REQUIRED FOR ALL STYLES. Deposit will be
                                    deducted from your service fee. $50 deposit is required to reschedule your
                                    appointment if cancellations occur.</div>
                            </div>
                        </div>

                        <div class="afudf" style="position:relative">
                            <h4 style="color:#000;margin-top:12px">HAIR INQUIRIES </h4>
                            <div
                                style="display:flex;justify-content:center;position:absolute;width:100%;z-index:111;top:27px">
                                <div class="gsjht" style="position: relative;left:auto;transform:none;top:auto;"><svg
                                        width="800px" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M8.25014 6.01489C8.25005 6.00994 8.25 6.00498 8.25 6V5C8.25 2.92893 9.92893 1.25 12 1.25C14.0711 1.25 15.75 2.92893 15.75 5V6C15.75 6.00498 15.75 6.00994 15.7499 6.0149C17.0371 6.05353 17.8248 6.1924 18.4261 6.69147C19.2593 7.38295 19.4787 8.55339 19.9177 10.8943L20.6677 14.8943C21.2849 18.186 21.5934 19.8318 20.6937 20.9159C19.794 22 18.1195 22 14.7704 22H9.22954C5.88048 22 4.20595 22 3.30624 20.9159C2.40652 19.8318 2.71512 18.186 3.33231 14.8943L4.08231 10.8943C4.52122 8.55339 4.74068 7.38295 5.57386 6.69147C6.17521 6.19239 6.96288 6.05353 8.25014 6.01489ZM9.75 5C9.75 3.75736 10.7574 2.75 12 2.75C13.2426 2.75 14.25 3.75736 14.25 5V6C14.25 5.99999 14.25 6.00001 14.25 6C14.1747 5.99998 14.0982 6 14.0204 6H9.97954C9.90177 6 9.82526 6 9.75 6.00002C9.75 6.00002 9.75 6.00003 9.75 6.00002V5ZM15.7399 10.8768C15.6718 10.4682 15.2854 10.1922 14.8768 10.2603C14.4682 10.3284 14.1922 10.7148 14.2603 11.1234L15.2603 17.1234C15.3284 17.532 15.7148 17.808 16.1234 17.7399C16.532 17.6718 16.808 17.2854 16.7399 16.8768L15.7399 10.8768ZM9.12317 10.2603C8.71459 10.1922 8.32817 10.4682 8.26007 10.8768L7.26007 16.8768C7.19198 17.2854 7.46799 17.6718 7.87657 17.7399C8.28515 17.808 8.67157 17.532 8.73966 17.1234L9.73966 11.1234C9.80776 10.7148 9.53174 10.3284 9.12317 10.2603Z"
                                            fill="#eec7b8" />
                                    </svg></div>
                            </div>
                            <div style=" display:flex;">
                                <div class="qyrbry-box" style="flex:1">
                                    <div>Hair is included for all Braidstyles. <br />I do not provide hair for any
                                        crotchet styles, locs or twist styles. Colors offered are
                                        1,1b,1/27,1/30,2,4,27,30,613. Other colors are offered upon request or you can
                                        bring your own pre-stretched hair. I recommend pre-stretched Xpressions,
                                        Fretress, Formation and Ruwa. <span style="color:yellow;">Hair must be at LEAST
                                            3 inches.</span> If your hair is shorter than 3 inches, please schedule a
                                        consultation by texting me.</div>
                                </div>
                                <div class="qyrbry-box" style="flex:1">
                                    <div>When booking appointment all of your contact information must be valid. DO NOT
                                        BOOK UNDER NICKNAME! Once your appointment is booked, I will contact you. After
                                        3 days of being serviced, I am no longer responsible for any misfortunes.
                                        Clients are responsible for the upkeep and maintenance of their hair.</div>
                                </div>
                            </div>

                            <div class="qyrbry-box" style="padding-top:6px"><b>NO EXTRA PEOPLE! ONLY THOSE BEING
                                    SERVICED. Unless you are accompanying a minor. YOU CANNOT schedule for a certain
                                    style and then change it at the appointment.</b></div>

                            <div style=" display:flex;">
                                <div class="qyrbry-box" style="flex:1;padding-top:6px">
                                    <div>Make sure your hair is free of texturized sores. If you have any sores or open
                                        wounds on your scalp YOU WILL NOT BE SERVICED! If you are unsure about edges,
                                        undercut, soft hair, etc, please email me at <a
                                            href="mailto:cocohairsignature@gmail.com">cocohairsignature@gmail.com</a>
                                        regarding a consultation to ensure that you receive the best services for your
                                        hair. My consultations are free and typically take no less than 15 minutes.
                                    </div>
                                </div>
                                <div class="qyrbry-box" style="flex:1;padding-top:6px">
                                    <div>Have a blunt/pixie cut? No problem. I will try my best to hide your ends using
                                        my skill and products. But keep in mind that style may not come out as neat.
                                        <span style="color:yellow;">PLEASE COME CHEMICAL/OIL FREE,</span> <span
                                            style="color:yellow;">WASHED, BLOW DRIED AND DETANGLED.</span>
                                        <div style="padding:0 25px;margin:6px 0;"><img
                                                src="https://cocohairsignature.com/img/n/ettjo.png" class="img-fluid" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div style="display:flex;padding-top:15px">

                            <div class="qyrbry-box" style="flex:1">
                                <h4>NO CALL NO SHOW</h4>
                                <div>You must cancel 24 hours in advance or you’ll be charged 75% of service fee. NO
                                    SHOW appointments are charged 100% and you can no longer book anymore IF YOU REFUSE
                                    TO PAY FOR SERVICES. <span style="color:yellow;">Emergency / Same day / Squeeze In /
                                        Day off Appointment: $150 and up depending on hairstyle.</span> Please note that
                                    this is an additional fee.</div>
                            </div>
                            <div class="qyrbry-box" style="flex:1">
                                <h4>LATE POLICIES</h4>
                                <div>You’ll have 10 minutes grace period. After 10 minutes you will be charged a $35
                                    late fee. After 20 minutes your appointment will be considered a NO SHOW.</div>
                            </div>
                        </div>
                    </div>

                    <p style="text-align:center;margin-top: 12px"><b>PLEASE COME WITH A GOOD ATTITUDE 😊</b></p>
                </div>
            </div>
        </div>
    </section>

    <section style="color:#fff;background:#000 !important;" id="categories">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-xl-11">
                    <div class="category-shell p-3 p-md-4">
                        <div class="mbr-section-head text-center mb-4">
                            <h3 class="category-title mb-0"><strong>Categories</strong></h3>
                            <p class="category-subtitle mb-0">If you'd like longer length, I charge extra. Please
                                contact me prior to booking.</p>
                        </div>

                        <div id="bootstrap-accordion_1" class="accordion" role="tablist" aria-multiselectable="true">
                            <?php $i = 0;
                            foreach ($db_category as $category_row) {
                                $i++;
                                $category_id = (int) $category_row["id"];
                                $category_name = $category_row["name"]; ?>
                                <div class="card category-card">
                                    <div class="card-header p-0 border-0 bg-transparent" role="tab"
                                        id="headingOne_<?php echo $i; ?>">
                                        <button type="button" class="category-toggle collapsed" data-bs-toggle="collapse"
                                            data-bs-target="#collapse1_<?php echo $i; ?>" aria-expanded="false"
                                            aria-controls="collapse1_<?php echo $i; ?>">
                                            <h6 class="category-name">
                                                <?php echo htmlspecialchars($category_name, ENT_QUOTES); ?>
                                            </h6>
                                            <span class="chevron" aria-hidden="true">&#9662;</span>
                                        </button>
                                    </div>
                                    <div id="collapse1_<?php echo $i; ?>" class="panel-collapse noScroll collapse"
                                        role="tabpanel" aria-labelledby="headingOne_<?php echo $i; ?>"
                                        data-bs-parent="#bootstrap-accordion_1">
                                        <div class="category-body">
                                            <div class="panel-text">
                                                <?php foreach ($category_row["items"] as $product_row) {
                                                    $product_id = (int) $product_row["id"];

                                                    $image_ref = $product_row["image"];
                                                    $image_url = site::url_s3Host()."/img/" . $image_ref . ".jpg?" . $recache;

                                                    $style_name = trim((string) $product_row["name"]);
                                                    $style_desc = trim((string) $product_row["description"]);
                                                    if ($style_desc === "") {
                                                        $style_desc = "Time - " . trim((string) $product_row["time"]);
                                                    }
                                                    ?>
                                                    <div class="jkwj4n5">
                                                        <div class="jkwj4n5-1">
                                                            <img src="<?php echo $image_url; ?>"
                                                                alt="<?php echo htmlspecialchars($style_name, ENT_QUOTES); ?>">
                                                        </div>
                                                        <div class="jkwj4n5-2">
                                                            <div class="style-meta">
                                                                <span
                                                                    class="style-name"><?php echo htmlspecialchars($style_name, ENT_QUOTES); ?></span>
                                                                <span
                                                                    class="style-desc"><?php echo htmlspecialchars($style_desc, ENT_QUOTES); ?></span>
                                                            </div>
                                                            <?php if (count($product_row["variants"]) > 0) {
                                                                foreach ($product_row["variants"] as $variant_row) {
                                                                    $variant_id = (int) $variant_row["id"];
                                                                    $price_text = "$" . number_format((float) $variant_row["price"], 2);
                                                                    $variant_title = trim((string) $variant_row["name"]);
                                                                    $variant_desc = trim((string) $variant_row["description"]);
                                                                    $option_text = $price_text . " - " . $variant_title;
                                                                    if ($variant_desc !== "") {
                                                                        $option_text .= " (" . $variant_desc . ")";
                                                                    }
                                                                    $option_id = $category_id . "-" . $product_id . "-" . $variant_id;
                                                                    ?>
                                                                    <label class="style-option">
                                                                        <input class="style-radio" type="radio"
                                                                            data-hair="<?php echo $option_id; ?>" name="n7q5">
                                                                        <span><?php echo htmlspecialchars($option_text, ENT_QUOTES); ?></span>
                                                                    </label>
                                                                <?php }
                                                            } else { ?>
                                                                <div class="style-option">
                                                                    <span>No variants available yet.</span>
                                                                </div>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>

                            <div class="category-footer">
                                <div class="schedule-note"><b>Monday - Saturday</b> (08:00AM - 07:00PM)</div>
                                <button class="btn btn-warning book-btn" onclick="saveHairStyleSElect()">Select
                                    Date</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <?php include(dirr . "/template/footer.php"); ?>




    <div class="modal fade" id="dateTimeSelectionModal" tabindex="-1" aria-labelledby="dateTimeSelectionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content bg-dark text-white">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="dateTimeSelectionModalLabel">Select Date & Time</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row gy-4">
                        <div class="col-lg-6">
                            <div class="agddhahah"></div>
                        </div>
                        <div class="col-lg-6">
                            <div class="time_sel_xewqctorbox"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="contactInfoModal" tabindex="-1" aria-labelledby="contactInfoModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-dark text-white">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="contactInfoModalLabel">Your Contact Info</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="contactForm">
                        <div class="mb-3">
                            <label for="fullname" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="fullname" name="fullname"
                                placeholder="First and Last Name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Email"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="phonenumber" class="form-label">Phone Number</label>
                            <input type="text" class="form-control" id="phonenumber" name="phonenumber"
                                placeholder="Phone Number" maxlength="15" oninput="formatPhoneNumber(this);" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="saveContactInfo();">Proceed to
                        Checkout</button>
                </div>
            </div>
        </div>
    </div>


    <script type="text/javascript"
        src="<?php echo site::url_hostdir(); ?>/3rdparty/datepicker4/js/pignose.calendar.full.min.js?<?= $recache; ?>"></script>
    <script type="text/javascript" src="<?php echo site::url_hostdir(); ?>/assets/js.js?<?= $recache; ?>"></script>


</body>

</html>