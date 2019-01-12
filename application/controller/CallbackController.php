<?php
defined("BASE_PATH") OR  exit("Direct Access Forbidden");

class CallbackController extends BotController {

    public function default() {
        $id = $this->tgIncomingChatId;
        $text = $this->tgIncomingText;
        $this->log->logText("CallbackController.Thread","default  $id | $text");
        $this->tgBot->sendMessage($id,
            "<b>Default Message Controller</b>\n\n$text", TG_MODE_HTML);
    }


}

?>