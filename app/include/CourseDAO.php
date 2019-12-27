<?php

require_once 'common.php';

class CourseDAO {

    CONST course_header = ['course','school','title','description','exam date','exam start','exam end'];

    // Retrieve all courses
    public  function retrieveAll() {
        $sql = 'SELECT * FROM course ORDER BY `course`';


        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        //$result = array();

        while($row = $stmt->fetch()) {
            $result[] = new Course($row['course'], $row['school'], $row['title'], $row['description'], $row['exam date'], $row['exam start'], $row['exam end']);
        }

        $stmt = null;
        $conn = null;

        return $result;
    }

    // Retrieve Course by Course ID
    public function retrieve($courseid) {

        $connMgr = new ConnectionManager();
        $pdo = $connMgr->getConnection();

        $sql = 'SELECT * FROM COURSE WHERE COURSE = BINARY :courseid';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":courseid", $courseid, PDO::PARAM_STR);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        $course = FALSE;

        if($row = $stmt->fetch()) {
            $course = new Course($row['course'], $row['school'], $row['title'], $row['description'], $row['exam date'], $row['exam start'], $row['exam end']);
        }

        $stmt = null;
        $pdo = null;

        return $course;
    }
    // Add Course
    public function add($course) {
        $sql = "INSERT INTO course(course,school,title,`description`,`exam date`, `exam start`, `exam end`) VALUES (:course, :school, :title, :description, :examdate,:examstart, :examend)";

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);

        $courseName = $course->getCourse();
        $school = $course->getSchool();
        $title = $course->getTitle();
        $description = $course->getDescription();
        $examDate = $course->getExamDate();
        $examStart = $course->getExamStart();
        $examEnd = $course->getExamEnd();

        $stmt->bindParam(':course',$courseName , PDO::PARAM_STR);
        $stmt->bindParam(':school',$school , PDO::PARAM_STR);
        $stmt->bindParam(':title',$title , PDO::PARAM_STR);
        $stmt->bindParam(':description',$description , PDO::PARAM_STR);
        $stmt->bindParam(':examdate',$examDate , PDO::PARAM_STR);
        $stmt->bindParam(':examstart',$examStart , PDO::PARAM_STR);
        $stmt->bindParam(':examend',$examEnd , PDO::PARAM_STR);

        $isAddOK = $stmt->execute();

        $stmt = null;
        $conn = null;

        return $isAddOK;
    }
    // Remove Course
    public function remove($course) {
        $sql = 'DELETE FROM course WHERE course = BINARY :course';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':course', $course, PDO::PARAM_STR);

        $isAddOK = $stmt->execute();

        $stmt = null;
        $conn = null;

        return $isAddOK;;
    }

    // Remove All Bids
    public function removeAll() {
        $sql = 'TRUNCATE TABLE course';

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