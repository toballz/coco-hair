<?php
include("../config.php");

const PRODUCT_PREPAID_AMOUNT = 50;

function adminEsc($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
}

function adminMoney($value): string
{
    return "$" . number_format((float) $value, 2);
}

$overview = [
    "paid_orders" => 0,
    "pending_orders" => 0,
    "products_sold" => 0,
    "prepaid_revenue" => 0.00,
];

$overviewSql = "SELECT
    SUM(CASE WHEN pp.haspaid = 1 THEN 1 ELSE 0 END) AS paid_orders,
    SUM(CASE WHEN pp.haspaid = 0 THEN 1 ELSE 0 END) AS pending_orders,
    COUNT(DISTINCT CASE WHEN pp.haspaid = 1 THEN pl.id_ai END) AS products_sold
FROM product_purchased pp
LEFT JOIN product_variant pv ON pv.id_ai = pp.product_variant_id_ref
LEFT JOIN product_lists pl ON pl.id_ai = pv.product_list_id_ref";

$overviewResult = $db->query($overviewSql);
if ($overviewResult instanceof mysqli_result && $row = $overviewResult->fetch_assoc()) {
    $overview["paid_orders"] = (int) ($row["paid_orders"] ?? 0);
    $overview["pending_orders"] = (int) ($row["pending_orders"] ?? 0);
    $overview["products_sold"] = (int) ($row["products_sold"] ?? 0);
    $overview["prepaid_revenue"] = $overview["paid_orders"] * PRODUCT_PREPAID_AMOUNT;
}

$topProducts = [];
$topProductsSql = "SELECT
    pl.id_ai AS product_id,
    pl.hair_name,
    pv.name AS variant_name,
    pc.category_name,
    SUM(CASE WHEN pp.haspaid = 1 THEN 1 ELSE 0 END) AS paid_orders
FROM product_purchased pp
LEFT JOIN product_variant pv ON pv.id_ai = pp.product_variant_id_ref
LEFT JOIN product_lists pl ON pl.id_ai = pv.product_list_id_ref
LEFT JOIN product_category pc ON pc.id_ai = pl.category
GROUP BY pl.id_ai, pl.hair_name, pv.name, pc.category_name
HAVING paid_orders > 0
ORDER BY paid_orders DESC, pl.hair_name ASC, pv.name ASC
LIMIT 12";

$topProductsResult = $db->query($topProductsSql);
if ($topProductsResult instanceof mysqli_result) {
    while ($row = $topProductsResult->fetch_assoc()) {
        $row["prepaid_revenue"] = ((int) ($row["paid_orders"] ?? 0)) * PRODUCT_PREPAID_AMOUNT;
        $topProducts[] = $row;
    }
}

$categoryStats = [];
$categoryStatsSql = "SELECT
    pc.category_name,
    SUM(CASE WHEN pp.haspaid = 1 THEN 1 ELSE 0 END) AS paid_orders
FROM product_purchased pp
LEFT JOIN product_variant pv ON pv.id_ai = pp.product_variant_id_ref
LEFT JOIN product_lists pl ON pl.id_ai = pv.product_list_id_ref
LEFT JOIN product_category pc ON pc.id_ai = pl.category
GROUP BY pc.category_name
HAVING paid_orders > 0
ORDER BY paid_orders DESC, pc.category_name ASC";

$categoryStatsResult = $db->query($categoryStatsSql);
if ($categoryStatsResult instanceof mysqli_result) {
    while ($row = $categoryStatsResult->fetch_assoc()) {
        $row["prepaid_revenue"] = ((int) ($row["paid_orders"] ?? 0)) * PRODUCT_PREPAID_AMOUNT;
        $categoryStats[] = $row;
    }
}

$recentPaidOrders = [];
$recentPaidOrdersSql = "SELECT
    pp.id_gen,
    pp.customername,
    pp.date_created,
    pl.hair_name,
    pv.name AS variant_name
FROM product_purchased pp
LEFT JOIN product_variant pv ON pv.id_ai = pp.product_variant_id_ref
LEFT JOIN product_lists pl ON pl.id_ai = pv.product_list_id_ref
WHERE pp.haspaid = 1
ORDER BY pp.date_created DESC
LIMIT 8";

