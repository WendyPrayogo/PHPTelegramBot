<?php
defined("BASE_PATH") OR  exit("Direct Access Forbidden");

require_once CORE_FUNCTION_PATH."Logger.php";
define("TG_SEND_MESSAGE", "sendMessage");
define("TG_EDIT_MESSAGE", "editMessage");
define("TG_ANSWER_QUERY_MESSAGE", "answerCallbackQuery");
define("TG_ANSWER_INLINE_QUERY", "answerInlineQuery");
define("TG_GET_ME", "getMe");
define("TG_PRIVATE_TEXT_MESSAGE", "privatemessage");
define("TG_GROUP_TEXT_MESSAGE", "groupmessage");
define("TG_SUPERGROUP_TEXT_MESSAGE", "supergroupmessage");
define("TG_CHANNEL_POST", "channel_post");
define("TG_COMMAND_MESSAGE", "command");
define("TG_TEXT_MESSAGE", "textmessage");
define("TG_CALLBACK_QUERY", "callback_query");
define("TG_INLINE_QUERY", "inline_query");
define("TG_NEW_CHAT_MEMBER", "newmember");
define("TG_LEFT_CHAT_MEMBER", "leftmember");
define("TG_NOT_FOUND", "null_not_found");
define("TG_MODE_MARKDOWN", "Markdown");
define("TG_MODE_HTML", "HTML");
define("TG_INLINE_QUERY_RESULT_ARTICLE", "inlineQueryResultArticle");

class TelegramBot {

    private $api_url = "api.telegram.org";
    private $bot_token = "bot:token";
    private $api_full_url = "";
    private $restRQ = NULL;
    private $tg_updates = NULL;
    private $tg_updates_type = NULL;
    private $log = NULL;
    private $tg_bot_command = NULL;
    private $tg_bot_incoming_command = NULL;
    private $tg_message_is_command = FALSE;
    private $tg_bot_name_from_command = NULL;

    public function __construct($restController, $botToken = NULL, $apiUrl = NULL) {
        $this->log = new Logger();
        $this->restRQ = $restController;
        $this->api_url = $apiUrl == NULL ? $this->api_url : $apiUrl;
        $this->bot_token = $botToken == NULL ? $this->bot_token : $botToken;
        $this->api_full_url = "https://".$this->api_url."/bot".$this->bot_token."/";
        $this->log->logText("TelegramBot.Thread","Init - End : $this->api_url [edited > $apiUrl]");
        //$this->log->logText("RestController.Thread","Init - End : ".$this->api_url." | ".$botToken." [edited > ".$apiUrl."]");
    }

    public function getMe() {
        $url_to_send = $this->api_full_url.TG_GET_ME;
        $return = $this->restRQ->sendRequest($url_to_send);
        $this->log->logText("TelegramBot.Thread","getMe response\n\t\t$return");
        return $return;
    }

    private function send($chat_id, $text, $mode = NULL, $options = NULL) {
        if ($options != NULL) $message = $options;
        if ($mode != NULL) $message['parse_mode'] = $mode;
        $message['chat_id'] = $chat_id;
        $message['text'] = $text;
        $json_message = json_encode($message);
        $url_to_send = $this->api_full_url.TG_SEND_MESSAGE;
        $return = $this->restRQ->sendRequest($url_to_send, $json_message);
        $this->log->logText("TelegramBot.Thread","send response\n\t\t$json_message\n\t\t$return");
        return $return;
    }

    private function edit($chat_id, $message_id, $text, $mode = NULL, $options = NULL) {
        if ($options != NULL) $message = $options;
        if ($mode != NULL) $message['parse_mode'] = $mode;
        $message['chat_id'] = $chat_id;
        $message['message_id'] = $message_id;
        $message['text'] = $text;
        $json_message = json_encode($message);
        $url_to_send = $this->api_full_url.TG_EDIT_MESSAGE;
        $return = $this->restRQ->sendRequest($url_to_send, $json_message);
        $this->log->logText("TelegramBot.Thread","edit response\t\t$json_message\n\t\t$return");
        return $return;
    }

    public function answerQuery($callback_id, $text, $show_alert = false) {
        $message['callback_query_id'] = $callback_id;
        $message['text'] = $text;
        $message['show_alert'] = $show_alert;
        $json_message = json_encode($message);
        $url_to_send = $this->api_full_url.TG_ANSWER_QUERY_MESSAGE;
        $return = $this->restRQ->sendRequest($url_to_send, $json_message);
        $this->log->logText("TelegramBot.Thread","answerQuery\n $json_message \n $return");
        return $return;
    }
    
    public function sendMessage($chat_id, $text, $mode = NULL) {
        return $this->send($chat_id, $text, $mode);
    }

