<?php
include_once("../config.php");

function receiptEsc($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
}

function receiptFormatDate($rawDate)
{
    $rawDate = trim((string) $rawDate);
    if ($rawDate === "") {
        return "Not set";
    }

    if (preg_match("/^\d{8}$/", $rawDate)) {
        $dateObj = DateTime::createFromFormat("Ymd", $rawDate);
        if ($dateObj instanceof DateTime) {
            return $dateObj->format("l, F j, Y");
        }
    }

    if (preg_match("/^\d{4}-\d{2}-\d{2}$/", $rawDate)) {
        $dateObj = DateTime::createFromFormat("Y-m-d", $rawDate);
        if ($dateObj instanceof DateTime) {
            return $dateObj->format("l, F j, Y");
        }
    }

    $timestamp = strtotime($rawDate);
    return ($timestamp !== false) ? date("l, F j, Y", $timestamp) : $rawDate;
}

function receiptFormatTime($rawTime)
{
    $rawTime = trim((string) $rawTime);
    if ($rawTime === "") {
        return "Not set";
    }

    if (preg_match("/^\d{3,4}$/", $rawTime)) {
        $rawTime = str_pad($rawTime, 4, "0", STR_PAD_LEFT);
        $timeObj = DateTime::createFromFormat("Hi", $rawTime);
        if ($timeObj instanceof DateTime) {
            return $timeObj->format("g:i A");
        }
    }

    $timestamp = strtotime($rawTime);
    return ($timestamp !== false) ? date("g:i A", $timestamp) : $rawTime;
}

function receiptFormatMoney($value)
{
    if ($value === null || trim((string) $value) === "") {
        return "TBD";
    }

    if (is_numeric($value)) {
        return "$" . number_format((float) $value, 2);
    }

    return trim((string) $value);
}

function receiptFormatIssuedOn($rawDateTime)
{
    $rawDateTime = trim((string) $rawDateTime);
    if ($rawDateTime === "") {
        return date("F j, Y g:i A");
    }

    $timestamp = strtotime($rawDateTime);
    return ($timestamp !== false) ? date("F j, Y g:i A", $timestamp) : date("F j, Y g:i A");
}

$rawOrderId = $_GET["orderId"] ?? "";
$orderId = preg_replace("/[^a-zA-Z0-9]/", "", trim((string) $rawOrderId));
$escapedOrderId = mysqli_real_escape_string($db, $orderId);

$receiptFound = false;
$customerName = "";
$customerEmail = "";
$customerPhone = "";
$scheduleDate = "Not set";
$scheduleTime = "Not set";
$serviceName = "Appointment Service";
$serviceDuration = "Not provided";
$servicePrice = "TBD";
$serviceDescription = "";
$receiptIssuedOn = date("F j, Y g:i A");
$imageUrl = site::url_s3Host() . "/img/n/00.jpg";

