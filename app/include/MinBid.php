<?php
class MinBid {

    private $code;
    private $section;
    private $minimum;

    public function __construct($code, $section, $minimum) {
        $this->code = $code;
        $this->section = $section;
        $this->minimum = $minimum;
    }

    public function getCode() {
        return $this->code;
    }

    public function getSection() {
        return $this->section;
    }

    public function getMinimum() {
        return $this->minimum;
    }

}

?>