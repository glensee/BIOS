<?php

require_once 'common.php';

class MinBidDAO
{
    // Retrieve min bid by code, section
    public function retrieve($code, $section){
        $sql = 'SELECT * FROM `minbid` WHERE `section` = :section AND `code` = :code';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->bindParam(':section', $section, PDO::PARAM_STR);
        $stmt->bindParam(':code', $code, PDO::PARAM_STR);
        $stmt->execute();

        $result = FALSE;

        while ($row = $stmt->fetch()) {
            $result = new MinBid($row['code'], $row['section'], $row['minimum']);
        }

        $stmt = null;
        $conn = null;

        return $result;
    }
    // Add bid
    public function add($minbid)
    {
        $sql = 'INSERT INTO `minbid` VALUES (:code, :section, :minimum)';
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);

        $code = $minbid->getCode();
        $section = $minbid->getSection();
        $minimum = $minbid->getMinimum();

        $stmt->bindParam(':code', $code, PDO::PARAM_STR);
        $stmt->bindParam(':section', $section, PDO::PARAM_STR);
        $stmt->bindParam(':minimum', $minimum, PDO::PARAM_STR);

        $isAddOK = $stmt->execute();

        $stmt = null;
        $conn = null;

        return $isAddOK;
    }

    // Update Bid
    public function update($minbid)
    {
        $sql = 'UPDATE minbid SET minimum=:minimum WHERE section=:section AND code=:code';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);

        $code = $minbid->getCode();
        $section = $minbid->getSection();
        $minimum = $minbid->getMinimum();

        $stmt->bindParam(':code', $code, PDO::PARAM_STR);
        $stmt->bindParam(':section', $section, PDO::PARAM_STR);
        $stmt->bindParam(':minimum', $minimum, PDO::PARAM_STR);

        $isAddOK = $stmt->execute();

        $stmt = null;
        $conn = null;

        return $isAddOK;
    }

    // Remove All Bids
    public function removeAll()
    {
        $sql = 'TRUNCATE TABLE minbid';

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
