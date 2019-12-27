<?php

require_once 'common.php';

class BidDAO
{

    const bid_header = ['userid', 'amount', 'code', 'section'];

    // Retrieve All Bids
    public function retrieveAll()
    {
        $sql = 'SELECT userid,(CAST(amount as DECIMAL(38,2))) as `amount`,code,section FROM bid ORDER BY `code`,`section`,`amount` desc,`userid`';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $result = array();

        while ($row = $stmt->fetch()) {
            $result[] = new Bid($row['userid'], $row['amount'], $row['code'], $row['section']);
        }

        $stmt = null;
        $conn = null;

        return $result;
    }

    // Retrieve all bids by USERID
    public function retrieve($userid)
    {
        $sql = 'SELECT userid,(CAST(amount as DECIMAL(38,2))) as `amount`,code,section FROM bid WHERE `userid`=:userid ORDER BY `userid` ';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
        $stmt->execute();

        $result = array();

        while ($row = $stmt->fetch()) {
            $result[] = new Bid($row['userid'], $row['amount'], $row['code'], $row['section']);
        }

        $stmt = null;
        $conn = null;

        return $result;
    }
        // Retrieve all bids by code
        public function retrieve_code($code)
        {
            $sql = 'SELECT * FROM bid WHERE `code`=:code ORDER BY `userid` ';

            $connMgr = new ConnectionManager();
            $conn = $connMgr->getConnection();

            $stmt = $conn->prepare($sql);
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $stmt->bindParam(':code', $code, PDO::PARAM_STR);
            $stmt->execute();

            $result = array();

            while ($row = $stmt->fetch()) {
                $result[] = new Bid($row['userid'], $row['amount'], $row['code'], $row['section']);
            }

            $stmt = null;
            $conn = null;

            return $result;
        }
    // Retrieve specfic bid by userid, code, section
    public function retrieve_specific($userid, $code, $section)
    {
        $sql = 'SELECT * FROM bid WHERE `userid`=:userid AND `section` = :section AND `code` = :code ORDER BY `userid` ';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
        $stmt->bindParam(':section', $section, PDO::PARAM_STR);
        $stmt->bindParam(':code', $code, PDO::PARAM_STR);
        $stmt->execute();

        $result = FALSE;

        if ($row = $stmt->fetch()) {
            $result = new Bid($row['userid'], $row['amount'], $row['code'], $row['section']);
        }

        $stmt = null;
        $conn = null;

        return $result;
    }
    // Retrieve specfic bid by userid, code
    public function retrieve_userid_code($userid, $code)
    {
        $sql = 'SELECT * FROM bid WHERE `userid`=:userid AND `code` = :code ORDER BY `userid` ';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
        $stmt->bindParam(':code', $code, PDO::PARAM_STR);
        $stmt->execute();

        $result = FALSE;

        if ($row = $stmt->fetch()) {
            $result = new Bid($row['userid'], $row['amount'], $row['code'], $row['section']);
        }

        $stmt = null;
        $conn = null;

        return $result;
    }
    // Retrieve bids by code, section
    public function retrieve_section($section, $code){
        $sql = 'SELECT * FROM bid WHERE `section` = :section AND `code` = :code ORDER BY `amount` desc, `userid`';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->bindParam(':section', $section, PDO::PARAM_STR);
        $stmt->bindParam(':code', $code, PDO::PARAM_STR);
        $stmt->execute();

        $result = [];

        while ($row = $stmt->fetch()) {
            $result[] = new Bid($row['userid'], $row['amount'], $row['code'], $row['section']);
        }

        $stmt = null;
        $conn = null;

        return $result;
    }
    // Add bid
    public function add($bid)
    {
        $sql = 'INSERT INTO bid(userid, amount, code, section) VALUES (:userid, :amount, :code, :section)';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);

        $id = $bid->getID();
        $amount = $bid->getAmount();
        $code = $bid->getCode();
        $section = $bid->getSection();

        $stmt->bindParam(':userid', $id, PDO::PARAM_STR);
        $stmt->bindParam(':amount', $amount, PDO::PARAM_STR);
        $stmt->bindParam(':code', $code, PDO::PARAM_STR);
        $stmt->bindParam(':section', $section, PDO::PARAM_STR);

        $isAddOK = $stmt->execute();

        $stmt = null;
        $conn = null;

        return $isAddOK;
    }

    // Update Bid
    public function update($bid)
    {
        $sql = 'UPDATE bid SET amount=:amount,section=:section WHERE userid=:userid AND code=:code';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);

        $id = $bid->getID();
        $amount = $bid->getAmount();
        $code = $bid->getCode();
        $section = $bid->getSection();

        $stmt->bindParam(':userid', $id, PDO::PARAM_STR);
        $stmt->bindParam(':amount', $amount, PDO::PARAM_STR);
        $stmt->bindParam(':code', $code, PDO::PARAM_STR);
        $stmt->bindParam(':section', $section, PDO::PARAM_STR);

        $isAddOK = $stmt->execute();

        $stmt = null;
        $conn = null;

        return $isAddOK;
    }

    // Drop bid
    public function drop($userid, $code, $section)
    {

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $sql = 'DELETE FROM bid WHERE userid = :userid AND section=:section AND code=:code';
        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
        $stmt->bindParam(':code', $code, PDO::PARAM_STR);
        $stmt->bindParam(':section', $section, PDO::PARAM_STR);

        $result = $stmt->execute();
        // encountered error

        $stmt = null;
        $conn = null;

        return $result;
    }


    // Remove All Bids
    public function removeAll()
    {
        $sql = 'TRUNCATE TABLE bid';

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
