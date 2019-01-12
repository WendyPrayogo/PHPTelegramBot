<?php
    header('Content-Type: application/json');
    $len = isset($_GET['l']) ? $_GET['l'] : 128;
    $len = $len / 2;
    $bot_id = isset($_GET['b']) ? $_GET['b'] : "telegram_bot";
    $bytes  = openssl_random_pseudo_bytes($len, $cstrong);
    $token  = bin2hex($bytes);
    $id     = str_replace("=", "", base64_encode($bot_id));
    $out['token_length'] = strlen($token)." hex string";
    $out['cryptographic'] = $cstrong;
    $out['bot_id'] = $bot_id;
    $out['bot_id_encoded'] = $id;
    $out['token'] = $token;
    $out['webhook_token'] = $id."-".$token;
    $out['usages']['url'] = $_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME']."?b=bot_name_or_bot_id&l=token_length_to_generated";
    $out['usages']['help_length_token'] = "Change token_length_to_generated with Integer, default 128 [better use more than 128]";
    $out['usages']['help_bot_name_or_id'] = "Change bot_name_or_bot_id with BOT Name/BOT ID, default telegram_bot";
    $out['usages']['help_url_example'] = $_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME']."?b=bot_suka_suka&l=200";
    $out['usages']['help_finish'] = "Let's try.. Be Happy..!!";
    echo json_encode($out);
?>