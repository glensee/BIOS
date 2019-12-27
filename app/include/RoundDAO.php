<?php

require_once 'common.php';

class RoundDAO
{
    // Retrieve round
    public function retrieve()
    {
        $sql = 'SELECT * FROM round';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $round = FALSE;

        while ($row = $stmt->fetch()) {
            $round = [$row['round'], $row['status']];
        }

        $stmt = null;
        $conn = null;

        return $round;
    }

    // Retrieve all round
    public function retrieveAll()
    {
        $sql = 'SELECT * FROM round';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $round_array = [];

        while ($row = $stmt->fetch()) {
            // Return an Array of key,value pair to the beautiful frontend
            $round_array['Round ' . $row['round']] = ucfirst($row['status']); // ucfirst capitalizes first char
        }

        $stmt = null;
        $conn = null;

        return $round_array;
    }

    // Update Round number
    public function update($round, $status)
    {
        $sql = 'UPDATE round SET status= :status WHERE round = :round';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':round', $round, PDO::PARAM_INT);
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);

        $isAddOK = $stmt->execute();

        $stmt = null;
        $conn = null;

        return $isAddOK;
    }

    // Add new round
    public function add($round, $status)
    {
        $sql = 'INSERT INTO round VALUES (:round,:status)';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':round', $round, PDO::PARAM_INT);
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);

        $isAddOK = $stmt->execute();

        $stmt = null;
        $conn = null;

        return $isAddOK;
    }

    // Remove All Round
    public function removeAll()
    {
        $sql = 'TRUNCATE TABLE round';

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