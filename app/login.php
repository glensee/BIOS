<!DOCTYPE html>

<?php

require_once 'include/common.php';

?>

<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BIOS Login</title>
    <?php include_once("globalCSS.php"); ?>
</head>

<body class="gray-bg">

    <div class="middle-box text-center loginscreen animated fadeInDown">
        <div>
            <div>

                <h1 class="logo-name"></h1>
                <img src='img/uniLogo.png' width="auto" height="180">

            </div>
            </p>
            <p>Sign in with your Merlion's user ID</p>
            <form class="m-t" role="form" method='POST' action="loginProcess.php">
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="Username" name='username' required="">
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" placeholder="Password" name='password' required="">
                </div>
                <button type="submit" class="btn btn-primary block full-width m-b">Login</button>

                <a href="index.html"><small>Forgot password?</small></a>
            </form>
            <?php printErrors(); ?>
        </div>
    </div>

    <!-- Mainly scripts -->
    <script src="js/jquery-3.1.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>

</body>

</html>