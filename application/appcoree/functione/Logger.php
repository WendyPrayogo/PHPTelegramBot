<?php
defined("BASE_PATH") OR  exit("Direct Access Forbidden");

class Logger {

    public function logJson($from, $json) {
        $datetime = date('Y-m-d H:i:s');
        $text_to_write = "JLog> $datetime > $from\n$text\n";
        $this->doLog($text_to_write);
    }

    public function logText($from, $text) {
        $datetime = date('Y-m-d H:i:s');
        $text_to_write = "TLog > $datetime > $from:$text\n";
        $this->doLog($text_to_write);
    }

    public function log($text) {
        $datetime = date('Y-m-d H:i:s');
        $text_to_write = "Log  > $datetime > $text\n";
        $this->doLog($text_to_write);
    }

    public function logStart($text) {
        $datetime = date('Y-m-d H:i:s');
        $microtime = microtime(TRUE);
        $text_to_write = "STARTSTARTSTARTSTARTSTARTSTART $datetime > Time Start: $text (microseconds) | $microtime\n";
        $this->doLog($text_to_write);
    }

    public function logEnd($text) {
        $datetime = date('Y-m-d H:i:s');
        $microtime = microtime(TRUE);
        $text_to_write = "ENDENDENDENDENDENDENDENDENDEND $datetime > Processing Time: $text (second)  | $microtime\n";
        $this->doLog($text_to_write);
    }

    private function doLog($textToWrite) {
        $date = date(LOGGER_DATE_FORMAT);
        if (!file_exists(LOGGER_PATH)) mkdir(LOGGER_PATH);
        $loggerFile = LOGGER_PATH.LOGGER_PREFIX_NAME."__".$date.".".LOGGER_EXTENTION_FILE;
        $file = fopen($loggerFile, "a");
        fwrite($file, $textToWrite);
        fclose($file);
    }

    public function destruct() {
        
    }

}

?>