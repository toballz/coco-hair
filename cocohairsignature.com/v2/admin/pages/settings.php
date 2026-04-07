<?php
include("../config.php");

function adminEsc($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Settings | Admin</title>
    <?php include("../template/_head.php"); ?>
</head>
<body>
    <?php include("../template/_header.php"); ?>

    <section class="admin-section mb-3">
        <h2 class="admin-section-title">Site Settings</h2>
        <p class="text-muted-soft mb-3">Read-only overview of current configured values.</p>

        <div class="row g-3">
            <div class="col-12 col-lg-6">
                <label class="form-label">Site Name</label>
                <input class="form-control" value="<?php echo adminEsc(site::name); ?>" readonly>
            </div>
            <div class="col-12 col-lg-6">
                <label class="form-label">Site URL</label>
                <input class="form-control" value="<?php echo adminEsc(site::url_hostdir()); ?>" readonly>
            </div>
            <div class="col-12 col-lg-6">
                <label class="form-label">Business Phone</label>
                <input class="form-control" value="<?php echo adminEsc(site::phone); ?>" readonly>
            </div>
            <div class="col-12 col-lg-6">
                <label class="form-label">Address</label>
                <input class="form-control" value="<?php echo adminEsc(site::address); ?>" readonly>
            </div>
        </div>
    </section>

    <section class="admin-section mb-3">
        <h2 class="admin-section-title">System</h2>
        <div class="row g-3">
            <div class="col-12 col-lg-4">
                <label class="form-label">Database Host</label>
                <input class="form-control" value="<?php echo adminEsc(servername); ?>" readonly>
            </div>
            <div class="col-12 col-lg-4">
                <label class="form-label">Database Name</label>
                <input class="form-control" value="<?php echo adminEsc(dbname); ?>" readonly>
            </div>
            <div class="col-12 col-lg-4">
                <label class="form-label">Recache Token</label>
                <input class="form-control" value="<?php echo adminEsc($recache); ?>" readonly>
            </div>
        </div>
    </section>

    <section class="admin-section">
        <h2 class="admin-section-title">Guidance</h2>
        <p class="mb-2">
            This page is currently informational. To add editable settings safely, use a whitelist-based
            update API and keep secret credentials outside web root.
        </p>
        <p class="form-help mb-0">
            Suggested next step: implement role-based access control before enabling write operations.
        </p>
    </section>

    <?php include("../template/_footer.php"); ?>
</body>
</html>
