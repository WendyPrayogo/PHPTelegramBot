<?php
defined("BASE_PATH") OR  exit("Direct Access Forbidden");

class GroupMessageController extends BotController {

    public function default() {
        $id = $this->tgIncomingChatId;
        $text = $this->tgIncomingText;
        $this->log->logText("GroupMessageController.Thread","default  $id | $text");
        $this->tgBot->sendMessage($id,
            "<b>Default Message Controller</b>\n\n$text", TG_MODE_HTML);
    }

}

?>