<?php
include_once("../config.php");
?>
<!DOCTYPE html>
<html>

<head>
    <?php include(dirr . "/template/head.php"); ?>
    <title>This page is not found</title>
</head>

<body>

    <?php include(dirr . "/template/header.php"); ?>
    <section class="bg-dark text-white d-flex align-items-center justify-content-center" style="min-height: 70vh;">
        <div class="container text-center">
            <div class="display-1 fw-bold mb-4"
                style="background: linear-gradient(to right, #c69c22, #f9f07f); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                404</div>
            <h2 class="mb-3">Oops! Page Not Found</h2>
            <p class="mb-4">The page you're looking for doesn't exist. It might have been moved or deleted.</p>
            <a href="<?php echo site::url_hostdir(); ?>/pages/hairlist.php" class="btn btn-lg text-white"
                style="background:#e35a1e;">Book an Hairstyle</a>
        </div>
    </section>
    <?php include(dirr . "/template/footer.php"); ?>
</body>

</html>