<?php

require_once 'include/common.php';
require_once 'include/protect.php';
require_once 'include/Student.php';
require_once 'bidHelper.php';

$roundEnded = hasRoundEnded();
$result = biddingResult();
$sectstuDAO = new SectionStudentDAO();
$studentDAO = new StudentDAO();
$round = new RoundDAO();
$round = $round->retrieve();
$minimum = (!($round[0] == 1 || $round[1] == 'Not Started')) ? "<th>Minimum</th>" : '';
$_SESSION['student'] = $studentDAO->retrieve($_SESSION['student']->getID());
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <?php include_once("globalCSS.php"); ?>
</head>

<body>
    <div id="wrapper">
        <nav class="navbar-default navbar-static-side" role="navigation">
            <div class="sidebar-collapse">
                <ul class="nav metismenu" id="side-menu">
                    <li class="nav-header">
                        <div class="dropdown profile-element"> <span>
                                <img alt="image" class="img-circle" src="img/profile_small.jpg" />
                            </span>
                            <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                                <span class="clear"> <span class="block m-t-xs"> <strong class="font-bold"><?php echo $_SESSION['student']->getName(); ?></strong>
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

            <div class="row">
                <div class="col-lg-12">
                    <div class="wrapper wrapper-content">
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="ibox float-e-margins">
                                    <div class="ibox-title">
                                        <h5>Bidding Rounds</h5>
                                        <div class="ibox-tools">
                                            <a class="collapse-link">
                                                <i class="fa fa-chevron-up"></i>
                                            </a>
                                            <a class="close-link">
                                                <i class="fa fa-times"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="ibox-content">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Round</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- <?php if ($_SESSION['round'] == 0) : ?>
                                                    <tr>
                                                        <td colspan="2">No active rounds</td>
                                                    </tr>
                                                <?php endif; ?> -->
                                                <?php
                                                $round = new RoundDAO();
                                                $roundNo = $round->retrieve();
                                                ?>
                                                <tr>
                                                    <td><?php echo "Round " . $roundNo[0] ?></td>
                                                    <td><?php echo ucfirst($roundNo[1]) ?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="ibox float-e-margins">
                                    <div class="ibox-title">
                                        <h5>E-Accounts</h5>
                                        <div class="ibox-tools">
                                            <a class="collapse-link">
                                                <i class="fa fa-chevron-up"></i>
                                            </a>
                                            <a class="close-link">
                                                <i class="fa fa-times"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="ibox-content">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Description</th>
                                                    <th>Balance</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>eCredits</td>
                                                    <td><?php echo "$" . $_SESSION['student']->geteDollar(); ?>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>



                            <div class="col-lg-8">
                                <div class="ibox float-e-margins">
                                    <div class="ibox-title">
                                        <h5>Bidding Results</h5>
                                        <div class="ibox-tools">
                                            <a class="collapse-link">
                                                <i class="fa fa-chevron-up"></i>
                                            </a>
                                            <a class="close-link">
                                                <i class="fa fa-times"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="ibox-content">
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Course Code</th>
                                                        <th>Section </th>
                                                        <th>Amount</th>
                                                        <th>Status</th>
                                                        <?php echo $minimum ?>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <thead>
                                                        <tr>
                                                            <?php
                                                            foreach ($result as $value) {
                                                                echo "<tr>";
                                                                foreach ($value as $v) {
                                                                    echo "<td>$v</td>";
                                                                }
                                                                echo "</tr>";
                                                            }
                                                            ?>
                                                        </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>



                            <div class="col-lg-8">
                                <div class="ibox float-e-margins">
                                    <div class="ibox-title">
                                        <h5>Enrollments</h5>
                                        <div class="ibox-tools">
                                            <a class="collapse-link">
                                                <i class="fa fa-chevron-up"></i>
                                            </a>
                                            <a class="close-link">
                                                <i class="fa fa-times"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="ibox-content">
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Course Code</th>
                                                        <th>Section </th>
                                                        <th>Amount</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <thead>
                                                        <tr>
                                                            <?php
                                                            if (!isEmpty($enrollments = $sectstuDAO->retrieve($_SESSION['student']->getID()))) {
                                                                if ($roundNo[0] != '2' && $roundNo[1] != 'started') {
                                                                    foreach ($enrollments as $e) {
                                                                        $code = $e->getCourse();
                                                                        $sect = $e->getSection();
                                                                        $amt = $e->getAmount();
                                                                        echo "<td>$code</td>";
                                                                        echo "<td>$sect</td>";
                                                                        echo "<td>$amt</td>";
                                                                        echo "<td><a href='bidDrop.php?course=$code&sect=$sect&amt=$amt&bid=FALSE' class='btn btn-primary disabled btn-sm'>Drop Section</a></td>";
                                                                        echo "</tr>";
                                                                    }
                                                                } else {
                                                                    foreach ($enrollments as $e) {
                                                                        $code = $e->getCourse();
                                                                        $sect = $e->getSection();
                                                                        $amt = $e->getAmount();
                                                                        echo "<td>$code</td>";
                                                                        echo "<td>$sect</td>";
                                                                        echo "<td>$amt</td>";
                                                                        echo "<td><a href='bidDrop.php?course=$code&sect=$sect&amt=$amt&bid=FALSE' class='btn btn-primary btn-sm'>Drop Section</a></td>";
                                                                        echo "</tr>";
                                                                    }
                                                                }
                                                            }
                                                            ?>
                                                    </thead>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!---Footer---->
                <div class="footer">
                    <div>
                        <strong>Copyright</strong> &copy; 2019 Merlion University. All rights reserved.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mainly scripts -->
    <script src="js/jquery-3.1.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/plugins/metisMenu/jquery.metisMenu.js"></script>
    <script src="js/plugins/slimscroll/jquery.slimscroll.min.js"></script>

    <!-- Flot -->
    <script src="js/plugins/flot/jquery.flot.js"></script>
    <script src="js/plugins/flot/jquery.flot.tooltip.min.js"></script>
    <script src="js/plugins/flot/jquery.flot.spline.js"></script>
    <script src="js/plugins/flot/jquery.flot.resize.js"></script>
    <script src="js/plugins/flot/jquery.flot.pie.js"></script>
    <script src="js/plugins/flot/jquery.flot.symbol.js"></script>
    <script src="js/plugins/flot/curvedLines.js"></script>

    <!-- Peity -->
    <script src="js/plugins/peity/jquery.peity.min.js"></script>
    <script src="js/demo/peity-demo.js"></script>

    <!-- Custom and plugin javascript -->
    <script src="js/inspinia.js"></script>
    <script src="js/plugins/pace/pace.min.js"></script>

    <!-- jQuery UI -->
    <script src="js/plugins/jquery-ui/jquery-ui.min.js"></script>

    <!-- Jvectormap -->
    <script src="js/plugins/jvectormap/jquery-jvectormap-2.0.2.min.js"></script>
    <script src="js/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>

    <!-- Sparkline -->
    <script src="js/plugins/sparkline/jquery.sparkline.min.js"></script>

    <!-- Sparkline demo data  -->
    <script src="js/demo/sparkline-demo.js"></script>

    <!-- ChartJS-->
    <script src="js/plugins/chartJs/Chart.min.js"></script>

    <script>
        $(document).ready(function() {
            var d1 = [
                [1262304000000, 6],
                [1264982400000, 3057],
                [1267401600000, 20434],
                [1270080000000, 31982],
                [1272672000000, 26602],
                [1275350400000, 27826],
                [1277942400000, 24302],
                [1280620800000, 24237],
                [1283299200000, 21004],
                [1285891200000, 12144],
                [1288569600000, 10577],
                [1291161600000, 10295]
            ];
            var d2 = [
                [1262304000000, 5],
                [1264982400000, 200],
                [1267401600000, 1605],
                [1270080000000, 6129],
                [1272672000000, 11643],
                [1275350400000, 19055],
                [1277942400000, 30062],
                [1280620800000, 39197],
                [1283299200000, 37000],
                [1285891200000, 27000],
                [1288569600000, 21000],
                [1291161600000, 17000]
            ];

            var data1 = [{
                    label: "Data 1",
                    data: d1,
                    color: '#17a084'
                },
                {
                    label: "Data 2",
                    data: d2,
                    color: '#127e68'
                }
            ];
            $.plot($("#flot-chart1"), data1, {
                xaxis: {
                    tickDecimals: 0
                },
                series: {
                    lines: {
                        show: true,
                        fill: true,
                        fillColor: {
                            colors: [{
                                opacity: 1
                            }, {
                                opacity: 1
                            }]
                        },
                    },
                    points: {
                        width: 0.1,
                        show: false
                    },
                },
                grid: {
                    show: false,
                    borderWidth: 0
                },
                legend: {
                    show: false,
                }
            });

            var lineData = {
                labels: ["January", "February", "March", "April", "May", "June", "July"],
                datasets: [{
                        label: "Example dataset",
                        backgroundColor: "rgba(26,179,148,0.5)",
                        borderColor: "rgba(26,179,148,0.7)",
                        pointBackgroundColor: "rgba(26,179,148,1)",
                        pointBorderColor: "#fff",
                        data: [48, 48, 60, 39, 56, 37, 30]
                    },
                    {
                        label: "Example dataset",
                        backgroundColor: "rgba(220,220,220,0.5)",
                        borderColor: "rgba(220,220,220,1)",
                        pointBackgroundColor: "rgba(220,220,220,1)",
                        pointBorderColor: "#fff",
                        data: [65, 59, 40, 51, 36, 25, 40]
                    }
                ]
            };

            var lineOptions = {
                responsive: true
            };

            var ctx = document.getElementById("lineChart").getContext("2d");
            new Chart(ctx, {
                type: 'line',
                data: lineData,
                options: lineOptions
            });
        });
    </script>
</body>

</html>