<?php

require_once 'common.php';

class SectionStudentDAO
{

    const section_student_header = ['userid', 'course', 'section', 'amount'];

    // Retrieve all enrollments
    public function retrieveAll()
    {
        $sql = 'SELECT userid,course,section,(CAST(amount as DECIMAL(38,2))) as amount FROM `section-student` ORDER BY course,userid';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $result = array();

        while ($row = $stmt->fetch()) {
            $result[] = new SectionStudent($row['userid'], $row['course'], $row['section'], $row['amount']);
        }

        $stmt = null;
        $conn = null;

        return $result;
    }

    // Retrieve all enrollments by USERID
    public function retrieve($userid)
    {
        $sql = 'SELECT * FROM `section-student` WHERE `userid`=:userid ORDER BY `userid` ';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
        $stmt->execute();

        $result = array();

        while ($row = $stmt->fetch()) {
            $result[] = new SectionStudent($row['userid'], $row['course'], $row['section'], $row['amount']);
        }

        $stmt = null;
        $conn = null;

        return $result;
    }

    public function retrieve_course($course)
    {
        $sql = 'SELECT * FROM `section-student` WHERE `course`=:course ORDER BY `course` ';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->bindParam(':course', $course, PDO::PARAM_STR);
        $stmt->execute();

        $result = array();

        while ($row = $stmt->fetch()) {
            $result[] = new SectionStudent($row['userid'], $row['course'], $row['section'], $row['amount']);
        }

        $stmt = null;
        $conn = null;

        return $result;
    }
    // Retrieve specfic enrollment by userid, code, section
    public function retrieve_specific($userid, $course, $section)
    {
        $sql = 'SELECT * FROM `section-student` WHERE `userid`=:userid AND `section` = :section AND `course` = :course ORDER BY `userid` ';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
        $stmt->bindParam(':section', $section, PDO::PARAM_STR);
        $stmt->bindParam(':course', $course, PDO::PARAM_STR);
        $stmt->execute();

        $result = FALSE;

        if ($row = $stmt->fetch()) {
            $result = new SectionStudent($row['userid'], $row['course'], $row['section'], $row['amount']);
        }

        $stmt = null;
        $conn = null;

        return $result;
    }
    // Retrieve specfic enrollment by userid, code, section
    public function retrieve_userid_course($userid, $course)
    {
        $sql = 'SELECT * FROM `section-student` WHERE `userid`=:userid AND `course` = :course ORDER BY `userid` ';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
        $stmt->bindParam(':course', $course, PDO::PARAM_STR);
        $stmt->execute();

        $result = FALSE;

        if ($row = $stmt->fetch()) {
            $result = new SectionStudent($row['userid'], $row['course'], $row['section'], $row['amount']);
        }

        $stmt = null;
        $conn = null;

        return $result;
    }
    // Retrieve enrollment
    public function retrieve_enrollment($course, $section)
    {
        $sql = 'SELECT userid,course,section,(CAST(amount as DECIMAL(38,2))) as amount FROM `section-student` WHERE `section` = :section AND `course` = :course ORDER BY `userid` ';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->bindParam(':section', $section, PDO::PARAM_STR);
        $stmt->bindParam(':course', $course, PDO::PARAM_STR);
        $stmt->execute();

        $result = [];

        while ($row = $stmt->fetch()) {
            $result[] = new SectionStudent($row['userid'], $row['course'], $row['section'], $row['amount']);
        }

        $stmt = null;
        $conn = null;

        return $result;
    }
    // Add enrollment
    public function add($enrollment)
    {
        $sql = 'INSERT INTO `section-student` VALUES (:userid, :course, :section, :amount)';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);

        $id = $enrollment->getID();
        $amount = $enrollment->getAmount();
        $code = $enrollment->getCourse();
        $section = $enrollment->getSection();

        $stmt->bindParam(':userid', $id, PDO::PARAM_STR);
        $stmt->bindParam(':amount', $amount, PDO::PARAM_STR);
        $stmt->bindParam(':course', $code, PDO::PARAM_STR);
        $stmt->bindParam(':section', $section, PDO::PARAM_STR);

        $isAddOK = $stmt->execute();

        $stmt = null;
        $conn = null;

        return $isAddOK;
    }

    // Update enrollment
    public function update($enrollment)
    {
        $sql = 'UPDATE `section-student` SET amount=:amount WHERE userid=:userid AND section=:section AND course=:course';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);

        $id = $enrollment->getID();
        $amount = $enrollment->getAmount();
        $course = $enrollment->getCourse();
        $section = $enrollment->getSection();

        $stmt->bindParam(':userid', $id, PDO::PARAM_STR);
        $stmt->bindParam(':amount', $amount, PDO::PARAM_STR);
        $stmt->bindParam(':course', $course, PDO::PARAM_STR);
        $stmt->bindParam(':section', $section, PDO::PARAM_STR);

        $isAddOK = $stmt->execute();

        $stmt = null;
        $conn = null;

        return $isAddOK;
    }

    public function drop($userid, $course, $section)
    {

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $sql = 'DELETE FROM `section-student` WHERE userid = :userid AND section=:section AND course=:course';
        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
        $stmt->bindParam(':course', $course, PDO::PARAM_STR);
        $stmt->bindParam(':section', $section, PDO::PARAM_STR);

        $result = $stmt->execute();

        $stmt = null;
        $conn = null;

        return $result;
    }


    // Remove All enrollment
    public function removeAll()
    {
        $sql = 'TRUNCATE TABLE `section-student`';

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