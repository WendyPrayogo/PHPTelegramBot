# PHPTelegramBot
PHP Telegram Bot

### How To  
Set you Webhook and Telegram Bot Token in configuration files on folder  \
__/application/appcoree/confige/config.ini__

Changes this field to your configuration  \
`accepted_method = "GET|POST"`  \
`telegram_bot_token = "bot_token"`  \
`telegram_bot_webhook_token = "webhook_web_token"` \
  \
__Controller__  \
Controllers class on folder __application/controller/__ \
Default Controller for this bot
* Command -> CommandController.php  
* Group Message -> GroupMessageController.php  
* SuperGroup Message -> SuperGroupMessageController.php (not tested)  
* Private Message -> PrivateMessageController.php  
* Inline Query -> InlineQueryController.php  
* Callback Query -> CallbackController.php  


__WORKS IN PROGRESS__
* Model Functions
* View Functions
* Database Functions
* and others

Sample Bot using this code >> https://t.me/TPersonal_Bot

__Links__  
https://core.telegram.org/bots  
https://core.telegram.org/bots/api  

__Thanks__
