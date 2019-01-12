<?php
defined("BASE_PATH") OR  exit("Direct Access Forbidden");

class DoMath {

    public function doMath($stringMath) {
        if (strchr($stringMath, ";")) return "Illegal character found!";
        if (preg_match("/[a-z]|:|\\/i", $stringMath))
            return "Invalid input! Math input can't contains character!";
        $patterns = array("/[^0-9+\-\/*,]/i");
        $math_out = preg_replace($patterns, "", $stringMath);
        $string = "\$outputs=".$math_out.";";
        $eval_ret = eval($string);
        return $outputs;    
    }

}

?>