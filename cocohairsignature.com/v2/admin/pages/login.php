<?php
include("../config.php");

$loginError = "";

if (admin_is_logged_in()) {
    header("Location: ./index.php");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usernameInput = trim((string) ($_POST["username"] ?? ""));
    $passwordInput = (string) ($_POST["password"] ?? "");


    
    if ($usernameInput == env::ADMIN_USERNAME && $passwordInput == env::ADMIN_PASSWORD) {
        session_regenerate_id(true);
        $_SESSION["admin_logged_in"] = true;
        $_SESSION["admin_username"] = $usernameInput;
        header("Location: ./index.php");
    }

    $loginError = "Invalid login details.";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Login | Admin</title>
    <?php include("../template/_head.php"); ?>
</head>

<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <h1 class="h4 mb-3 text-center">Admin Login</h1>
                        <p class="text-muted text-center mb-4">Sign in to continue to the dashboard.</p>

                        <?php if ($loginError !== "") { ?>
                            <div class="alert alert-danger">
                                <?php echo htmlspecialchars($loginError, ENT_QUOTES, "UTF-8"); ?></div>
                        <?php } ?>

                        <form method="post">
                            <div class="mb-3">
                                <label class="form-label" for="username">Username</label>
                                <input type="text" class="form-control" id="username" name="username"
                                    value="<?php echo htmlspecialchars((string) ($_POST["username"] ?? ""), ENT_QUOTES, "UTF-8"); ?>"
                                    autocomplete="username" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password"
                                    autocomplete="current-password" required>
                            </div>

                            <button type="submit" class="btn btn-dark w-100">Login</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>