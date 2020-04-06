<?php
// POST
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

// 获取当前的UNIX毫秒时间戳
function msectime() {
    list($msec, $sec) = explode(' ', microtime());
    $msectime = (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
    return $msectime;    
}


if (isset($_POST["message"]) && isset($_POST["username"])) {
    try {
        $config_json = file_get_contents(".config.json");
        $config = json_decode($config_json, true);
    } catch (Exception $e) {
        exit('{"errcode": 110001, "errmsg": "Open config file failed! Please check your config file and permission."}');
    }

    $botCount = count($config["bots"]); // 计算config中bots的数目
    if ($botCount > 6) {
        exit('{"errcode": 110001, "errmsg": "The number of bots should be less than 6. Please check your config file."}');
    } elseif ($botCount == 0){
        exit('{"errcode": 110001, "errmsg": "You have not add any bots. Please check your config file."}');
    }

    $db = new SQLite3(".sendLog.db"); 
    $db->busyTimeout(100);
    if( !$db ) {
        exit('{"errcode": 120001, "errmsg": "Failed to open the database of sendLog!"}');
    }

    $botID = 0;
    $preBot = $db->query(<<<EOF
SELECT botID, COUNT(botID) AS num FROM sendLog
WHERE botID IN (
	SELECT botID FROM sendLog ORDER BY time DESC LIMIT 1
) ORDER BY time DESC LIMIT 20
EOF)->fetchArray(SQLITE3_ASSOC);
    if (empty($preBot)) {
        $botID = 0;
    } else if ($preBot["num"] > 18) {
        $botID = $preBot["botID"]  + 1;
    } else {
        $botID = $preBot["botID"];
    }
    if($botID >= $botCount) {
        $botID = 0;
    }
    
    $timestamp = msectime();

    $secret = $config["bots"][$botID]["secret"];
    $sign = urlencode(base64_encode(hash_hmac('sha256', "$timestamp\n$secret", $secret, true)));
    $webhook = $config["bots"][$botID]["webhookURL"]."&timestamp=$timestamp&sign=$sign";

    $user = $_POST["username"];
    $message = $_POST["message"];
    $hostURL = $config["hostURL"];
    $data = array(
        "msgtype" => "markdown",
        "markdown" => array(
            "title" => "$message",
            "text" => <<<EOF
###### [匿名] $user\n
$message\n
&nbsp;\n 
###### *点[这里]($hostURL)使用本群匿名钉服务*
EOF
        )
    );
    $data_string = json_encode($data);
    $result = send_post($webhook, $data_string);
    $sec = time();
    $db->query("INSERT INTO sendLog VALUES ('$sec', '$botID', '$message')");
    $previewTime = $sec-120; // 两分钟前的时间戳
    $db->query("DELETE FROM sendLog WHERE time < '$previewTime'"); // 删除2分钟前的数据
    $db->close();
    exit($result);
} else {
    exit('{"errcode": 110001, "errmsg": "Missing POST data."}');
}
?>