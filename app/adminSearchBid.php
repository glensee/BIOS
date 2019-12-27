<?php
require_once 'include/common.php';
require_once 'include/protect.php';
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Bid</title>
    <?php include_once("globalCSS.php"); ?>
</head>

<style>
/* Enforce fixed table width | prevents auto-sizing bug with DataTable */
table {
    margin: 0 auto;
    width: 100%;
    clear: both;
    border-collapse: collapse;
    table-layout: fixed;
    word-wrap: break-word;
}
</style>

<body>
    <!-- Page loading indicator -->
    <div class="loader loader-default" data-text="Retrieving..." data-blink></div>
    <div id="wrapper">
        <nav class="navbar-default navbar-static-side" role="navigation">
            <div class="sidebar-collapse">
                <ul class="nav metismenu" id="side-menu">
                    <li class="nav-header">
                        <div class="dropdown profile-element"> <span>
                                <img alt="image" class="img-circle" src="img/a8.jpg" width="35%" />
                            </span>
                            <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                                <span class="clear"> <span class="block m-t-xs"> <strong class="font-bold">Mary
                                            Doe</strong>
                                    </span> <span class="text-muted text-xs block">Admin </span> </span></a>
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
                                <h5>Search Bid (Dump Bid)</h5>
                            </div>
                            <div class="ibox-content">
                                <form id="searchForm" method="POST">
                                    <div class="form-group">
                                        <label for="course">Course Code</label>
                                        <input type="text" class="form-control" name='course' placeholder="Course Code"
                                            required>
                                    </div>
                                    <div class="form-group">
                                        <label for="section">Section Number</label>
                                        <input type="text" class="form-control" name='section'
                                            placeholder="Section Number" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary" name="SubmitSearch">Retrieve All
                                        Bids</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12" id="resultsCard">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <h5>Search Results</h5>
                            </div>
                            <div class="ibox-content">
                                <div id="searchSuccess" class="alert alert-success" hidden>
                                    <strong>Success! </strong>Bid records retrieved!
                                </div>
                                <div id="searchFailure" class="alert alert-danger" hidden>
                                    <!-- Error message is populated via jQuery below -->
                                </div>
                                <table class="table table-striped" id="resultsTable" width="100%">
                                    <!-- Table is populated via AJAX below -->
                                    <thead>
                                        <tr>
                                            <th>Row</th>
                                            <th>User ID</th>
                                            <th>Amount</th>
                                            <th>Result</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="footer">
                <div>
                    <strong>Copyright</strong> &copy; 2019 Merlion University. All rights reserved.
                </div>
            </div>
        </div>
    </div>

    <!-- Mainly design and utility scripts -->
    <script src="js/jquery-3.1.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/plugins/metisMenu/jquery.metisMenu.js"></script>
    <script src="js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
    <script src="js/inspinia.js"></script>
    <script src="js/plugins/pace/pace.min.js"></script>
    <script src="js/plugins/jasny/jasny-bootstrap.min.js"></script>
    <script src="js/plugins/dropzone/dropzone.js"></script>
    <script src="js/plugins/codemirror/codemirror.js"></script>
    <script src="js/plugins/codemirror/mode/xml/xml.js"></script>
    <script src="js/plugins/dataTables/datatables.min.js"></script>

    <!-- Functionality: Hide Results Card Div without space reservation -->
    <script>
    document.getElementById("resultsCard").style.display = 'none';
    </script>

    <!-- Functionality: POST Search using AJAX -->
    <script>
    var request;
    var table;

    function setup(e, that) {
        e.preventDefault();
        // Abort any pending request
        if (request) request.abort();
        // Store all form inputs
        var $inputs = that.find("input");
        // Disable inputs for the duration of AJAX request
        $inputs.prop("disabled", true);
        return $inputs;
    }

    function createJSON(inputs) {
        // Create JSON object from all user inputs
        var obj = {};
        inputs.each(function() {
            obj[this.name] = $(this).val();
        });
        return JSON.stringify(obj);
    }

    function reset() {
        // Reset error messgaes
        $("#searchFailure").html('');
        // Reset table
        $("#resultsTable").dataTable().fnClearTable();
        $("#resultsTable").dataTable().fnDraw();
        $("#resultsTable").dataTable().fnDestroy();
    }

    function validateStatus(response) {
        if (response["status"] === "success") {
            // Show Success Alert
            $("#searchSuccess").show();
            $("#searchFailure").hide();
            // Show DataTable
            $("#resultsTable").show();
            // Create DataTable
            createDataTable(response);
        } else {
            // Show Error Alert
            $("#searchSuccess").hide();
            $("#searchFailure").show();
            // Hide DataTable
            $("#resultsTable").hide();
            // Update error messages
            $("#searchFailure").html("<strong>Error! </strong>");
            response["message"].forEach(msg => {
                $("#searchFailure").html(
                    $("#searchFailure").html() + "<br> - " + msg
                )
            })
        }
    }

    function createDataTable(response) {
        // Populate jQuery DataTable
        table = $('#resultsTable').DataTable({
            data: response["bids"],
            "bDestroy": true,
            searching: false,
            paging: false,
            info: false,
            columns: [{
                    data: 'row'
                },
                {
                    data: 'userid'
                },
                {
                    data: 'amount'
                },
                {
                    data: 'result'
                }
            ]
        });
    }

    $("#searchForm").submit(function(e) {
        // Call functions
        reset();
        var inputs = setup(e, $(this));
        var jsonData = createJSON(inputs);

        // Call POST request
        request = $.ajax({
            url: "json/bid-dump.php?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VybmFtZSI6ImFkbWluIiwiZGF0ZXRpbWUiOiIyMDE5LTEwLTI5IDA4OjQ1OjQ2In0.3Udk97YTtixTEXkmpOMXfqlhGy1hmH7osmkxvIZHdcU&r=" +
                jsonData,
            type: "post",
        });

        // POST Request Success - this is where we receive JSON response results from our back-end
        request.done(function(response, textStatus, jqXHR) {
            // Log response to console (View results in F12 Console tab)
            console.log(response);
            // Validate response
            validateStatus(response);
        });

        // POST Request Failure - e.g. no Internet
        request.fail(function(jqXHR, textStatus, errorThrown) {
            console.error("The following error occurred: " + textStatus, errorThrown);
        });

        // Called no matter request succeeds or fails
        request.always(function() {
            // Unhide results card
            document.getElementById("resultsCard").style.display = 'block';
            // Re-enable all inputs & eset inputs fields
            inputs.prop("disabled", false).val('');
            // Hide loading indicator
            $(".loader.loader-default").removeClass("is-active");
        });
    });
    </script>

    <!-- Functionality: Form Submission -->
    <script>
    // Display loading indicator on form submission
    $('#searchForm').submit(function() {
        $(".loader.loader-default").addClass("is-active");
    });

    // Automatically scroll-to & direct user attention to Search Results card
    if ($('#resultsCard').is(':visible')) {
        document.getElementById('resultsCard').scrollIntoView({
            behavior: "smooth",
            block: "start"
        });
    }
    </script>
</body>

</html>