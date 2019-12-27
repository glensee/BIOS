 <?php
    require_once 'include/common.php';
    require_once 'include/protect.php';
    require_once 'bidHelper.php';
    require_once 'include/validations.php';

    $stuDAO = new StudentDAO();
    # get ID from SESSION variable
    $id = $_SESSION['student']->getID();
    # get $bid from SESSION and GET variables
    $isBid = filter_var($_GET['bid'], FILTER_VALIDATE_BOOLEAN);
    $data = [
        $id,
        $_GET['course'],
        $_GET['sect']
    ];

    $drop = drop($data, $isBid);

    // if drop success, will update bid
    if (isEmpty($drop)) {
        $_SESSION['student'] = $stuDAO->retrieve($id);
    } else {
        $_SESSION['errors'] = $drop;
    }

    $redirect = ($isBid) ? "studentBid.php" : "studentIndex.php";
    header("Location: $redirect");

    exit;