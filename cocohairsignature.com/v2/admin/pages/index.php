<?php
include("../config.php");

function adminEsc($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
}

function adminFormatDate(string $ymd): string
{
    $ymd = trim($ymd);
    if (!preg_match("/^\d{8}$/", $ymd)) {
        return $ymd !== "" ? $ymd : "N/A";
    }
    $d = DateTime::createFromFormat("Ymd", $ymd);
    return ($d instanceof DateTime) ? $d->format("M j, Y") : $ymd;
}

function adminFormatTime($value): string
{
    $raw = trim((string) $value);
    if ($raw === "") {
        return "N/A";
    }
    if (preg_match("/^\d{3,4}$/", $raw)) {
        $raw = str_pad($raw, 4, "0", STR_PAD_LEFT);
        $d = DateTime::createFromFormat("Hi", $raw);
        if ($d instanceof DateTime) {
            return $d->format("g:i A");
        }
    }
    return $raw;
}

$todayYmd = (new DateTime("today"))->format("Ymd");
$stats = [
    "paid_total" => 0,
    "pending_total" => 0,
    "upcoming_paid" => 0,
    "revenue_total" => 0.00,
];

$statSql = "SELECT
    SUM(CASE WHEN haspaid = 1 THEN 1 ELSE 0 END) AS paid_total,
    SUM(CASE WHEN haspaid = 0 THEN 1 ELSE 0 END) AS pending_total,
    SUM(CASE WHEN haspaid = 1 AND date_scheduled >= '$todayYmd' THEN 1 ELSE 0 END) AS upcoming_paid,
    COALESCE(SUM(CASE WHEN pp.haspaid = 1 THEN pv.price ELSE 0 END), 0) AS revenue_total
FROM product_purchased pp
LEFT JOIN product_variant pv ON pv.id_ai = pp.product_variant_id_ref";

$statResult = $db->query($statSql);
if ($statResult instanceof mysqli_result && $statRow = $statResult->fetch_assoc()) {
    $stats["paid_total"] = (int) ($statRow["paid_total"] ?? 0);
    $stats["pending_total"] = (int) ($statRow["pending_total"] ?? 0);
    $stats["upcoming_paid"] = (int) ($statRow["upcoming_paid"] ?? 0);
    $stats["revenue_total"] = (float) ($statRow["revenue_total"] ?? 0);
}

$recentBookings = [];
$recentSql = "SELECT
    pp.id_gen,
    pp.customername,
    pp.email,
    pp.date_scheduled,
    pp.time_scheduled,
    pp.haspaid,
    pp.date_created,
    pc.category_name,
    pl.hair_name,
    pv.name AS variant_name,
    pv.price
FROM product_purchased pp
LEFT JOIN product_variant pv ON pv.id_ai = pp.product_variant_id_ref
LEFT JOIN product_lists pl ON pl.id_ai = pv.product_list_id_ref
LEFT JOIN product_category pc ON pc.id_ai = pl.category
ORDER BY pp.date_created DESC
LIMIT 8";

$recentResult = $db->query($recentSql);
if ($recentResult instanceof mysqli_result) {
    while ($row = $recentResult->fetch_assoc()) {
        $recentBookings[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Dashboard</title>
    <?php include("../template/_head.php"); ?>
</head>
<body>
    <?php include("../template/_header.php"); ?>

    <section class="row g-3 mb-3">
        <div class="col-12 col-md-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-label">Paid Appointments</div>
                <div class="stat-value"><?php echo number_format($stats["paid_total"]); ?></div>
                <div class="stat-note">All confirmed bookings</div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-label">Pending Payments</div>
                <div class="stat-value"><?php echo number_format($stats["pending_total"]); ?></div>
                <div class="stat-note">Awaiting checkout completion</div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-label">Upcoming Paid</div>
                <div class="stat-value"><?php echo number_format($stats["upcoming_paid"]); ?></div>
                <div class="stat-note">Scheduled from today onward</div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-label">Revenue (Paid)</div>
                <div class="stat-value">$<?php echo number_format($stats["revenue_total"], 2); ?></div>
                <div class="stat-note">Derived from booked variants</div>
            </div>
        </div>
    </section>

    <section class="admin-section mb-3 quick-actions">
        <h2 class="admin-section-title">Quick Actions</h2>
        <div class="d-flex flex-wrap gap-2">
            <a class="btn btn-dark" href="pages/users.php"><i class="bi bi-people me-1"></i> Open Bookings</a>
            <a class="btn btn-outline-dark" href="pages/logs.php"><i class="bi bi-journal-text me-1"></i> View Activity</a>
            <a class="btn btn-outline-dark" href="pages/settings.php"><i class="bi bi-sliders me-1"></i> Site Settings</a>
            <a class="btn btn-outline-dark" href="<?php echo site::url_hostdir(); ?>/pages/hairlist.php" target="_blank"
                rel="noopener noreferrer"><i class="bi bi-box-arrow-up-right me-1"></i> Open Public Site</a>
        </div>
    </section>

    <section class="admin-section">
        <div class="d-flex flex-wrap justify-content-between gap-2 mb-2">
            <h2 class="admin-section-title mb-0">Recent Bookings</h2>
            <a class="btn btn-sm btn-outline-secondary" href="pages/users.php">See all</a>
        </div>
        <div class="table-responsive">
            <table class="table table-admin align-middle">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Client</th>
                        <th>Service</th>
                        <th>Schedule</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($recentBookings) === 0) { ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted-soft py-4">No bookings found.</td>
                        </tr>
                    <?php } ?>
                    <?php foreach ($recentBookings as $booking) { ?>
                        <?php
                        $serviceName = trim(
                            ($booking["category_name"] ?? "") .
                            (($booking["category_name"] ?? "") !== "" ? " - " : "") .
                            ($booking["hair_name"] ?? "") .
                            (($booking["variant_name"] ?? "") !== "" ? " - " . $booking["variant_name"] : "")
                        );
                        if ($serviceName === "") {
                            $serviceName = "Unknown service";
                        }
                        $isPaid = ((int) ($booking["haspaid"] ?? 0) === 1);
                        ?>
                        <tr>
                            <td class="fw-semibold"><?php echo adminEsc($booking["id_gen"]); ?></td>
                            <td>
                                <div class="fw-semibold"><?php echo adminEsc($booking["customername"]); ?></div>
                                <div class="small text-muted-soft"><?php echo adminEsc($booking["email"]); ?></div>
                            </td>
                            <td><?php echo adminEsc($serviceName); ?></td>
                            <td>
                                <div><?php echo adminEsc(adminFormatDate((string) ($booking["date_scheduled"] ?? ""))); ?></div>
                                <div class="small text-muted-soft"><?php echo adminEsc(adminFormatTime($booking["time_scheduled"] ?? "")); ?></div>
                            </td>
                            <td>$<?php echo number_format((float) ($booking["price"] ?? 0), 2); ?></td>
                            <td>
                                <span class="badge badge-status <?php echo $isPaid ? "text-bg-success" : "text-bg-warning"; ?>">
                                    <?php echo $isPaid ? "Paid" : "Pending"; ?>
                                </span>
                            </td>
                            <td>
                                <a class="btn btn-sm btn-outline-secondary"
                                    href="<?php echo site::url_hostdir() . "/pages/receipt.php?orderId=" . urlencode((string) $booking["id_gen"]); ?>"
                                    target="_blank" rel="noopener noreferrer">
                                    Receipt
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </section>

    <?php include("../template/_footer.php"); ?>
</body>
</html>