if (strlen($orderId) >= 5) {
    $sqlNew = "SELECT
        pp.id_gen,
        pp.customername,
        pp.email,
        pp.phonenumber,
        pp.date_scheduled,
        pp.time_scheduled,
        pp.date_created,
        pv.price,
        pv.name AS variant_name,
        pv.description AS variant_description,
        pl.id_ai AS product_id,
        pl.hair_name,
        pl.description AS hair_description,
        pl.time_range,
        pl.hair_images,
        pc.category_name
    FROM product_purchased pp
    LEFT JOIN product_variant pv ON pv.id_ai = pp.product_variant_id_ref
    LEFT JOIN product_lists pl ON pl.id_ai = pv.product_list_id_ref
    LEFT JOIN product_category pc ON pc.id_ai = pl.category
    WHERE pp.id_gen = '$escapedOrderId' AND pp.haspaid = '1'
    LIMIT 1";

    $resultNew = $db->query($sqlNew);

    if ($resultNew instanceof mysqli_result && $resultNew->num_rows === 1) {
        $row = $resultNew->fetch_assoc();

        $customerName = trim((string) ($row["customername"] ?? ""));
        $customerEmail = trim((string) ($row["email"] ?? ""));

        $phoneData = json_decode((string) ($row["phonenumber"] ?? ""), true);
        if (is_array($phoneData)) {
            $phoneCc = trim((string) ($phoneData["cc"] ?? ""));
            $phoneNumber = trim((string) ($phoneData["number"] ?? ""));
            $customerPhone = trim($phoneCc . " " . $phoneNumber);
        }
        if ($customerPhone === "") {
            $customerPhone = trim((string) ($row["phonenumber"] ?? ""));
        }

        $scheduleDate = receiptFormatDate($row["date_scheduled"] ?? "");
        $scheduleTime = receiptFormatTime($row["time_scheduled"] ?? "");
        $serviceDuration = trim((string) ($row["time_range"] ?? ""));
        if ($serviceDuration === "") {
            $serviceDuration = "Not provided";
        }
        $servicePrice = receiptFormatMoney($row["price"] ?? "");

        $categoryName = trim((string) ($row["category_name"] ?? ""));
        $hairName = trim((string) ($row["hair_name"] ?? ""));
        $variantName = trim((string) ($row["variant_name"] ?? ""));

        $nameParts = [];
        if ($categoryName !== "") {
            $nameParts[] = $categoryName;
        }
        if ($hairName !== "") {
            $nameParts[] = $hairName;
        }
        if ($variantName !== "") {
            $nameParts[] = $variantName;
        }
        if (count($nameParts) > 0) {
            $serviceName = implode(" - ", $nameParts);
        }

        $hairDescription = trim((string) ($row["hair_description"] ?? ""));
        $variantDescription = trim((string) ($row["variant_description"] ?? ""));
        $serviceDescription = trim($hairDescription . (($hairDescription !== "" && $variantDescription !== "") ? " " : "") . $variantDescription);

        $imageRef = "";
        $imageSet = json_decode((string) ($row["hair_images"] ?? ""), true);
        if (is_array($imageSet)) {
            foreach ($imageSet as $imageCandidate) {
                $imageCandidate = preg_replace("/[^0-9a-zA-Z_-]/", "", trim((string) $imageCandidate));
                if ($imageCandidate !== "") {
                    $imageRef = $imageCandidate;
                    break;
                }
            }
        }
        if ($imageRef === "") {
            $imageRef = preg_replace("/[^0-9a-zA-Z_-]/", "", (string) ($row["product_id"] ?? ""));
        }

        $imageUrl = site::url_s3Host() . "/img/" . $imageRef . ".jpg?" . ($recache ?? "4");


        $receiptIssuedOn = receiptFormatIssuedOn($row["date_created"] ?? "");
        $receiptFound = true;
    }
}