    public function sendWithMarkup($chat_id, $text, $mode = NULL, $markup = NULL) {
        $options['reply_markup'] = $markup;
        return $this->send($chat_id, $text, $mode, $options);
    }

    public function editWithMarkup($text, $last_message, $mode = NULL, $markup = NULL) {
        // 
        $options['reply_markup'] = $markup;
        return $this->edit($chat_id, $message_id, $text, $mode, $options);
    }

    public function editMessage($text, $last_message, $mode = NULL) {
        // 
        return $this->edit($chat_id, $message_id, $text, $mode);
    }

    public function answerInlineQuery($id, $result) {
        if ($this->tg_updates_type == TG_INLINE_QUERY) {
            $message['inline_query_id'] = $id;
            $message['results'] = $result;
            $message['cache_time'] = 1000;
            $message['next_offset'] = "";
            $message['is_personal'] = FALSE;
            $json_message = json_encode($message);
            $url_to_send = $this->api_full_url.TG_ANSWER_INLINE_QUERY;
            $return = $this->restRQ->sendRequest($url_to_send, $json_message);
            $this->log->logText("TelegramBot.Thread","send response\n\t\t$json_message\n\t\t$return");
            return $return;    
        } else return NULL;
    }

    public function checkUpdateType($update) {
        $this->tg_updates = $update;
        if (isset($this->tg_updates['message'])) {
            if ($this->tg_updates['message']['chat']['type'] == 'group') {
                $this->tg_updates_type = TG_GROUP_TEXT_MESSAGE;
                return TG_GROUP_TEXT_MESSAGE;
            } else if (($this->tg_updates['message']['chat']['type'] == 'private')) {
                $this->tg_updates_type = TG_PRIVATE_TEXT_MESSAGE;
                return TG_PRIVATE_TEXT_MESSAGE;
            } else if ($this->tg_updates['message']['chat']['type'] == 'supergroup') {
                $this->tg_updates_type = TG_SUPERGROUP_TEXT_MESSAGE;
                return TG_SUPERGROUP_TEXT_MESSAGE;
            }
            return TG_NOT_FOUND;
        } else if (isset($this->tg_updates['callback_query'])) {
            $this->tg_updates_type = TG_CALLBACK_QUERY;
            return TG_CALLBACK_QUERY;
        } else if(isset($this->tg_updates['inline_query'])) {
            $this->tg_updates_type = TG_INLINE_QUERY;
            return TG_INLINE_QUERY;
        } else if (isset($this->tg_updates['channel_post'])) {
            $this->tg_updates_type = TG_CHANNEL_POST;
            return TG_CHANNEL_POST;
        }
        return TG_NOT_FOUND;
    }

    // from 
    public function getFromUserName() {
        if ($this->tg_updates_type != TG_CHANNEL_POST) {
            return $this->tg_updates['message']['from']['username'];
        } else return "Channel Post";
    }

    public function getFromLastName() {
        if ($this->tg_updates_type != TG_CHANNEL_POST) {
            return $this->tg_updates['message']['from']['last_name'];
        } else return "Channel Post";
    }

    public function getFromFirstName() {
        if ($this->tg_updates_type != TG_CHANNEL_POST) {
            return $this->tg_updates['message']['from']['first_name'];
        } else return "Channel Post";
    }

    public function getFromFullName() {
        if ($this->tg_updates_type != TG_CHANNEL_POST) {
            $first_name = $this->tg_updates['message']['from']['first_name'];
            $last_name = $this->tg_updates['message']['from']['last_name'];
            return $first_name." ".$last_name;
        } else return "Channel Post";
    }

    public function getChatId() {
        if (isset($this->tg_updates['message'])) {
            return $this->tg_updates['message']['chat']['id'];
        } else if(isset($this->tg_updates['channel_post'])) {
            return $this->tg_updates['message']['chat']['id'];
        } else return NULL;
    }

    public function getUpdatesType() {
        return $this->tg_updates_type;
    }

    public function getChatTitleOrName() {
        if ($this->tg_updates_type == TG_PRIVATE_TEXT_MESSAGE) {
            $first_name = $this->tg_updates['message']['chat']['first_name'];
            $last_name = $this->tg_updates['message']['chat']['last_name'];
            return $first_name." ".$last_name;
        } else if ($this->tg_updates_type == TG_CHANNEL_POST) {
            return "Channel ".$this->tg_updates['message']['chat']['title'];
        } else if ($this->tg_updates_type == TG_GROUP_TEXT_MESSAGE) {
            return "Group ".$this->tg_updates['message']['chat']['title'];
        } return NULL;
    }

    public function getChatUsername() {
        if ($this->tg_updates_type == TG_PRIVATE_TEXT_MESSAGE) {
            $tmp = $this->tg_updates['message']['chat']['username'];
            return isset($tmp) ? $tmp : NULL;
        } else if ($this->tg_updates_type == TG_CHANNEL_POST) {
            return "Channel ".$this->tg_updates['message']['chat']['title'];
        } else if ($this->tg_updates_type == TG_GROUP_TEXT_MESSAGE) {
            return "Group ".$this->tg_updates['message']['chat']['title'];
        } return NULL;
    }

