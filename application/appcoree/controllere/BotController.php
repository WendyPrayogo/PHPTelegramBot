<?php
defined("BASE_PATH") OR  exit("Direct Access Forbidden");

require_once CORE_FUNCTION_PATH."Logger.php";

class BotController {

    protected $tgBot = NULL;
    protected $log = NULL;
    protected $tgUpdatesType = NULL;
    protected $tgBotCommand = NULL;
    protected $tgIncomingChatId = NULL;
    protected $tgIncomingText = NULL;
    protected $tgChatTitleOrName = NULL;

    public function __init_bot_controller($bot) {
        $this->log = new Logger();
        $this->log->logText("BotController.Thread","Init Bot Controller");        
        $this->tgBot = $bot;
        $this->tgIncomingChatId  = $this->tgBot->getChatId();
        $this->tgIncomingText    = $this->tgBot->getText();
        $this->tgBotCommand      = $this->tgBot->isCommand();
        $this->tgChatTitleOrName = $this->tgBot->getChatTitleOrName();
        $this->tgUpdatesType     = $this->tgBot->getUpdatesType();
    }

    public function load($libraries) {
        /** do load function */
        if (file_exists(LIBRARIES_PATH.$libraries)) {
            
        }
    }

}


?>