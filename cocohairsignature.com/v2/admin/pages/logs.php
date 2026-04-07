<?php
include("../config.php");

function adminEsc($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
}

$events = [];
$sql = "SELECT
    pp.id_gen,
    pp.customername,
    pp.haspaid,
    pp.date_created,
    pp.date_updated
FROM product_purchased pp
ORDER BY pp.date_updated DESC
LIMIT 200";

$result = $db->query($sql);
if ($result instanceof mysqli_result) {
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Activity | Admin</title>
    <?php include("../template/_head.php"); ?>
</head>
<body>
    <?php include("../template/_header.php"); ?>

    <section class="admin-section mb-3">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <h2 class="admin-section-title mb-0">Activity Stream</h2>
            <input class="form-control" style="max-width: 320px;" type="search" placeholder="Search activity..."
                data-table-filter data-table-target="#activityTable">
        </div>

        <div class="table-responsive">
            <table class="table table-admin align-middle" id="activityTable">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Updated</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($events) === 0) { ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted-soft py-4">No activity found.</td>
                        </tr>
                    <?php } ?>
                    <?php foreach ($events as $event) { ?>
                        <?php $isPaid = ((int) ($event["haspaid"] ?? 0) === 1); ?>
                        <tr>
                            <td class="fw-semibold"><?php echo adminEsc($event["id_gen"]); ?></td>
                            <td><?php echo adminEsc($event["customername"]); ?></td>
                            <td>
                                <span class="badge badge-status <?php echo $isPaid ? "text-bg-success" : "text-bg-warning"; ?>">
                                    <?php echo $isPaid ? "Paid" : "Pending"; ?>
                                </span>
                            </td>
                            <td class="small text-muted-soft"><?php echo adminEsc($event["date_created"]); ?></td>
                            <td class="small text-muted-soft"><?php echo adminEsc($event["date_updated"]); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </section>

    <?php include("../template/_footer.php"); ?>
</body>
</html>