    public function getChatFirstName() {
        if ($this->tg_updates_type == TG_PRIVATE_TEXT_MESSAGE) {
            $tmp = $this->tg_updates['message']['chat']['first_name'];
            return isset($tmp) ? $tmp : NULL;
        } else if ($this->tg_updates_type == TG_CHANNEL_POST) {
            return "Channel ".$this->tg_updates['message']['chat']['title'];
        } else if ($this->tg_updates_type == TG_GROUP_TEXT_MESSAGE) {
            return "Group ".$this->tg_updates['message']['chat']['title'];
        } return NULL;
    }

    public function getChatLastName() {
        if ($this->tg_updates_type == TG_PRIVATE_TEXT_MESSAGE) {
            $tmp = $this->tg_updates['message']['chat']['last_name'];
            return isset($tmp) ? $tmp : NULL;
        } else if ($this->tg_updates_type == TG_CHANNEL_POST) {
            return "Channel ".$this->tg_updates['message']['chat']['title'];
        } else if ($this->tg_updates_type == TG_GROUP_TEXT_MESSAGE) {
            return "Group ".$this->tg_updates['message']['chat']['title'];
        } return NULL;
    }

    public function getChatFullName() {
        if ($this->tg_updates_type == TG_PRIVATE_TEXT_MESSAGE) {
            $first_name = $this->tg_updates['message']['chat']['first_name'];
            $last_name = $this->tg_updates['message']['chat']['last_name'];
            return $fisrt_name." ".$last_name;
        } else if ($this->tg_updates_type == TG_CHANNEL_POST) {
            return "Channel ".$this->tg_updates['message']['chat']['title'];
        } else if ($this->tg_updates_type == TG_GROUP_TEXT_MESSAGE) {
            return "Group ".$this->tg_updates['message']['chat']['title'];
        } return NULL;
    }

    public function getText() {
        if (isset($this->tg_updates['message'])) {
            return isset($this->tg_updates['message']['text']) ? $this->tg_updates['message']['text'] : NULL;
        } else if(isset($this->tg_updates['channel_post'])) { 
            return isset($this->tg_updates['channel_post']['text']) ? $this->tg_updates['channel_post']['text'] : NULL;
        } else return NULL;
    }

    public function isCommand() {
        if (isset($this->tg_updates['message'])) {
            if (isset($this->tg_updates['message']['entities'])) {
                if ($this->tg_updates['message']['entities'][0]['type'] == 'bot_command') {
                    $command = $this->tg_updates['message']['text'];
                    $this->tg_message_is_command = TRUE;
                    $this->log->logText("TelegramBot.Thread","bot_command > $command");
                    $this->tg_bot_incoming_command = explode(" ", $command);
                    $cmd = substr($this->tg_bot_incoming_command[0], 1);
                    $cmdArray = explode("@", $cmd);
                    $this->tg_bot_command = isset($cmdArray[0]) ? $cmdArray[0] : NULL;
                    $this->tg_bot_name_from_command = isset($cmdArray[1]) ? $cmdArray[1] : NULL;
                    $this->log->logText("TelegramBot.Thread","Bot Command > ".$this->tg_bot_command);
                    return $this->tg_bot_command;
                }
            } else return FALSE;
        }
        return FALSE;
    }    

    public function getCommand() {
        if ($this->tg_message_is_command) {
            return $this->tg_bot_command;
        } else return NULL;
    }

    public function getCommandParams(&$tg_bot_command_param_array = NULL) {
        if ($this->tg_message_is_command) {
            $paramConcatenated = "";
            if (count($this->tg_bot_incoming_command) < 2) return "";
            for ($i=1; $i<count($this->tg_bot_incoming_command); $i++) {
                $paramConcatenated = $paramConcatenated." ".$this->tg_bot_incoming_command[$i];
                $tg_bot_command_param_array[$i-1] = $this->tg_bot_incoming_command[$i];
            }
            return $paramConcatenated;
        } else return NULL;
    }

    public function getInlineQuery(&$query = "", &$offset = "") {
        if ($this->tg_updates_type == TG_INLINE_QUERY) {
            $query  = $this->tg_updates['inline_query']['query'];
            $offset = $this->tg_updates['inline_query']['offset'];
            return $query;
        } else return NULL;
    }

    public function getInlineQueryId() {
        if ($this->tg_updates_type == TG_INLINE_QUERY) {
            $id = $this->tg_updates['inline_query']['id'];
            return $id;
        } else return NULL;
    }

    public function destruct() {
        
    }
}

?>