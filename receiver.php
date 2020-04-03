<?php
function send_post($url, $post_data) {
    $options = array(
        'http' => array(
            'method' => 'POST',
            'header' => 'Content-Type: application/json;charset=utf-8',
            'content' => $post_data,
            'timeout' => 5
        )
    );
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    return $result;
}

function msectime() {
    list($msec, $sec) = explode(' ', microtime());
    $msectime = (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
    return $msectime;    
}

if (isset($_POST["message"]) && isset($_POST["username"])) {
    $config_json = file_get_contents(".config.json");
    $config = json_decode($config_json, true);
    $timestamp = msectime();
    $secret = $config["secret"];
    $sign = urlencode(base64_encode(hash_hmac('sha256', "$timestamp\n$secret", $secret, true)));

    $webhook = $config["webhookURL"]."&timestamp=$timestamp&sign=$sign";

    $user = $_POST["username"];
    $sentence = $_POST["message"];
    $hostURL = $config["hostURL"];
    $data = array(
        "msgtype" => "markdown",
        "markdown" => array(
            "title" => "$sentence",
            "text" => <<<EOF
###### [匿名] $user\n
$sentence\n
&nbsp;\n 
###### *点[这里]($hostURL)使用本群匿名钉服务*
EOF
        )
    );
    $data_string = json_encode($data);
    $result = send_post($webhook, $data_string);  
    exit($result);
} else {
    exit('{"errcode": 310001, "errmsg":"Missing POST data."}');
}
?>