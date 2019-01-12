<?php
defined("BASE_PATH") OR  exit("Direct Access Forbidden");

class InlineQueryController extends BotController {

    public function default() {
        $query = $this->tgBot->getInlineQuery($query, $offset);
        $id = $this->tgBot->getInlineQueryId();
        $queryResult01 = array(
            "type"=>"article", 
            "id"=>"111", 
            "url"=>"https://www.temansebaya.com", 
            "title"=>"Mau guwehh", 
            "input_message_content"=> array("message_text"=>"Query Mau Mau", "disable_web_page_preview"=>TRUE));
        $queryResult02 = array(
            "type"=>"article", 
            "id"=>"112", 
            "url"=>"https://www.temansebaya.com", 
            "title"=>"Terseraahhhh", 
            "input_message_content"=> array("message_text"=>"Query Hihihi"));
        $queryResult03 = array(
            "type"=>"article", 
            "id"=>"113", 
            "url"=>"https://www.temansebaya.com", 
            "title"=>"Apa ajaa bolehhh", 
            "input_message_content"=> array("message_text"=>"Query Hahah", "disable_web_page_preview"=>FALSE));
        $queryResult04 = array(
            "type"=>"article", 
            "id"=>"114", 
            "title"=>"Gituu ajaaa", 
            "description"=>"Ini adalaahhhhhh...!!!!!!", 
            "input_message_content"=> array("message_text"=>"Query Apa ajaaa"));
        $queryResult05 = array(
            "type"=>"article", 
            "id"=>"115", 
            "title"=>"Yaaa gituuu", 
            "description"=>"Ini adalaahhhhhh...!!!!!!", 
            "input_message_content"=> array("phone_number"=>"081917000000", "first_name"=>"Apaa ajaaaa"));
        $queryResultArray = array($queryResult01, $queryResult02, $queryResult03, $queryResult04, $queryResult05);
        $result = $this->tgBot->answerInlineQuery($id, $queryResultArray);
        $this->log->logText("InlineMessageController.Thread","Query  $query\n\t\t".json_encode($queryResultArray));
        $this->log->logText("InlineMessageController.Thread","Result  $query\n\t\t".json_encode($result));
    }

}

?>