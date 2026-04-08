<?php
$currentPath = str_replace("\\", "/", (string) ($_SERVER["PHP_SELF"] ?? ""));
$needle = "admin/";
$pos = strpos($currentPath, $needle);
if ($pos !== false) {
    $currentPath = substr($currentPath, $pos + strlen($needle));
}
$currentPath = ltrim($currentPath, "/");

$adminNavItems = [
    ["href" => "index.php", "label" => "Dashboard", "icon" => "bi-speedometer2"],
    ["href" => "pages/appointments.php", "label" => "Appointments", "icon" => "bi-calendar-week"],
    ["href" => "pages/product_stats.php", "label" => "Product Stats", "icon" => "bi-bar-chart-line"],
    ["href" => "pages/availability.php", "label" => "Availability", "icon" => "bi-calendar2-check"],
    ["href" => "pages/override.php", "label" => "Overrides", "icon" => "bi-calendar2-plus"],
    ["href" => "pages/settings.php", "label" => "Settings", "icon" => "bi-sliders"],
    ["href" => "pages/logs.php", "label" => "Activity", "icon" => "bi-journal-text"],
];

if (!function_exists("renderAdminNavItems")) {
    function renderAdminNavItems(array $items, string $currentPath): void
    {
        foreach ($items as $item) {
            $isActive = ($currentPath === $item["href"]);
            $activeClass = $isActive ? "active" : "";
            $activeAria = $isActive ? 'aria-current="page"' : "";
            echo '<a class="admin-nav-link ' . $activeClass . '" href="' . $item["href"] . '" ' . $activeAria . '>';
            echo '<i class="bi ' . $item["icon"] . '"></i>';
            echo '<span>' . htmlspecialchars($item["label"], ENT_QUOTES) . '</span>';
            echo "</a>";
        }
    }
}
?>

<div class="admin-layout">
    <aside class="admin-sidebar d-none d-lg-flex">
        <div class="admin-brand-wrap">
            <div class="admin-brand-mark">CHS</div>
            <div>
                <div class="admin-brand-title">CocoHairSignature</div>
                <div class="admin-brand-subtitle">Admin Console</div>
            </div>
        </div>

        <nav class="admin-nav mt-3">
            <?php renderAdminNavItems($adminNavItems, $currentPath); ?>
        </nav>

        <div class="admin-sidebar-footer">
            <a class="btn btn-sm btn-outline-light w-100" href="pages/logout.php">
                <i class="bi bi-box-arrow-right me-1"></i> Logout
            </a>
        </div>
    </aside>

    <div class="admin-main">
        <header class="admin-topbar">
            <button class="btn btn-outline-secondary d-lg-none" type="button" data-bs-toggle="offcanvas"
                data-bs-target="#adminMobileNav" aria-controls="adminMobileNav">
                <i class="bi bi-list"></i>
            </button>
            <div>
                <div class="admin-topbar-title">Admin Panel</div>
                <div class="admin-topbar-subtitle">Site management and bookings overview</div>
            </div>
            <div class="admin-topbar-meta">
                <span class="badge text-bg-light border" id="adminNow">--</span>
            </div>
        </header>

        <main class="admin-content">

            <div class="offcanvas offcanvas-start" tabindex="-1" id="adminMobileNav"
                aria-labelledby="adminMobileNavLabel">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title" id="adminMobileNavLabel">Admin Navigation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                    <div class="admin-nav">
                        <?php renderAdminNavItems($adminNavItems, $currentPath); ?>
                        <a class="admin-nav-link mt-2" href="pages/logout.php">
                            <i class="bi bi-box-arrow-right"></i>
                            <span>Logout</span>
                        </a>
                    </div>
                </div>
            </div>
