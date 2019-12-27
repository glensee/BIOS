<?php
require_once 'include/common.php';
require_once 'include/protect.php';
require_once 'include/Round.php';
require_once 'include/RoundDAO.php';

$round = new RoundDAO();
$roundNo = $round->retrieve();
$round = $roundNo[0];
?>

<li>
    <a href="studentIndex.php"><i class="fa fa-diamond"></i> <span class="nav-label">Home</span></a>
</li>
<li>
    <a href="studentBid.php"><i class="fa fa-diamond"></i> <span class="nav-label">Plan & Bid</span></a>
</li>

<?php if ($round == 2) {
    echo '<li><a href="studentCourseSearch.php"><i class="fa fa-diamond"></i> <span class="nav-label">Course Search</span></a></li>';
}

?>
<!--<li><a href="studentCourseSearch.php"><i class="fa fa-diamond"></i> <span class="nav-label">Course Search</span></a></li>-->