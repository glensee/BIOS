<?php

require_once 'common.php';

class CourseCompletedDAO {

    CONST course_completed_header = ['userid','code'];

    // Retrieve all completed courses
    public  function retrieveAll() {
        $sql = 'SELECT * FROM course_completed ORDER BY `code`,`userid`';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $result = array();

        while($row = $stmt->fetch()) {
            $result[] = new CourseCompleted($row['userid'], $row['code']);
        }
        $stmt = null;
        $conn = null;

        return $result;
    }

    // Rerieve all course completed by userid
    public  function retrieve($userid) {
        $sql = 'SELECT * FROM course_completed WHERE `userid` = BINARY :userid ORDER BY `userid`';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
        $stmt->execute();

        $result = array();

        while($row = $stmt->fetch()) {
            $result[] = $row['code'];
        }
        $stmt = null;
        $conn = null;

        return $result;
    }
    // Add Course Completed
    public function add($course_completed) {
        $sql = 'INSERT INTO course_completed(userid, code) VALUES (:userid, :code)';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);

        $id = $course_completed->getID();
        $code = $course_completed->getCode();

        $stmt->bindParam(':userid', $id, PDO::PARAM_STR);
        $stmt->bindParam(':code',$code , PDO::PARAM_STR);

        $isAddOK = $stmt->execute();

        $stmt = null;
        $conn = null;

        return $isAddOK;
    }
    // Remove Course Completed
    public function remove($userid,$code) {
        $sql = 'DELETE FROM course_completed WHERE userid = BINARY :userid AND code = BINARY :code';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
        $stmt->bindParam(':code', $code, PDO::PARAM_STR);

        $isAddOK = $stmt->execute();

        $stmt = null;
        $conn = null;

        return $isAddOK;;
    }
    // Remove All Bids
    public function removeAll() {
        $sql = 'TRUNCATE TABLE course_completed';

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