$isEmailMode = isset($_GET["email"]) && $_GET["email"] === "1";
$isRedirectedFromStripe = false;
if (($_GET['redrfrm'] ?? "null") == "stripe") {
    $isRedirectedFromStripe = true;
}
?>
<?php if ($isEmailMode) { ?>
    <style>
        .em-wrap,
        .em-wrap table,
        .em-wrap td {
            font-family: Arial, Helvetica, sans-serif;
            color: #111111;
        }

        .em-wrap {
            width: 100%;
            background: #f6f4ed;
            padding: 20px 0;
        }

        .em-card {
            width: 100%;
            max-width: 680px;
            background: #ffffff;
            border: 1px solid #e8e5dd;
            border-radius: 14px;
            overflow: hidden;
        }

        .em-hero {
            background: #1d1d1d;
            color: #ffffff;
            padding: 20px 24px;
        }

        .em-chip {
            display: inline-block;
            border-radius: 20px;
            background: #f1f3f5;
            color: #1f2937;
            padding: 6px 12px;
            font-size: 12px;
            margin: 0 6px 8px 0;
        }

        .em-table {
            width: 100%;
            border-collapse: collapse;
        }

        .em-table td {
            padding: 9px 0;
            border-bottom: 1px solid #eceff3;
            font-size: 14px;
        }

        .em-table td:first-child {
            width: 150px;
            color: #677185;
            font-weight: 700;
        }
    </style>
    <table class="em-wrap" role="presentation" cellspacing="0" cellpadding="0" border="0">
        <tr>
            <td align="center">
                <table class="em-card" role="presentation" cellspacing="0" cellpadding="0" border="0">
                    <?php if ($receiptFound) { ?>
                        <tr>
                            <td class="em-hero">
                                <div
                                    style="font-size:11px;letter-spacing:1.6px;text-transform:uppercase;color:#f3d17a;font-weight:700;">
                                    Appointment Receipt</div>
                                <div style="font-size:24px;font-weight:700;margin-top:6px;">
                                    <?php echo receiptEsc(site::name); ?>
                                </div>
                                <div style="font-size:13px;color:#dbdbdb;margin-top:7px;"><?php echo receiptEsc(site::phone); ?>
                                    | <?php echo receiptEsc(site::address); ?></div>
                                <div style="margin-top:10px;font-size:13px;"><strong>Receipt:</strong>
                                    <?php echo receiptEsc($orderId); ?> <span style="color:#63d397;">(PAID)</span></div>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:24px;">
                                <div style="font-size:20px;font-weight:700;margin-bottom:12px;">
                                    <?php echo receiptEsc($serviceName); ?>
                                </div>
                                <div style="margin-bottom:14px;">
                                    <span class="em-chip">Date: <?php echo receiptEsc($scheduleDate); ?></span>
                                    <span class="em-chip">Time: <?php echo receiptEsc($scheduleTime); ?></span>
                                    <span class="em-chip">Duration: <?php echo receiptEsc($serviceDuration); ?></span>
                                </div>
                                <img src="<?php echo receiptEsc($imageUrl); ?>" alt="<?php echo receiptEsc($serviceName); ?>"
                                    style="width:100%;max-width:620px;border-radius:10px;display:block;background:#f3f3f3;margin:0 0 16px 0;">
                                <table class="em-table" role="presentation" cellspacing="0" cellpadding="0" border="0">
                                    <tr>
                                        <td>Client Name</td>
                                        <td><?php echo receiptEsc($customerName); ?></td>
                                    </tr>
                                    <tr>
                                        <td>Email</td>
                                        <td><?php echo receiptEsc($customerEmail); ?></td>
                                    </tr>
                                    <tr>
                                        <td>Phone</td>
                                        <td><?php echo receiptEsc($customerPhone); ?></td>
                                    </tr>
                                    <tr>
                                        <td>Service Price</td>
                                        <td style="color:#0f9d58;font-weight:700;"><?php echo receiptEsc($servicePrice); ?></td>
                                    </tr>
                                    <tr>
                                        <td>Issued On</td>
                                        <td><?php echo receiptEsc($receiptIssuedOn); ?></td>
                                    </tr>
                                </table>
                                <?php if ($serviceDescription !== "") { ?>
                                    <div
                                        style="margin-top:14px;padding:12px;border:1px solid #e5e7eb;border-radius:8px;font-size:14px;background:#fafafa;">
                                        <strong>Service Notes:</strong> <?php echo receiptEsc($serviceDescription); ?>
                                    </div>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } else { ?>
                        <tr>
                            <td style="padding:26px;text-align:center;">
                                <div style="font-size:22px;font-weight:700;margin-bottom:8px;">Receipt Not Found</div>
                                <div style="font-size:14px;color:#6b7280;">We could not find a paid receipt for this appointment
                                    ID.</div>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </td>
        </tr>
    </table>
    <?php exit;
} ?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1">
    <title>Appointment Receipt | CocoHairSignature</title>
    <style>
        :root {
            --ink: #161616;
            --muted: #667085;
            --paper: #ffffff;
            --gold: #d3b05f;
            --bg-soft: #f8f5ec;
            --line: #eceff3;
            --success: #118e54;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            font-family: "Segoe UI", Tahoma, Arial, sans-serif;
            color: var(--ink);
        }

        body {
            background: linear-gradient(180deg, var(--bg-soft) 0%, #ffffff 100%);
        }

        .rcpt-page {
            max-width: 1100px;
            margin: 0 auto;
            padding: 30px 16px 60px;
        }

        .rcpt-card {
            background: var(--paper);
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 24px 55px rgba(0, 0, 0, .11);
            border: 1px solid #ede9de;
        }

        .rcpt-hero {
            background: linear-gradient(120deg, #111 0%, #232323 55%, #323232 100%);
            color: #ffffff;
            padding: 26px 30px;
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            justify-content: space-between;
            align-items: flex-start;
        }

        .rcpt-label {
            font-size: 12px;
            letter-spacing: 1.8px;
            text-transform: uppercase;
            color: var(--gold);
            font-weight: 700;
        }

        .rcpt-company {
            font-size: 30px;
            font-weight: 700;
            margin: 4px 0 8px;
        }

        .rcpt-contact {
            color: #d4d4d4;
            font-size: 14px;
            line-height: 1.5;
        }

        .rcpt-paid {
            display: inline-block;
            background: #138f56;
            color: #ffffff;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .7px;
            padding: 6px 12px;
            margin-bottom: 8px;
        }

        .rcpt-id-label {
            color: #c9c9c9;
            font-size: 12px;
            margin-bottom: 2px;
        }

        .rcpt-id {
            font-weight: 700;
            font-size: 15px;
        }

        .rcpt-body {
            display: grid;
            grid-template-columns: 350px 1fr;
            gap: 28px;
            padding: 28px;
        }

        .rcpt-image {
            width: 100%;
            border-radius: 12px;
            background: #f3f3f3;
            display: block;
            min-height: 260px;
            object-fit: cover;
        }

        .rcpt-actions {
            margin-top: 12px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .rcpt-btn {
            display: inline-block;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 700;
            font-size: 14px;
            padding: 10px 14px;
            border: 1px solid #d2d6dc;
            color: #111;
            background: #ffffff;
            cursor: pointer;
        }

        .rcpt-btn-gold {
            border-color: #e2b53c;
            background: #f5c856;
            color: #111;
        }

        .rcpt-title {
            margin: 0 0 14px;
            font-size: 24px;
            line-height: 1.3;
        }

        .rcpt-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 9px;
            margin-bottom: 14px;
        }

        .rcpt-chip {
            display: inline-block;
            border-radius: 999px;
            padding: 7px 12px;
            font-size: 12px;
            font-weight: 700;
            background: #f1f3f5;
            color: #1f2937;
        }

        .rcpt-table {
            width: 100%;
            border-collapse: collapse;
        }

        .rcpt-table td {
            padding: 11px 0;
            border-bottom: 1px solid var(--line);
            font-size: 15px;
        }

        .rcpt-table td:first-child {
            width: 165px;
            color: var(--muted);
            font-weight: 700;
        }

        .rcpt-price {
            color: var(--success);
            font-weight: 800;
        }

        .rcpt-note {
            margin-top: 14px;
            padding: 12px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            background: #fafafa;
            font-size: 14px;
            line-height: 1.5;
        }

        .rcpt-empty {
            text-align: center;
            padding: 42px 20px;
        }

        .rcpt-empty h1 {
            margin: 0 0 8px;
            font-size: 28px;
        }

        .rcpt-empty p {
            margin: 0 0 18px;
            color: var(--muted);
        }

        @media (max-width: 860px) {
            .rcpt-company {
                font-size: 23px;
            }

            .rcpt-body {
                grid-template-columns: 1fr;
                padding: 18px;
                gap: 20px;
            }

            .rcpt-table td,
            .rcpt-table td:first-child {
                display: block;
                width: 100%;
                padding: 7px 0;
            }

            .rcpt-table td:first-child {
                padding-top: 14px;
                border-bottom: 0;
            }
        }

        @media print {
            body {
                background: #ffffff;
            }

            .rcpt-page {
                max-width: 100%;
                padding: 0;
            }

            .rcpt-card {
                box-shadow: none;
                border-radius: 0;
            }

            .rcpt-actions {
                display: none;
            }
        }
    </style>
</head>

<body>
    <main class="rcpt-page">
        <section class="rcpt-card">
            <?php if ($receiptFound) { ?>
                <header class="rcpt-hero">
                    <div>
                        <div class="rcpt-label">Appointment Receipt</div>
                        <div class="rcpt-company"><?php echo receiptEsc(site::name); ?></div>
                        <div class="rcpt-contact"><?php echo receiptEsc(site::phone); ?> |
                            <?php echo receiptEsc(site::address); ?>
                        </div>
                    </div>
                    <div>
                        <span class="rcpt-paid">PAID</span>
                        <div class="rcpt-id-label">Receipt No.</div>
                        <div class="rcpt-id"><?php echo receiptEsc($orderId); ?></div>
                    </div>
                </header>
                <div class="rcpt-body">
                    <div>
                        <img class="rcpt-image" src="<?php echo receiptEsc($imageUrl); ?>"
                            alt="<?php echo receiptEsc($serviceName); ?>">
                        <div class="rcpt-actions">
                            <button type="button" class="rcpt-btn" onclick="window.print()">Print Receipt</button>
                            <a href="<?php echo site::url_hostdir(); ?>/pages/hairlist.php"
                                class="rcpt-btn rcpt-btn-gold">Book
                                Again</a>
                        </div>
                    </div>
                    <div>
                        <h1 class="rcpt-title"><?php echo receiptEsc($serviceName); ?></h1>
                        <div class="rcpt-chips">
                            <span class="rcpt-chip">Date: <?php echo receiptEsc($scheduleDate); ?></span>
                            <span class="rcpt-chip">Time: <?php echo receiptEsc($scheduleTime); ?></span>
                            <span class="rcpt-chip">Duration: <?php echo receiptEsc($serviceDuration); ?></span>
                        </div>
                        <table class="rcpt-table">
                            <tbody>
                                <tr>
                                    <td>Client Name</td>
                                    <td><?php echo receiptEsc($customerName); ?></td>
                                </tr>
                                <tr>
                                    <td>Email</td>
                                    <td><?php echo receiptEsc($customerEmail); ?></td>
                                </tr>
                                <tr>
                                    <td>Phone</td>
                                    <td><?php echo receiptEsc($customerPhone); ?></td>
                                </tr>
                                <tr>
                                    <td>Service Price</td>
                                    <td class="rcpt-price"><?php echo receiptEsc($servicePrice); ?></td>
                                </tr>
                                <tr>
                                    <td>Issued On</td>
                                    <td><?php echo receiptEsc($receiptIssuedOn); ?></td>
                                </tr>
                            </tbody>
                        </table>
                        <?php if ($serviceDescription !== "") { ?>
                            <div class="rcpt-note">
                                <strong>Service Notes:</strong> <?php echo receiptEsc($serviceDescription); ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            <?php } else if ($isRedirectedFromStripe == true) { ?>
                    <div class="rcpt-empty" style="padding: 80px 20px;">
                        <div style="text-align: center; margin-bottom: 20px;">
                            <div style="height: 95px; width: 95px; display: inline-block;">
                                <svg style="width: 100%; height: 100%; animation: spin 1s linear infinite;"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
                                    <circle cx="50" cy="50" fill="none" stroke="#d3b05f" stroke-width="4" r="35"
                                        stroke-dasharray="164.93361431346412 56.97787143782138"
                                        style="transform-origin: 50px 50px; animation: rotate 1s linear infinite;"></circle>
                                </svg>
                            </div>
                        </div>
                        <h1 style="margin: 20px 0 8px; font-size: 28px; color: #161616;">Processing Your Payment</h1>
                        <p style="margin: 0 0 18px; color: #667085; font-size: 16px;">Please wait while we confirm your
                            appointment...</p>
                    </div>
                    <style>
                        @keyframes rotate {
                            100% {
                                transform: rotate(360deg);
                            }
                        }

                        @keyframes spin {
                            0% {
                                opacity: 1;
                            }

                            100% {
                                opacity: 1;
                            }
                        }
                    </style>
                    <script>
                        (function () {
                            setTimeout(function () {
                                window.location.reload();
                            }, 3000);
                        })();
                    </script>
            <?php } else { ?>
                    <div class="rcpt-empty">
                        <h1>Receipt Not Found</h1>
                        <p>We could not find a paid receipt for this appointment ID.</p>
                        <a href="<?php echo site::url_hostdir(); ?>/pages/hairlist.php" class="rcpt-btn rcpt-btn-gold">Book
                            Appointment</a>
                        <a href="#" class="rcpt-btn" onclick="window.location.reload();return false;">Refresh Receipt</a>
                    </div>
            <?php } ?>
        </section>
    </main>
</body>

</html>