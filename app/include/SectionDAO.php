<?php

require_once 'common.php';

class SectionDAO {

    CONST section_header = ['course','section','day','start','end','instructor','venue','size'];

    // Retrieve all sections
    public  function retrieveAll() {
        $sql = 'SELECT * FROM section ORDER BY `course`,`section`';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $result = array();

        while($row = $stmt->fetch()) {
            $result[] = new Section($row['course'], $row['section'], $row['day'], $row['start'], $row['end'], $row['instructor'], $row['venue'], $row['size']);
        }

        $stmt = null;
        $conn = null;

        return $result;
    }

    // Retrieve Section by course & section
    public function retrieve($course,$section) {
        $sql = 'SELECT * FROM section WHERE course = BINARY :course AND `section`= BINARY :section';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->bindParam(':course', $course, PDO::PARAM_STR);
        $stmt->bindParam(':section', $section, PDO::PARAM_STR);
        $stmt->execute();

        $result = FALSE;

        if($row = $stmt->fetch()) {
            $result = new Section($row['course'], $row['section'], $row['day'], $row['start'], $row['end'], $row['instructor'], $row['venue'], $row['size']);
        }

        $stmt = null;
        $conn = null;

        return $result;
    }

    // Add Section
    public function add($section) {
        $sql = 'INSERT INTO section(course, section, `day`, `start`, `end`, instructor, venue, size) VALUES (:course, :section, :day, :start, :end, :instructor, :venue, :size)';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);

        $courseName = $section->getCourse();
        $sectionName = $section->getSection();
        $day = $section->getDay();
        $start = $section->getStart();
        $end = $section->getEnd();
        $instructor = $section->getInstructor();
        $venue = $section->getVenue();
        $size = $section->getSize();

        $stmt->bindParam(':course', $courseName, PDO::PARAM_STR);
        $stmt->bindParam(':section', $sectionName, PDO::PARAM_STR);
        $stmt->bindParam(':day', $day , PDO::PARAM_STR);
        $stmt->bindParam(':start', $start, PDO::PARAM_STR);
        $stmt->bindParam(':end',$end, PDO::PARAM_STR);
        $stmt->bindParam(':instructor', $instructor, PDO::PARAM_STR);
        $stmt->bindParam(':venue',$venue , PDO::PARAM_STR);
        $stmt->bindParam(':size',$size , PDO::PARAM_STR);

        $isAddOK = $stmt->execute();

        $stmt = null;
        $conn = null;

        return $isAddOK;
    }

    // Remove Section
    public function remove($course,$section) {
        $sql = 'DELETE FROM student WHERE course = BINARY :course AND section = BINARY :section';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':course', $course, PDO::PARAM_STR);
        $stmt->bindParam(':section', $section, PDO::PARAM_STR);

        $isAddOK = $stmt->execute();

        $stmt = null;
        $conn = null;

        return $isAddOK;
    }

    // Remove All Section
    public function removeAll() {
        $sql = 'TRUNCATE TABLE section';

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