<?php

class AdminDAO {

    public  function retrieve($username) {
        $sql = 'SELECT * FROM admin WHERE username=:username';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();

        $admin = FALSE;

        if($row = $stmt->fetch()) {
            $admin = new Admin($row['username'],$row['password'], $row['name']);
        }

        $stmt = null;
        $pdo = null;

        return $admin;
    }

}

?>