$recentPaidOrdersResult = $db->query($recentPaidOrdersSql);
if ($recentPaidOrdersResult instanceof mysqli_result) {
    while ($row = $recentPaidOrdersResult->fetch_assoc()) {
        $recentPaidOrders[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Product Stats | Admin</title>
    <?php include("../template/_head.php"); ?>
</head>

<body>
    <?php include("../template/_header.php"); ?>

    <section class="row g-3 mb-3">
        <div class="col-12 col-md-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-label">Paid Orders</div>
                <div class="stat-value"><?php echo number_format($overview["paid_orders"]); ?></div>
                <div class="stat-note">Completed prepaid bookings</div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-label">Pending Orders</div>
                <div class="stat-value"><?php echo number_format($overview["pending_orders"]); ?></div>
                <div class="stat-note">Awaiting payment</div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-label">Products Sold</div>
                <div class="stat-value"><?php echo number_format($overview["products_sold"]); ?></div>
                <div class="stat-note">Distinct paid products</div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-label">Prepaid Revenue</div>
                <div class="stat-value"><?php echo adminEsc(adminMoney($overview["prepaid_revenue"])); ?></div>
                <div class="stat-note">$50 per paid order</div>
            </div>
        </div>
    </section>

    <section class="admin-section mb-3">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <h2 class="admin-section-title mb-0">Top Products</h2>
            <span class="text-muted-soft small">Revenue uses fixed $50 prepay</span>
        </div>

        <div class="table-responsive">
            <table class="table table-admin align-middle">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Paid Orders</th>
                        <th>Prepaid Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($topProducts) === 0) { ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted-soft py-4">No paid product data found.</td>
                        </tr>
                    <?php } ?>
                    <?php foreach ($topProducts as $product) { ?>
                        <?php
                        $productName = trim((string) ($product["hair_name"] ?? ""));
                        $variantName = trim((string) ($product["variant_name"] ?? ""));
                        if ($variantName !== "") {
                            $productName .= ($productName !== "" ? " - " : "") . $variantName;
                        }
                        if ($productName === "") {
                            $productName = "Unknown product";
                        }
                        ?>
                        <tr>
                            <td>
                                <div class="fw-semibold"><?php echo adminEsc($productName); ?></div>
                                <div class="small text-muted-soft">ID: <?php echo adminEsc($product["product_id"]); ?></div>
                            </td>
                            <td><?php echo adminEsc($product["category_name"] ?: "Uncategorized"); ?></td>
                            <td><?php echo number_format((int) ($product["paid_orders"] ?? 0)); ?></td>
                            <td><?php echo adminEsc(adminMoney($product["prepaid_revenue"] ?? 0)); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="row g-3">
        <div class="col-12 col-xl-5">
            <div class="admin-section h-100">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                    <h2 class="admin-section-title mb-0">Category Performance</h2>
                    <span class="text-muted-soft small">Paid orders only</span>
                </div>

                <div class="table-responsive">
                    <table class="table table-admin align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th>Orders</th>
                                <th>Prepaid Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($categoryStats) === 0) { ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted-soft py-4">No category data found.</td>
                                </tr>
                            <?php } ?>
                            <?php foreach ($categoryStats as $category) { ?>
                                <tr>
                                    <td><?php echo adminEsc($category["category_name"] ?: "Uncategorized"); ?></td>
                                    <td><?php echo number_format((int) ($category["paid_orders"] ?? 0)); ?></td>
                                    <td><?php echo adminEsc(adminMoney($category["prepaid_revenue"] ?? 0)); ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-7">
            <div class="admin-section h-100">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                    <h2 class="admin-section-title mb-0">Recent Paid Orders</h2>
                    <span class="text-muted-soft small">$50 prepaid each</span>
                </div>

                <div class="table-responsive">
                    <table class="table table-admin align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Product</th>
                                <th>Prepaid</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($recentPaidOrders) === 0) { ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted-soft py-4">No paid orders found.</td>
                                </tr>
                            <?php } ?>
                            <?php foreach ($recentPaidOrders as $order) { ?>
                                <?php
                                $orderProductName = trim((string) ($order["hair_name"] ?? ""));
                                $orderVariantName = trim((string) ($order["variant_name"] ?? ""));
                                if ($orderVariantName !== "") {
                                    $orderProductName .= ($orderProductName !== "" ? " - " : "") . $orderVariantName;
                                }
                                if ($orderProductName === "") {
                                    $orderProductName = "Unknown product";
                                }
                                ?>
                                <tr>
                                    <td class="fw-semibold"><?php echo adminEsc($order["id_gen"]); ?></td>
                                    <td>
                                        <div><?php echo adminEsc($order["customername"] ?: "Unknown customer"); ?></div>
                                        <div class="small text-muted-soft"><?php echo adminEsc($order["date_created"]); ?></div>
                                    </td>
                                    <td><?php echo adminEsc($orderProductName); ?></td>
                                    <td><?php echo adminEsc(adminMoney(PRODUCT_PREPAID_AMOUNT)); ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <?php include("../template/_footer.php"); ?>
</body>

</html>
