<?php
defined("BASE_PATH") OR  exit("Direct Access Forbidden");
session_start(); $_SESSION['processing_tick_count_start'] = microtime(TRUE);

include CORE_CONFIGURATION_PATH."Default.php";
include CORE_FUNCTION_PATH."Logger.php";
include CORE_FUNCTION_PATH."AutoloaderClass.php";
include CORE_CONTROLLER_PATH."RestController.php";
include CORE_CONTROLLER_PATH."BotController.php";
include CORE_LIBRARIES_PATH."TelegramBot.php";

//** object definition for logger, first loaded*/
$log   = new Logger(); $start_microtime = microtime(TRUE); $log->logStart($start_microtime);
//** variable definition */
$appConfiguration = parse_ini_file(CORE_CONFIGURATION_PATH."config.ini", TRUE);
$tgBotToken = $appConfiguration['application']['telegram_bot_token'];
$tgWebhookToken = $appConfiguration['application']['telegram_bot_webhook_token'];
$tgWebhookAcceptedMethod = "NA".$appConfiguration['application']['accepted_method'];
$tgBotCommand = NULL;
$methodCall = "default";
//** object definition */
$rest  = new RestController();
$tgBot = new TelegramBot($rest, $tgBotToken);
$aload = new AutoloaderClass();
//** logg processing time */
//** start app */
$aload->loadController();
/** default PHP, using PATH_INFO*/
$serverPathInfo = isset($_SERVER["PATH_INFO"]) ? $_SERVER["PATH_INFO"] : "/";
/** others user REQUEST_URI*/
// $serverPathInfo = isset($_SERVER["REQUEST_URI"]) ? $_SERVER["REQUEST_URI"] : "/";
$requestMethod = isset($_SERVER["REQUEST_METHOD"]) ? $_SERVER["REQUEST_METHOD"] : "NAV";
$requestPath = explode("/", $serverPathInfo);
$isMethod = strpos($tgWebhookAcceptedMethod, $requestMethod);
$log->logText("Main.Thread","Incoming Updates");
if ($requestPath[1] === $tgWebhookToken &&  $isMethod) {
    $log->logText("Main.Thread","Token Valid, Do Main");
    $requestUpdates   = $rest->getJSON();
    $updatesType      = $tgBot->checkUpdateType($requestUpdates);
    $tgIncomingChatId = $tgBot->getChatId();
    $tgIncomingText   = $tgBot->getText();
    $chatTitleOrName  = $tgBot->getChatTitleOrName();
    if ($tgBotCommand = $tgBot->isCommand()) {
        $methodCall   = $tgBotCommand;
        $classCall    = "CommandController";
    } else if ($updatesType == TG_GROUP_TEXT_MESSAGE) $classCall = "GroupMessageController";
    else if ($updatesType == TG_PRIVATE_TEXT_MESSAGE) $classCall = "PrivateMessageController";
    else if ($updatesType == TG_SUPERGROUP_TEXT_MESSAGE) $classCall = "SupergroupMessageController";
    else if ($updatesType == TG_CHANNEL_POST) $classCall = "ChannelMessageController";
    else if ($updatesType == TG_CALLBACK_QUERY) $classCall = "CallbackController";
    else if ($updatesType == TG_INLINE_QUERY) $classCall = "InlineQueryController";
    else $log->logText("Main.Thread","Not found anything to Route!!!");
    $log->logText("Main.Thread","before check callable, Type: $updatesType > $classCall > $methodCall\n\t\tMessage: $tgIncomingText | From: $chatTitleOrName");
    if ($loadedClass = $aload->isCallable($classCall, $methodCall)) {
        $log->logText("Main.Thread","Class and Method available, call it..!!");
        $rest->putJSON(array("status"=> "valid", "route"=>"yes", "method"=>$methodCall));
        $loadedClass->__init_bot_controller($tgBot);
        $loadedClass->$methodCall();
    } else {
        $log->logText("Main.Thread","Class and Method not available, send default message!!");
        $rest->putJSON(array("status"=> "invalid", "route"=>"no"));
        if ($classCall == "PrivateMessageController" || $classCall == "GroupMessageController" || $classCall == "SupergroupMessageController")
            $tgBot->sendMessage($tgIncomingChatId, "<b>Sorry, we can't process this message.</b>\n\nRequest: $tgIncomingText", TG_MODE_HTML); 
        else if ($classCall == "CommandController")
            $tgBot->sendMessage($tgIncomingChatId, "<b>Sorry, we can't process this command.</b>\n\nRequest: $tgIncomingText", TG_MODE_HTML); 
    }
} else {
    $log->logText("Main.Thread","token invalid");
    $rest->putJSON(array("status"=> "invalid", "message"=>$isMethod ? "Check your telegram webhook token!" : "Request method not supported"));
}

//** destructor, freeeeee */
$aload->unloadController(); $aload = NULL;
$rest->destruct();  $rest = NULL;
$tgBot->destruct(); $tgBot = NULL;
//** uncomment if single session */
session_unset();
//** logg processing time */
$end_microtime = microtime(TRUE); $log->logEnd($end_microtime-$start_microtime);
$log->destruct();   $log = NULL;
?>
