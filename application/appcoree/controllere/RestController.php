<?php
defined("BASE_PATH") OR  exit("Direct Access Forbidden");
require_once CORE_FUNCTION_PATH."Logger.php";

class RestController {
    
    private $log = NULL;

    public function __construct() {
        $this->log  = new Logger();
        $this->log->logText("RestController.Thread","Construct, Class Init");
    }

    public function putJSON($json_data, $ok = "ok") {
        header('Content-Type: application/json');
        $json_out['ok'] = $ok;
        $json_out['response'] = $json_data;
        $jsonData = json_encode($json_out);
        $this->log->logText("RestController.Thread","Done - putJSON \n\t\t$jsonData");
        echo $jsonData;
    }

    public function getJSON() {
        $php_input = file_get_contents('php://input');
        $this->log->logText("RestController.Thread","Done - getJSON \n\t\t$php_input");
        return json_decode($php_input, TRUE);
    }

    public function sendRequest($url, $body_fields = NULL, $method = "POST", $infile = "", $encoding = "", $maxredirs = 10, $timeout = 30) {
        $microtime = microtime(TRUE);
        $this->log->logText("RestController.Thread","Init - sendRequest >  [ ".$method." ] | $microtime");
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_ENCODING => $encoding,
            CURLOPT_MAXREDIRS => $maxredirs,
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "cache-control: no-cache"),
        ));
        if ($body_fields != NULL ) {
            $this->log->logText("RestController.Thread","Init - sendRequest - Body Fields > $body_fields");
            curl_setopt($curl, CURLOPT_POSTFIELDS, $body_fields);
        }
        if ($method == "POST") curl_setopt($curl, CURLOPT_POST, TRUE);
        else if ($method == "GET") curl_setopt($curl, CURLOPT_HTTPGET, TRUE);
        else if ($method == "HEAD") curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "HEAD");
        else if ($method == "DELETE") curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
        else if ($method == "PUT") {
            // to be later, $infilesize
            $infilesize = 0;
            curl_setopt_array($curl, array(
                CURLOPT_PUT => TRUE,
                CURLOPT_INFILE => $infile,
                CURLOPT_INFILESIZE => $infilesize
            ));
        }
        $response = curl_exec($curl);
        $err      = curl_error($curl);
        curl_close($curl);
        $microtime = microtime(TRUE);
        if ($err) {
            $this->log->logText("RestController.Thread","CURL Err. - | $microtime |sendRequest >\n\t\t$err");
            return FALSE;
        } else {
            $this->log->logText("RestController.Thread","CURL Rsp. - | $microtime | sendRequest >\n\t\t$response");
            return $response;
        }
    }

    public function destruct() {
        
    }    
}
?>