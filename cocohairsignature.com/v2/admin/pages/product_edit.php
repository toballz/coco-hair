<?php
include("../config.php");

function adminEsc($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
}

function adminNormalizeImageJson(string $raw): array
{
    $raw = trim($raw);
    if ($raw === "") {
        return [];
    }

    $decoded = json_decode($raw, true);
    if (is_array($decoded)) {
        return array_values(array_filter(array_map(
            static function ($value): string {
                return trim((string) $value);
            },
            $decoded
        ), static function ($value): bool {
            return $value !== "";
        }));
    }

    return [];
}

$flashSuccess = "";
$errors = [];

$categories = [];
$categoryResult = $db->query("SELECT id_ai, category_name FROM product_category ORDER BY category_name ASC");
if ($categoryResult instanceof mysqli_result) {
    while ($row = $categoryResult->fetch_assoc()) {
        $categories[] = $row;
    }
}

$products = [];
$productListSql = "SELECT
    p.id_ai,
    p.hair_name,
    p.category,
    c.category_name
FROM product_lists p
LEFT JOIN product_category c ON c.id_ai = p.category
ORDER BY c.category_name ASC, p.hair_name ASC, p.id_ai ASC";

$productListResult = $db->query($productListSql);
if ($productListResult instanceof mysqli_result) {
    while ($row = $productListResult->fetch_assoc()) {
        $products[] = $row;
    }
}

