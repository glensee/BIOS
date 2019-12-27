<?php
require_once 'common.php';
require_once 'token.php';

function clearing($n){
    $bidDAO = new BidDAO();
    $sectionDAO = new SectionDAO();
    $sectionStudentDAO = new SectionStudentDAO();
    $studentDAO = new StudentDAO();
    $minBidDAO = new MinBidDAO();
    $bids = $bidDAO->retrieveAll();

    if ($bids){
        $temp = [];
        $course = $bids[0]->getCode();
        $section = $bids[0]->getSection();
        $totalSeats = $sectionDAO->retrieve($course,$section)->getSize();
        $enrolled = count($sectionStudentDAO->retrieve_enrollment($course,$section));
        $size = $totalSeats - $enrolled;

        foreach($bids as $bid){
            $c = $bid->getCode();
            $s = $bid->getSection();
            $amt = $bid->getAmount();

            if ($c == $course && $s == $section){
                $temp[] = $bid;
            }

            else{

                $clearing_price = 0;
                # number of bids exceed section size
                if ( $n == 1 && $size > 1 && count($temp) >= $size && $temp[$size-1]->getAmount() == $temp[$size-2]->getAmount()){
                    $clearing_price = $temp[$size-1]->getAmount();
                }
                else if (count($temp) > $size){
                    $clearing_price = $temp[$size]->getAmount();
                }
                foreach ($temp as $temp_bid){
                    $temp_amt = $temp_bid->getAmount();
                    $temp_c = $temp_bid->getCode();
                    $temp_s = $temp_bid->getSection();

                    if ($temp_amt > $clearing_price){
                        $sectionStudentDAO->add(    new SectionStudent( $temp_bid->getID(), $temp_c, $temp_s, $temp_amt)   );
                    }
                    else{
                        $temp_id = $temp_bid->getID();
                        $stu = $studentDAO->retrieve($temp_id);
                        $currMoney = $stu->geteDollar();
                        $orgMoney = $temp_amt + $currMoney;
                        $studentDAO->update($temp_id, $orgMoney);
                    }
                }

                $temp = [$bid];
                $course = $bid->getCode();
                $section = $bid->getSection();
                $size = $sectionDAO->retrieve($course,$section)->getSize();
            }
        }

        $clearing_price = 0;
        # number of bids exceed section size
        if ( $n == 1 && $size > 1 &&  count($temp) >= $size && $temp[$size-1]->getAmount() == $temp[$size-2]->getAmount()){
            $clearing_price = $temp[$size-1]->getAmount();
        }
        else if (count($temp) > $size){
            $clearing_price = $temp[$size]->getAmount();
        }
        foreach ($temp as $temp_bid){
            $temp_amt = $temp_bid->getAmount();
            $temp_c = $temp_bid->getCode();
            $temp_s = $temp_bid->getSection();

            if ($temp_amt > $clearing_price){
                $sectionStudentDAO->add(    new SectionStudent( $temp_bid->getID(), $temp_c, $temp_s, $temp_amt)   );
            }
            else{
                $temp_id = $temp_bid->getID();
                $stu = $studentDAO->retrieve($temp_id);
                $currMoney = $stu->geteDollar();
                $orgMoney = $temp_amt + $currMoney;
                $studentDAO->update($temp_id, $orgMoney);
            }
        }
    }

}
?>