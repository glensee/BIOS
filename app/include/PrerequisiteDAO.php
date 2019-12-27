<?php

require_once 'common.php';

class PrerequisiteDAO {

    CONST preq_header = ['course','prerequisite'];

    // Retrieve all prerequisite
    public  function retrieveAll() {
        $sql = 'SELECT * FROM prerequisite ORDER BY `course`,`prerequisite`';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $result = array();

        while($row = $stmt->fetch()) {
            $result[] = new Prerequisite($row['course'], $row['prerequisite']);
        }

        $stmt = null;
        $conn = null;

        return $result;
    }

    // Retrieve by Course
    public function retrieve($course) {
        $sql = 'SELECT course, prerequisite FROM prerequisite WHERE course = BINARY :course ORDER BY course ';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->bindParam(':course', $course, PDO::PARAM_STR);
        $stmt->execute();

        $result = array();

        while($row = $stmt->fetch()) {
            $result[] = $row['prerequisite'];
        }

        $stmt = null;
        $conn = null;

        return $result;
    }

    // Add prerequisite
    public function add($prerequisite) {
        $sql = 'INSERT INTO prerequisite(course, prerequisite) VALUES (:course, :prerequisite)';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);

        $course = $prerequisite->getCourse();
        $prereq = $prerequisite->getPrerequisite();
        $stmt->bindParam(':course',$course, PDO::PARAM_STR);
        $stmt->bindParam(':prerequisite',$prereq , PDO::PARAM_STR);

        $isAddOK = $stmt->execute();

        $stmt = null;
        $conn = null;

        return $isAddOK;
    }

    // Remove prerequisite
    public function remove($course) {
        $sql = 'DELETE FROM prerequisite WHERE course = BINARY :course';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':course', $course, PDO::PARAM_STR);

        $isAddOK = $stmt->execute();

        $stmt = null;
        $conn = null;

        return $isAddOK;
    }

    // Remove All prerequisite
    public function removeAll() {
        $sql = 'TRUNCATE TABLE prerequisite';

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