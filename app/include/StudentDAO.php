<?php

require_once 'common.php';

class StudentDAO {

    CONST student_header = ['userid','password','name','school','edollar'];

    public function authenticate($userid, $password) {

        $connMgr = new ConnectionManager();
        $pdo = $connMgr->getConnection();

        $sql = "SELECT * FROM student WHERE userid = :userid";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":userid", $userid, PDO::PARAM_STR);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $status = $stmt->execute();

        if (!$status) {
            $err = $stmt->errorinfo();
            var_dump($err);
        }

        $return_msg = "";
        $row = $stmt->fetch();

        if (!$row) {
            $return_msg = "invalid username/password";
        }
        elseif ($row['password'] != $password) {
            $return_msg = "invalid password";
        }
        else{
            $return_msg = 'SUCCESS';
        }

        $stmt = null;
        $pdo = null;

        return $return_msg;
    }

    // Retrieve All Students
    public  function retrieveAll() {
        $sql = 'SELECT userid,password,name,school,(CAST(edollar AS DECIMAL(38,2))) AS `edollar` FROM student ORDER BY `userid`';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        while($row = $stmt->fetch()) {
            $result[] = new Student($row['userid'],
                                    $row['password'],
                                    $row['name'],
                                    $row['school'],
                                    $row['edollar']);
        }

        $stmt = null;
        $conn = null;

        return $result;
    }

    // Retrieve Student by USERID
    public function retrieve($userid) {

        $connMgr = new ConnectionManager();
        $pdo = $connMgr->getConnection();

        $sql = 'SELECT userid,password,name,school,(CAST(edollar AS DECIMAL(38,2))) AS `edollar` FROM student WHERE userid = BINARY :userid';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":userid", $userid, PDO::PARAM_STR);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $status = $stmt->execute();
        $student = FALSE;

        if($row = $stmt->fetch()) {
            $student = new Student($row['userid'],$row['password'],$row['name'], $row['school'], $row['edollar']);
        }

        $stmt = null;
        $pdo = null;

        return $student;
    }

    // Add Student
    public function add($student) {
        $sql = 'INSERT INTO student(userid, password, name, school, edollar) VALUES (:userid, :password, :name, :school, :edollar)';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);

        $id = $student->getID();
        $pass = $student->getPassword();
        $name = $student->getName();
        $school = $student->getSchool();
        $eDollar = $student->geteDollar();

        $stmt->bindParam(':userid',$id , PDO::PARAM_STR);
        $stmt->bindParam(':password',$pass , PDO::PARAM_STR);
        $stmt->bindParam(':name',$name , PDO::PARAM_STR);
        $stmt->bindParam(':school',$school , PDO::PARAM_STR);
        $stmt->bindParam(':edollar',$eDollar , PDO::PARAM_STR);

        $isAddOK = $stmt->execute();

        $stmt = null;
        $conn = null;

        return $isAddOK;
    }

    // Update Student
    public function update($userid,$eDollar) {
        $sql = 'UPDATE student SET edollar = :edollar WHERE userid = BINARY :userid';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':userid',$userid , PDO::PARAM_STR);
        $stmt->bindParam(':edollar',$eDollar , PDO::PARAM_STR);

        $isAddOK = $stmt->execute();

        $stmt = null;
        $conn = null;

        return $isAddOK;
    }
    // Remove Bid
    public function remove($userid) {
        $sql = 'DELETE FROM student WHERE userid = BINARY :userid';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);

        $isAddOK = $stmt->execute();

        $stmt = null;
        $conn = null;

        return $isAddOK;
    }

    // Remove All Students
    public function removeAll() {
        $sql = 'TRUNCATE TABLE student';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);

        $stmt->execute();
        $count = $stmt->rowCount();

        $stmt = null;
        $conn = null;

        return $count;
    }

}

?>