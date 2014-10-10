<?php

class prompt {

    public static function state($phrase) {
        echo PHP_EOL . $phrase;
    }

    public static function ask($phrase) {
        if($phrase){
            echo PHP_EOL . $phrase . " ";
        }
        $fp = fopen("php://stdin","r");
        $line = rtrim(fgets($fp, 1024));
        return $line;
    }

    public static function askif($phrase) {
        $line = prompt::ask($phrase . " [y/N]");
        return preg_match("|^[yY]|", $line);
    }

    public static function printexception(Exception $e) {
        echo "Exception: " . $e->getMessage() . PHP_EOL;
        echo "Location: " . $e->getFile() . ', line ' .  $e->getLine() . PHP_EOL;
        echo "Trace: " . PHP_EOL . $e->getTraceAsString() . PHP_EOL;
    }

}
