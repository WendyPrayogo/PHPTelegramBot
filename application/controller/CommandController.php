<?php
defined("BASE_PATH") OR  exit("Direct Access Forbidden");

class CommandController extends BotController {

    public function processing() {
        $this->log->logText("PrivateCommandController.Thread","processing  ".$this->tgIncomingChatId." | ".$this->tgIncomingText);
        $this->tgBot->sendMessage($id,
            "<b>Yeaahhhh!</b>\n\n$text", TG_MODE_HTML);
    }

    public function math() {
        $this->log->logText("PrivateCommandController.Thread","math  ".$this->tgIncomingChatId." | ".$this->tgIncomingText);
        $params = $this->tgBot->getCommandParams($paramsArray);
        $paramArr = json_encode($paramsArray);
        $this->tgBot->sendMessage($this->tgIncomingChatId,
            "<b>math!</b>\n\nCommand: ".$this->tgIncomingText."\nString: $params\nJSON: $paramArr", TG_MODE_HTML);
    }

    public function help() {
        $this->log->logText("PrivateCommandController.Thread","math  ".$this->tgIncomingChatId." | ".$this->tgIncomingText);
        $params = $this->tgBot->getCommandParams($paramsArray);
        $paramArr = json_encode($paramsArray);
        $help = "This is help...\nAvailable Command\n/processing\n/math\n/help\n\nThanks";
        $this->tgBot->sendMessage($this->tgIncomingChatId,
            "<b>Help</b>:\n".$help, TG_MODE_HTML);
    }

}

?>