<?php
require_once 'include/common.php';
require_once 'include/protect.php';
require_once 'bidHelper.php';

$userHasBids = hasUserBidded();
$currentBids = currentBids();
$roundEnded = hasRoundEnded();

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Place Bids</title>
    <?php include_once("globalCSS.php"); ?>
</head>

<style>
.label-space {
    margin-left: 10px;
}

#fileinput-style {
    width: 40%;
}
</style>

<body>
    <!-- Page loading indicator -->
    <div class="loader loader-default" data-text="Placing Bids..." data-blink></div>
    <div id="wrapper">
        <nav class="navbar-default navbar-static-side" role="navigation">
            <div class="sidebar-collapse">
                <ul class="nav metismenu" id="side-menu">
                    <li class="nav-header">
                        <div class="dropdown profile-element"> <span>
                                <img alt="image" class="img-circle" src="img/profile_small.jpg" />
                            </span>
                            <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                                <span class="clear"> <span class="block m-t-xs"> <strong
                                            class="font-bold"><?php echo $_SESSION['student']->getName(); ?></strong>
                                    </span> <span class="text-muted text-xs block"><?php
                                                                                    if (isset($_SESSION['student'])) {
                                                                                        echo "Undergraduate </span> </span></a>";
                                                                                    }
                                                                                    ?>
                        </div>
                        <div class="logo-element">
                            Mer
                        </div>
                    </li>
                    <?php include_once('studentNavbar.php') ?>;
                </ul>
            </div>
        </nav>

        <div id="page-wrapper" class="gray-bg">
            <div class="row border-bottom">
                <nav class="navbar navbar-static-top white-bg" role="navigation" style="margin-bottom: 0">
                    <ul class="nav navbar-top-links navbar-right">
                        <li>
                            <a href="logout.php">
                                <i class="fa fa-sign-out"></i> Log out
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>

            <div class="wrapper wrapper-content">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <h5>Place Bids</h5>
                                <?php
                                $bidLeft = $_SESSION['student']->geteDollar();
                                echo "<span class='label label-info label-size pull-right'>E-credits: <strong> $" . "{$bidLeft}</strong></span>";
                                ?>
                            </div>

                            <div class="ibox-content">
                                <!--- Error Msg---->
                                <?php
                                if (isset($_SESSION['errors'])) {
                                    $errorMsgs = '';
                                    foreach ($_SESSION['errors'] as $errorMsg) {
                                        $errorMsgs .= "<br>" . "- " . $errorMsg;
                                    }
                                    echo "<div class='alert alert-danger'><strong>Error!</strong> {$errorMsgs}</div>";
                                    unset($_SESSION['errors']);
                                } elseif (isset($_SESSION['bid_success'])) {
                                    $courseCode = $_SESSION['bid_success'][0];
                                    $courseSect = $_SESSION['bid_success'][1];
                                    $courseAmnt = '$' . $_SESSION['bid_success'][2];
                                    echo "<div class='alert alert-success'><strong>Success!</strong> You have bidded <strong> {$courseCode} - {$courseSect} </strong> for {$courseAmnt} successfully.</div>";
                                    unset($_SESSION['bid_success']);
                                }
                                ?>
                                <form id="bidForm" action="studentBidProcess.php" method="post">
                                    <div class="form-group">
                                        <label for="course_code">Course Code</label>
                                        <input type="text" class="form-control" name='course_code'
                                            placeholder="Course Code" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="sectionNo">Section Number</label>
                                        <input type="text" class="form-control" name='section'
                                            placeholder="Section Number" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="bidAmt">Bid Amount</label>
                                        <input type="text" class="form-control" name='amount' placeholder="Bid Amount"
                                            required>
                                    </div>
                                    <button type="submit" class="btn btn-primary" name="SubmitBid">Submit
                                        Bid</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <?php if ($userHasBids) : ?>
                    <div class="col-lg-12" id="moduleCard">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <h5>Modules You Have Bidded</h5>
                            </div>
                            <div class="ibox-content">
                                <table class="table table-striped" id="moduleTable">
                                    <thead>
                                        <tr>
                                            <th>Course Code</th>
                                            <th>Section</th>
                                            <th>Amt</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                                foreach ($currentBids as $key => $value) {
                                                    $code = $value->getCode();
                                                    $sect = $value->getSection();
                                                    $amt = $value->getAmount();
                                                    echo "<tr>";
                                                    echo "<td>{$code}</td>";
                                                    echo "<td>{$sect}</td>";
                                                    echo "<td>{$amt}</td>";
                                                    if (!$roundEnded) {
                                                        echo "<td><a href='bidDrop.php?course=$code&sect=$sect&amt=$amt&bid=TRUE' class='btn btn-primary btn-sm'>Drop Bid</a></td>";
                                                    } else {
                                                        echo "<td></td>";
                                                    }
                                                    echo "</tr>";
                                                } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                </div>
                <div class="footer">
                    <div>
                        <strong>Copyright</strong> &copy; 2019 Merlion University. All rights reserved.
                    </div>
                </div>
            </div>
        </div>

        <!-- Mainly scripts -->
        <script src="js/jquery-3.1.1.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <script src="js/plugins/metisMenu/jquery.metisMenu.js"></script>
        <script src="js/plugins/slimscroll/jquery.slimscroll.min.js"></script>

        <!-- Custom and plugin javascript -->
        <script src="js/inspinia.js"></script>
        <script src="js/plugins/pace/pace.min.js"></script>

        <!-- Jasny -->
        <script src="js/plugins/jasny/jasny-bootstrap.min.js"></script>

        <!-- DROPZONE -->
        <script src="js/plugins/dropzone/dropzone.js"></script>

        <!-- CodeMirror -->
        <script src="js/plugins/codemirror/codemirror.js"></script>
        <script src="js/plugins/codemirror/mode/xml/xml.js"></script>

        <!-- Functionality: Form Submission -->
        <script>
        // Display loading indicator on form submission
        $('#bidForm').submit(function() {
            $(".loader.loader-default").addClass("is-active");
        });
        </script>
</body>

</html>