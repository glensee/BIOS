<?php
require_once 'include/common.php';
require_once 'include/protect.php';
require_once 'include/bootstrap.php';
require_once 'include/Round.php';
require_once 'include/RoundDAO.php';

// Retrieve all bidding rounds and statuses
$round_array = (new RoundDAO())->retrieveAll();
$round_init_flag = "Awaiting bootstrap upload";
$round_inactive_flag = 'Not Started';
$round_active_flag = 'Started';
$round_terminated_flag = 'Stopped';

// Initialize Round 1 in the event database has no rounds
if (empty($round_array)) {
    $round_array = [];
    $round_array['Round ' . 1] = $round_init_flag;
}

// Retrieve bootstrapping result when bootstrap is submitted in the form
if (isset($_POST['SubmitBTSRP'])) {
    // Process bootstrap
    $bootstrap = json_decode(doBootstrap(), true);
    // Initialize Round 1 after bootstrapping even without page reload
    $round_array = [];
    $round_array['Round ' . 1] = $round_active_flag;
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <?php include_once("globalCSS.php"); ?>
</head>

<style>
    #fileinput-style {
        width: 40%;
    }

    .table>tbody>tr>td {
        vertical-align: middle;
    }
</style>

<body>
    <!-- Page loading i ndicator -->
    <div class="loader loader-default" data-text="Uploading..." data-blink></div>
    <div id="wrapper">
        <nav class="navbar-default navbar-static-side" role="navigation">
            <div class="sidebar-collapse">
                <ul class="nav metismenu" id="side-menu">
                    <li class="nav-header">
                        <div class="dropdown profile-element"> <span>
                                <img alt="image" class="img-circle" src="img/a8.jpg" width="35%" />
                            </span>
                            <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                                <span class="clear"> <span class="block m-t-xs"><strong class="font-bold">Mary
                                            Doe</strong>
                                    </span> <span class="text-muted text-xs block">Admin</span></span></a>
                        </div>
                        <div class="logo-element">
                            Mer
                        </div>
                    </li>
                    <?php include_once('adminNavbar.php') ?>;
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
                                <h5>Bidding Rounds</h5>
                            </div>
                            <div class="ibox-content">
                                <table class="table table-striped" id='roundTable'>
                                    <thead>
                                        <tr>
                                            <th>Round</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Loop through all bidding rounds array (key,value) pair -->
                                        <?php foreach ($round_array as $round_num => $round_status) : ?>
                                            <tr>
                                                <td><?php echo $round_num ?></td>
                                                <td><?php echo $round_status ?></td>
                                                <?php
                                                    # Set button disabled states
                                                    $disableStartBtn = "disabled"; // Stopped
                                                    $disableStopBtn = "disabled";  // Stopped
                                                    if ($round_status === $round_active_flag) { // Started
                                                        $disableStartBtn = "disabled";
                                                        $disableStopBtn = "";
                                                    } else if ($round_status === $round_inactive_flag) { // Not Started
                                                        $disableStartBtn = "";
                                                        $disableStopBtn = "disabled";
                                                    }
                                                    ?>
                                                <td>
                                                    <button type="submit" class="btn btn-sm btn-info btn-space startBidBtn" <?= $disableStartBtn ?>>Start
                                                        Round</button>
                                                    <button type="submit" class="btn btn-sm btn-danger btn-space stopBidBtn" <?= $disableStopBtn ?>>Stop
                                                        Round</button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <h5>Upload your Bootstrap</h5>
                            </div>
                            <div class="ibox-content">
                                <form id='bootstrap-form' action="" method="post" enctype="multipart/form-data">
                                    <div class="fileinput fileinput-new input-group" data-provides="fileinput" id="fileinput-style">
                                        <div class="form-control" data-trigger="fileinput">
                                            <i class="glyphicon glyphicon-file fileinput-exists"></i>
                                            <span class="fileinput-filename"></span>
                                        </div>
                                        <span class="input-group-addon btn btn-default btn-file">
                                            <span class="fileinput-new">Select file</span>
                                            <span class="fileinput-exists">Change</span>
                                            <input id='bootstrap-file' type="file" name="bootstrap-file" />
                                        </span>
                                        <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
                                    </div>
                                    <input type="submit" class="btn btn-disabled btn-space" id="SubmitBTSRP" name="SubmitBTSRP" value="Upload" disabled>
                                </form>
                            </div>
                        </div>
                    </div>

                    <?php if (isset($_POST['SubmitBTSRP'])) : ?>
                        <div class="col-lg-12" id="btstrpCard">
                            <div class="ibox float-e-margins">
                                <div class="ibox-title">
                                    <h5>Bootstrap Results</h5>
                                </div>
                                <div class="ibox-content">
                                    <?php if ($bootstrap['status'] == "success") : ?>
                                        <div class="alert alert-success">
                                            <strong>Success!</strong> Bootstrapping has been completed successfully.
                                        </div>
                                    <?php else : ?>
                                        <div class="alert alert-danger">
                                            <strong>Error!</strong> Bootstrapping was unsuccessful. Please refer to the
                                            messages below.
                                        </div>
                                    <?php endif; ?>

                                    <table class="table table-striped" id="btstrpTable">
                                        <caption><b>Number of Bootstrap Records Loaded from .csv</b></caption>
                                        <thead>
                                            <tr>
                                                <th>Bid</th>
                                                <th>Course</th>
                                                <th>Course Completed</th>
                                                <th>Prerequisite</th>
                                                <th>Section</th>
                                                <th>Student</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><?php echo $bootstrap['num-record-loaded'][0]['bid.csv']; ?></td>
                                                <td><?php echo $bootstrap['num-record-loaded'][1]['course.csv']; ?></td>
                                                <td><?php echo $bootstrap['num-record-loaded'][2]['course_completed.csv']; ?>
                                                </td>
                                                <td><?php echo $bootstrap['num-record-loaded'][3]['prerequisite.csv']; ?>
                                                </td>
                                                <td><?php echo $bootstrap['num-record-loaded'][4]['section.csv']; ?></td>
                                                <td><?php echo $bootstrap['num-record-loaded'][5]['student.csv']; ?></td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <?php if ($bootstrap['status'] == "error") : ?>
                                        <table class="table table-striped">
                                            <caption><b>Error Messages</b></caption>
                                            <thead>
                                                <tr>
                                                    <th>File</th>
                                                    <th>Line</th>
                                                    <th>Message</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($bootstrap['error'] as $messages) : ?>
                                                    <tr>
                                                        <td><?php echo $messages['file']; ?></td>
                                                        <td><?php echo $messages['line']; ?></td>
                                                        <td>
                                                            <?php $error = '';
                                                                        foreach ($messages['message'] as $message) {
                                                                            $error .= $message . ', ';
                                                                        }
                                                                        echo substr($error, 0, -2); ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                </div>
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
    <!-- Dropzone -->
    <script src="js/plugins/dropzone/dropzone.js"></script>
    <!-- CodeMirror -->
    <script src="js/plugins/codemirror/codemirror.js"></script>
    <script src="js/plugins/codemirror/mode/xml/xml.js"></script>
    <!-- SweetAlert2 -->
    <script src="js/plugins/sweetalert2/sweetalert2.all.min.js"></script>

    <!-- Functionality: Bootstrap Submission -->
    <script>
        // Disable submit button until file is uploaded
        $(document).ready(function() {
            $('input:file').change(function() {
                $('input:submit').attr('disabled', false);
                $('input:submit').removeClass("btn-disabled");
                $('input:submit').addClass("btn-primary");
            });
        });

        // Automatically scroll-to & direct user attention to Bootstrap Results card
        if ($('#btstrpCard').is(':visible')) {
            document.getElementById('btstrpCard').scrollIntoView({
                behavior: "smooth",
                block: "start"
            });
        }

        // Display loading indicator on form submission
        $('#bootstrap-form').submit(function() {
            $(".loader.loader-default").addClass("is-active");
        });
    </script>

    <!-- Functionality: Start / Stop Bidding Rounds using AJAX -->
    <script>
        // Stop round button
        $(".btn.btn-sm.btn-danger.btn-space.stopBidBtn").click(function(e) {
            e.preventDefault();
            var request, scope = this;

            // Call POST request
            request = $.ajax({
                url: "json/stop.php?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VybmFtZSI6ImFkbWluIiwiZGF0ZXRpbWUiOiIyMDE5LTEwLTI4IDE5OjExOjA4In0.7MLVVP2X-9auK3PernjNTiNaWFyUWV0tVyRp8kqBRVU",
                type: "post",
            });

            // POST Request Success - this is where we receive JSON response results from our back-end
            request.done(function() {
                window.location.href = window.location.href;
                // Log response to console (View results in F12 Console tab)
                // console.log(response);
                // Display modal pop-up
                // displayAlert(scope, "stopped", response);
            });

            // POST Request Failure - e.g. no Internet
            request.fail(function(jqXHR, textStatus, errorThrown) {
                console.error("The following error occurred: " + textStatus, errorThrown);
            });
        });

        // Start round button
        $(".btn.btn-sm.btn-info.btn-space.startBidBtn").click(function(e) {
            e.preventDefault();
            var request, scope = this;

            // Call POST request
            request = $.ajax({
                url: "json/start.php?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VybmFtZSI6ImFkbWluIiwiZGF0ZXRpbWUiOiIyMDE5LTEwLTI4IDE5OjExOjA4In0.7MLVVP2X-9auK3PernjNTiNaWFyUWV0tVyRp8kqBRVU",
                type: "post",
            });

            // POST Request Success - this is where we receive JSON response results from our back-end
            request.done(function() {
                window.location.href = window.location.href;
                // Log response to console (View results in F12 Console tab)
                // console.log(response);
                // // Display modal pop-up
                // displayAlert(scope, "started", response);
            });

            // POST Request Failure - e.g. no Internet
            request.fail(function(jqXHR, textStatus, errorThrown) {
                console.error("The following error occurred: " + textStatus, errorThrown);
            });
        });

        // function displayAlert(scope, set, response) {
        //     // Get round number from table
        //     var roundNumber = $(scope).closest("tr").find('td:nth-child(1)').text(),
        //         // Get results from response JSON
        //         status = response["status"],
        //         msg = "";

        //     // Craft success/error message
        //     status === "success" ? msg = `Successfully ${set} ${roundNumber}` : msg = response["message"][0];

        //     // Fire up SweetAlert2's modal pop-up displaying stop-bid responses
        //     Swal.fire({
        //         title: capitalize(status) + "!",
        //         text: capitalize(msg),
        //         type: status,
        //         onClose: function() { // Refresh page to retrieve new PHP values
        //             // Avoid using location.reload() in edge case where reload causes bootstrap re-submission
        //             window.location.href = window.location.href;
        //         }
        //     })
        // }

        function capitalize(s) {
            return s && s[0].toUpperCase() + s.slice(1);
        }
    </script>

</body>

</html>