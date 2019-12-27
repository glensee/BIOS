<?php
require_once 'include/common.php';
require_once 'include/protect.php';
require_once 'include/token.php';
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse All</title>
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
                        <nav class="navbar navbar-default">
                            <div class="container-fluid">
                                <ul class="nav navbar-nav">
                                    <li><a href="#courseContent">Course Content</a></li>
                                    <li><a href="#sectionContent">Section Content</a></li>
                                    <li><a href="#studentsTable">Student Content</a></li>
                                    <li><a href="#prerequisiteTable">Prerequisite Content</a></li>
                                    <li><a href="#bidTable">Bid Content</a></li>
                                    <li><a href="#courseTable">Completed Course Content</a></li>
                                </ul>
                            </div>
                        </nav>
                    </div>
                    <!--- All Course Contents---->
                    <div class="col-lg-12">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <span id="courseContent">
                                    <h5>All Course Contents</h5>
                                </span>
                            </div>
                            <div class="ibox-content">
                                <table id="resultsTable" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Course</th>
                                            <th>School</th>
                                            <th>Title</th>
                                            <th>Description</th>
                                            <th>Exam Date</th>
                                            <th>Exam Start</th>
                                            <th>Exam End</th>
                                        </tr>
                                    </thead>
                                </table>

                            </div>
                        </div>
                    </div>

                    <!--- All Section Contents---->
                    <div class="col-lg-12">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <span id="sectionContent">
                                    <h5>All Section Contents</h5>
                                </span>
                            </div>
                            <div class="ibox-content">
                                <table id="sectionTable" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Course</th>
                                            <th>Section</th>
                                            <th>Day</th>
                                            <th>Start</th>
                                            <th>End</th>
                                            <th>Instructor</th>
                                            <th>Venue</th>
                                            <th>Size</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!--- All Student Contents---->
                    <div class="col-lg-12">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <h5>All Student Contents</h5>
                            </div>
                            <div class="ibox-content">
                                <table id="studentsTable" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>User ID</th>
                                            <th>Password</th>
                                            <th>Name</th>
                                            <th>School</th>
                                            <th>Edollar</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!--- All Prerequisite Contents---->
                    <div class="col-lg-12">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <h5>All Prerequisite Contents</h5>
                            </div>
                            <div class="ibox-content">
                                <table id="prerequisiteTable" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Course</th>
                                            <th>Prerequisite</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!--- All Bid Contents---->
                    <div class="col-lg-12">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <h5>All Bid Contents</h5>
                            </div>
                            <div class="ibox-content">
                                <table id="bidTable" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>User ID</th>
                                            <th>Amount</th>
                                            <th>Course</th>
                                            <th>Section</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!--- All Course Contents---->
                    <div class="col-lg-12">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <h5>All Completed Course Contents</h5>
                            </div>
                            <div class="ibox-content">
                                <table id="courseTable" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>User ID</th>
                                            <th>Course</th>
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

    <!-- Functionality: Retrieve data using AJAX POST from DataTables -->
    <script>
    $(document).ready(function() {
        // Display loading indicator
        $(".loader.loader-default").addClass("is-active");

        // Call POST request
        var request;
        request = $.ajax({
            url: "json/dump.php?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VybmFtZSI6ImFkbWluIiwiZGF0ZXRpbWUiOiIyMDE5LTEwLTI5IDA4OjQ1OjQ2In0.3Udk97YTtixTEXkmpOMXfqlhGy1hmH7osmkxvIZHdcU",
            type: "post",
        });

        // POST Request Success - this is where we receive JSON response results from our back-end
        request.done(function(response, textStatus, jqXHR) {
            // Log response to console (View results in F12 Console tab)
            console.log(response);
            if (response["status"] === "success") initTables(response);
        });

        // POST Request Failure - e.g. no Internet
        request.fail(function(jqXHR, textStatus, errorThrown) {
            console.error("The following error occurred: " + textStatus, errorThrown);
        });

        function initTables(response) {
            $("#resultsTable").DataTable({
                data: response["course"],
                searching: false,
                paging: false,
                info: false,
                columns: [{
                        data: 'course'
                    },
                    {
                        data: 'school'
                    },
                    {
                        data: 'title'
                    },
                    {
                        data: 'description'
                    },
                    {
                        data: 'exam date'
                    },
                    {
                        data: 'exam start'
                    },
                    {
                        data: 'exam end'
                    }
                ]
            });

            $("#sectionTable").DataTable({
                data: response["section"],
                searching: false,
                paging: false,
                info: false,
                columns: [{
                        data: 'course'
                    },
                    {
                        data: 'section'
                    },
                    {
                        data: 'day'
                    },
                    {
                        data: 'start'
                    },
                    {
                        data: 'end'
                    },
                    {
                        data: 'instructor'
                    },
                    {
                        data: 'venue'
                    },
                    {
                        data: 'size'
                    }
                ]
            });

            $("#studentsTable").DataTable({
                data: response["student"],
                searching: false,
                paging: false,
                info: false,
                columns: [{
                        data: 'userid'
                    },
                    {
                        data: 'password'
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'school'
                    },
                    {
                        data: 'edollar'
                    },
                ]
            });

            $("#prerequisiteTable").DataTable({
                data: response["prerequisite"],
                searching: false,
                paging: false,
                info: false,
                columns: [{
                        data: 'course'
                    },
                    {
                        data: 'prerequisite'
                    }
                ]
            });

            $("#bidTable").DataTable({
                data: response["bid"],
                searching: false,
                paging: false,
                info: false,
                columns: [{
                        data: 'userid'
                    },
                    {
                        data: 'amount'
                    },
                    {
                        data: 'course'
                    },
                    {
                        data: 'section'
                    }
                ]
            });

            $("#courseTable").DataTable({
                data: response["completed-course"],
                searching: false,
                paging: false,
                info: false,
                columns: [{
                        data: 'userid'
                    },
                    {
                        data: 'course'
                    }
                ]
            });

            // Hide loading indicator
            $(".loader.loader-default").removeClass("is-active");
        }
    })
    </script>
</body>

</html>