$selectedProductId = (int) ($_GET["product_id"] ?? $_POST["product_id"] ?? 0);
if ($selectedProductId <= 0 && count($products) > 0) {
    $selectedProductId = (int) ($products[0]["id_ai"] ?? 0);
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["save_product"]) && $selectedProductId > 0) {
    $categoryId = (int) ($_POST["category_id"] ?? 0);
    $hairName = trim((string) ($_POST["hair_name"] ?? ""));
    $description = trim((string) ($_POST["description"] ?? ""));
    $timeRange = trim((string) ($_POST["time_range"] ?? ""));
    $hairImagesRaw = trim((string) ($_POST["hair_images"] ?? ""));
    $variantIds = $_POST["variant_id"] ?? [];
    $variantNames = $_POST["variant_name"] ?? [];
    $variantDescriptions = $_POST["variant_description"] ?? [];
    $variantPrices = $_POST["variant_price"] ?? [];

    if ($categoryId <= 0) {
        $errors[] = "Choose a category.";
    }
    if ($hairName === "") {
        $errors[] = "Product name is required.";
    }

    $imageJson = adminNormalizeImageJson($hairImagesRaw);
    if ($hairImagesRaw !== "" && $imageJson === []) {
        $errors[] = "Hair images must be a JSON array like [\"12\", \"13\"].";
    }

    $variantPayload = [];
    foreach ($variantIds as $index => $variantIdRaw) {
        $variantId = (int) $variantIdRaw;
        if ($variantId <= 0) {
            continue;
        }

        $variantName = trim((string) ($variantNames[$index] ?? ""));
        $variantDescription = trim((string) ($variantDescriptions[$index] ?? ""));
        $variantPriceRaw = trim((string) ($variantPrices[$index] ?? ""));

        if ($variantName === "") {
            $errors[] = "Variant name is required for variant #" . $variantId . ".";
        }

        if ($variantPriceRaw !== "" && !is_numeric($variantPriceRaw)) {
            $errors[] = "Variant price must be numeric for variant #" . $variantId . ".";
        }

        $variantPayload[] = [
            "id" => $variantId,
            "name" => $variantName,
            "description" => $variantDescription,
            "price" => ($variantPriceRaw !== "" && is_numeric($variantPriceRaw)) ? number_format((float) $variantPriceRaw, 2, ".", "") : "0.00",
        ];
    }

    if (count($errors) === 0) {
        $safeHairName = mysqli_real_escape_string($db, $hairName);
        $safeDescription = mysqli_real_escape_string($db, $description);
        $safeTimeRange = mysqli_real_escape_string($db, $timeRange);
        $safeImages = mysqli_real_escape_string($db, json_encode($imageJson, JSON_UNESCAPED_SLASHES));

        $ok = $db->query("UPDATE product_lists SET
            category = '$categoryId',
            hair_name = '$safeHairName',
            description = '$safeDescription',
            time_range = '$safeTimeRange',
            hair_images = '$safeImages'
            WHERE id_ai = '$selectedProductId'
            LIMIT 1");

        if ($ok) {
            foreach ($variantPayload as $variant) {
                $safeVariantName = mysqli_real_escape_string($db, $variant["name"]);
                $safeVariantDescription = mysqli_real_escape_string($db, $variant["description"]);
                $safeVariantPrice = mysqli_real_escape_string($db, $variant["price"]);

                $variantOk = $db->query("UPDATE product_variant SET
                    name = '$safeVariantName',
                    description = '$safeVariantDescription',
                    price = '$safeVariantPrice'
                    WHERE id_ai = '" . (int) $variant["id"] . "'
                    AND product_list_id_ref = '$selectedProductId'
                    LIMIT 1");

                if (!$variantOk) {
                    $errors[] = "Failed to update variant #" . (int) $variant["id"] . ".";
                    break;
                }
            }
        } else {
            $errors[] = "Failed to update product details.";
        }

        if (count($errors) === 0) {
            $flashSuccess = "Product updated successfully.";
        }
    }
}

$selectedProduct = null;
$variants = [];

if ($selectedProductId > 0) {
    $productSql = "SELECT
        p.id_ai,
        p.category,
        p.hair_name,
        p.description,
        p.time_range,
        p.hair_images,
        c.category_name
    FROM product_lists p
    LEFT JOIN product_category c ON c.id_ai = p.category
    WHERE p.id_ai = '$selectedProductId'
    LIMIT 1";

    $productResult = $db->query($productSql);
    if ($productResult instanceof mysqli_result && $productResult->num_rows === 1) {
        $selectedProduct = $productResult->fetch_assoc();
    }

    $variantSql = "SELECT
        id_ai,
        name,
        price,
        description
    FROM product_variant
    WHERE product_list_id_ref = '$selectedProductId'
    ORDER BY id_ai ASC";

    $variantResult = $db->query($variantSql);
    if ($variantResult instanceof mysqli_result) {
        while ($row = $variantResult->fetch_assoc()) {
            $variants[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Product Edit | Admin</title>
    <?php include("../template/_head.php"); ?>
    <style>
        .product-list-panel {
            border: 1px solid var(--admin-border);
            border-radius: 14px;
            background: #fff;
            overflow: hidden;
        }

        .product-list-item {
            display: block;
            padding: 12px 14px;
            border-bottom: 1px solid var(--admin-border);
            color: inherit;
            text-decoration: none;
        }

        .product-list-item:last-child {
            border-bottom: 0;
        }

        .product-list-item.active {
            background: #111827;
            color: #fff;
        }

        .variant-card {
            border: 1px solid var(--admin-border);
            border-radius: 12px;
            padding: 14px;
            background: #fff;
            height: 100%;
        }
    </style>
</head>

<body>
    <?php include("../template/_header.php"); ?>

    <section class="admin-section mb-3">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h2 class="admin-section-title mb-1">Product Editor</h2>
                <p class="text-muted-soft mb-0">Update product details and existing variants from the current catalog tables.</p>
            </div>
            <span class="badge text-bg-light border">Prepaid products: $50 collected at checkout</span>
        </div>
    </section>

    <?php if ($flashSuccess !== "") { ?>
        <div class="alert alert-success"><?php echo adminEsc($flashSuccess); ?></div>
    <?php } ?>

    <?php if (count($errors) > 0) { ?>
        <div class="alert alert-danger">
            <div class="fw-semibold mb-1">Please fix the following:</div>
            <ul class="mb-0">
                <?php foreach ($errors as $error) { ?>
                    <li><?php echo adminEsc($error); ?></li>
                <?php } ?>
            </ul>
        </div>
    <?php } ?>

    <section class="row g-3">
        <div class="col-12 col-xl-4">
            <div class="admin-section">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                    <h3 class="h5 mb-0">Products</h3>
                    <span class="text-muted-soft small"><?php echo number_format(count($products)); ?> total</span>
                </div>

                <div class="product-list-panel">
                    <?php if (count($products) === 0) { ?>
                        <div class="p-3 text-muted-soft">No products found.</div>
                    <?php } ?>
                    <?php foreach ($products as $product) { ?>
                        <?php $isActive = ((int) $product["id_ai"] === $selectedProductId); ?>
                        <a class="product-list-item <?php echo $isActive ? "active" : ""; ?>"
                            href="pages/product_edit.php?product_id=<?php echo (int) $product["id_ai"]; ?>">
                            <div class="fw-semibold"><?php echo adminEsc($product["hair_name"] ?: "Untitled product"); ?></div>
                            <div class="small <?php echo $isActive ? "text-white-50" : "text-muted-soft"; ?>">
                                <?php echo adminEsc($product["category_name"] ?: "Uncategorized"); ?> · ID <?php echo (int) $product["id_ai"]; ?>
                            </div>
                        </a>
                    <?php } ?>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-8">
            <div class="admin-section">
                <?php if (!$selectedProduct) { ?>
                    <div class="text-muted-soft">Select a product to edit.</div>
                <?php } else { ?>
                    <form method="post">
                        <input type="hidden" name="save_product" value="1">
                        <input type="hidden" name="product_id" value="<?php echo (int) $selectedProduct["id_ai"]; ?>">

                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="category_id">Category</label>
                                <select class="form-select" id="category_id" name="category_id">
                                    <option value="0">Select category</option>
                                    <?php foreach ($categories as $category) { ?>
                                        <?php $isSelected = ((int) $category["id_ai"] === (int) ($selectedProduct["category"] ?? 0)); ?>
                                        <option value="<?php echo (int) $category["id_ai"]; ?>" <?php echo $isSelected ? "selected" : ""; ?>>
                                            <?php echo adminEsc($category["category_name"]); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label" for="hair_name">Product Name</label>
                                <input class="form-control" id="hair_name" name="hair_name"
                                    value="<?php echo adminEsc($selectedProduct["hair_name"] ?? ""); ?>">
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label" for="time_range">Time Range</label>
                                <input class="form-control" id="time_range" name="time_range"
                                    value="<?php echo adminEsc($selectedProduct["time_range"] ?? ""); ?>"
                                    placeholder="Example: 3 hours">
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label" for="hair_images">Hair Images JSON</label>
                                <input class="form-control" id="hair_images" name="hair_images"
                                    value="<?php echo adminEsc($selectedProduct["hair_images"] ?? "[]"); ?>"
                                    placeholder='["12","13"]'>
                            </div>

                            <div class="col-12">
                                <label class="form-label" for="description">Product Description</label>
                                <textarea class="form-control" id="description" name="description" rows="5"><?php
                                    echo adminEsc($selectedProduct["description"] ?? "");
                                ?></textarea>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-4 mb-3">
                            <h3 class="h5 mb-0">Variants</h3>
                            <span class="text-muted-soft small"><?php echo number_format(count($variants)); ?> existing</span>
                        </div>

                        <div class="row g-3">
                            <?php if (count($variants) === 0) { ?>
                                <div class="col-12">
                                    <div class="variant-card text-muted-soft">No variants found for this product.</div>
                                </div>
                            <?php } ?>
                            <?php foreach ($variants as $index => $variant) { ?>
                                <div class="col-12">
                                    <div class="variant-card">
                                        <input type="hidden" name="variant_id[<?php echo $index; ?>]"
                                            value="<?php echo (int) $variant["id_ai"]; ?>">

                                        <div class="row g-3">
                                            <div class="col-12 col-md-5">
                                                <label class="form-label">Variant Name</label>
                                                <input class="form-control" name="variant_name[<?php echo $index; ?>]"
                                                    value="<?php echo adminEsc($variant["name"] ?? ""); ?>">
                                            </div>

                                            <div class="col-12 col-md-3">
                                                <label class="form-label">Price</label>
                                                <input class="form-control" name="variant_price[<?php echo $index; ?>]"
                                                    value="<?php echo adminEsc($variant["price"] ?? "0.00"); ?>">
                                            </div>

                                            <div class="col-12 col-md-4">
                                                <label class="form-label">Variant ID</label>
                                                <input class="form-control" value="<?php echo (int) $variant["id_ai"]; ?>" readonly>
                                            </div>

                                            <div class="col-12">
                                                <label class="form-label">Variant Description</label>
                                                <textarea class="form-control"
                                                    name="variant_description[<?php echo $index; ?>]" rows="3"><?php
                                                        echo adminEsc($variant["description"] ?? "");
                                                    ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>

                        <div class="d-flex flex-wrap gap-2 mt-4">
                            <button type="submit" class="btn btn-dark">
                                <i class="bi bi-save me-1"></i> Save Product
                            </button>
                            <a class="btn btn-outline-secondary"
                                href="<?php echo site::url_hostdir(); ?>/pages/hairlist.php" target="_blank" rel="noopener noreferrer">
                                View Public Catalog
                            </a>
                        </div>
                    </form>
                <?php } ?>
            </div>
        </div>
    </section>

    <?php include("../template/_footer.php"); ?>
</body>

</html>
