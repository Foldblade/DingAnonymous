<!DOCTYPE html>
<html lang="zh-Hans">
<head>
    <meta charset="UTF-8">
    <!--Let browser know website is optimized for mobile-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <!-- Favicon -->
    <link rel="shortcut icon" href="img/favicon.ico">

    <!-- Import MDUI -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/mdui@0.4.3/dist/css/mdui.min.css">
    <script src="https://cdn.jsdelivr.net/npm/mdui@0.4.3/dist/js/mdui.min.js"></script>

    <!-- Import Google Icon Font -->
    <link href="https://fonts.googleapis.cnpmjs.org/icon?family=Material+Icons" rel="stylesheet">

    <title>匿名钉</title>

</head>
<?php 
    $config_json = file_get_contents(".config.json");
    $config = json_decode($config_json, true);
?>
<body class="mdui-theme-layout-dark mdui-theme-primary-light-blue mdui-theme-accent-cyan">
<header>
    <nav class="mdui-appbar">
        <div class="mdui-toolbar mdui-color-theme">
            <span class="mdui-btn mdui-btn-icon" disabled><img src="img/favicon.ico" style="width: 80%; padding-left: 10%; padding-top: 10%;"></span>
            <div class="mdui-typo-headline">匿名钉</div>
            <span class="mdui-typo-title mdui-hidden-sm-down">此服务属于：<?php echo $config["owner"]; ?></span>
            <div class="mdui-toolbar-spacer mdui-hidden-sm-down"></div>
        </div>
    </nav>
</header>

<main>
    <div class="mdui-container">
        <div class="mdui-row">
            <div class="mdui-card mdui-col-xs-12 mdui-col-sm-8 mdui-col-offset-sm-2 mdui-m-y-3">
                <!-- 卡片的标题和副标题 -->
                <div class="mdui-card-primary">
                    <div class="mdui-card-primary-title">发送匿名消息</div>
                    <div class="mdui-card-primary-subtitle">Send anonymous message</div>
                </div>

                <!-- 卡片的内容 -->
                <div class="mdui-card-content">
                    <div class="mdui-container">
                        <div class="mdui-row">
                            <form>
                                    <div class="mdui-textfield mdui-textfield-floating-label mdui-col-xs-12">
                                        <i class="mdui-icon material-icons">account_circle</i>
                                        <label class="mdui-textfield-label">匿名用户名</label>
                                        <input class="mdui-textfield-input" type="text" name="username" id="username" maxlength="40"/>
                                        <div class="mdui-textfield-helper">输入一个心仪的用户名</div>
                                    </div>
                                <div class="mdui-textfield mdui-textfield-floating-label mdui-col-xs-12">
                                    <i class="mdui-icon material-icons">textsms</i>
                                    <label class="mdui-textfield-label">要发送的消息</label>
                                    <textarea class="mdui-textfield-input" name="message" id="message" maxlength="500"></textarea>
                                    <div class="mdui-textfield-helper">支持Markdown语法</div>
                                </div>
                                <div class="mdui-col-xs-12 mdui-m-y-3">
                                    <div class="mdui-typo-caption-opacity mdui-m-y-1">⚠ 由于钉钉机器人的频率限制，全群每分钟仅可通过匿名机器人发送20条消息；若不幸超过限制，则您的消息将不会发送。</div>
                                    <button class="mdui-btn mdui-btn-block mdui-color-theme-accent mdui-ripple" type="button" onclick="sendMessage();" id="send">发送</button>
                                </div>
                                <div class="mdui-text-center mdui-m-y-2 mdui-col-xs-12 mdui-hidden" id="loading">
                                    <div class="mdui-spinner mdui-spinner-colorful"></div>
                                </div>
                            </form>
                        </div>
                    </div> 
                </div>

            </div>

            <div class="mdui-card mdui-col-xs-12 mdui-col-sm-8 mdui-col-offset-sm-2 mdui-m-y-3">
                <!-- 卡片的标题和副标题 -->
                <div class="mdui-card-primary">
                    <div class="mdui-card-primary-title">历史记录</div>
                    <div class="mdui-card-primary-subtitle">View history</div>
                </div>

                <!-- 卡片的内容 -->
                <div class="mdui-card-content">
                    <div class="mdui-container">
                        <div class="mdui-row">
                            <div class="mdui-col-xs-12">
                                <div class="mdui-typo-caption-opacity mdui-m-b-2">
                                    ⚠ 历史记录在您关闭或刷新窗口后消失。若感到页面卡顿，请刷新本页面。
                                </div>
                                <div class="mdui-panel mdui-table-hoverable"  mdui-panel>
                                    <div class="mdui-panel-item">
                                        <div class="mdui-panel-item-header">
                                            <div class="mdui-panel-item-title">点击以展开历史记录</div>
                                            <i class="mdui-panel-item-arrow mdui-icon material-icons">keyboard_arrow_down</i>
                                        </div>
                                        <div class="mdui-panel-item-body">
                                            <div class="mdui-table-fluid">
                                                <table class="mdui-table" style="word-break:break-all;">
                                                  <thead>
                                                    <tr>
                                                      <th>时间</th>
                                                      <th>内容</th>
                                                    </tr>
                                                  </thead>
                                                  <tbody id="history_tbody">
                                                  </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> 
                </div>
            </div>

        </div>
    </div>
</main>

<footer>
    <div class="mdui-color-grey-900 mdui-hidden-sm-down">
        <div class="mdui-container mdui-typo">
            <div class="mdui-row">
                <div class="mdui-col-md-5 mdui-col-sm-12">
                    <h4>Ding Anonymous 匿名钉<br /><small>钉钉可用的匿名机器人服务</small></h4>
                    <p class="mdui-text-color-theme-secondary mdui-typo-caption-opacity">本站需使用如Firefox或Chrome等现代浏览器，方有完整浏览体验。如遇功能缺失，请更换您的浏览器再试。
                    <p class="mdui-text-color-theme-secondary mdui-typo-caption-opacity">本站的代码可于
                        <a class="mdui-text-color-theme-secondary" target="_blank" href="https://policies.google.cn/privacy">Github</a> 查看。
                    </p>
                </div>
            </div>
        </div>
        <div style="background:rgba(0, 0, 0, 0.15);" class="mdui-p-y-1 mdui-typo">
            <div class="mdui-container">
                <span class="mdui-text-color-theme-secondary">© <?php echo date("Y"); ?> By <a class="mdui-text-color-theme-secondary" href="https://github.com/foldblade">FoldBlade</a></span>
                <span class="mdui-text-color-theme-secondary mdui-float-right">Open Source on
                <a class="mdui-text-color-theme-secondary" target="_blank" href="https://github.com/foldblade">Github</a>
                </span>
            </div>
        </div>
    </div>
    <script>
        function getCookie(cname) {
            let name = cname + "=";
            let ca = document.cookie.split(';');
            for(let i=0; i<ca.length; i++) {
                let c = ca[i].trim();
                if (c.indexOf(name)==0) return decodeURI(c.substring(name.length, c.length));
            }
            return "";
        }
        function setCookie(name, value, Days) {
            let exp = new Date();
            exp.setTime(exp.getTime() + Days*24*60*60*1000);
            document.cookie = name + "="+ encodeURI(value) + ";expires=" + exp.toGMTString();
        }
        function sendMessage() {
            document.getElementById("send").disabled=true;
            document.getElementById("loading").classList.remove("mdui-hidden");
            
            let username = document.getElementById("username").value;
            setCookie('DingAnonymous_Username', username, 1);
            let message = document.getElementById("message").value;
            let httpRequest = new XMLHttpRequest();
            httpRequest.open('POST', "receiver.php", true);
            httpRequest.setRequestHeader("Content-type","application/x-www-form-urlencoded");
            httpRequest.send('username=' + username + '&message=' + message);
            httpRequest.onreadystatechange = function () {
                if (httpRequest.readyState == 4 && httpRequest.status == 200) {
                    let json = JSON.parse(httpRequest.responseText);
                    if (json["errcode"] == 0 && json["errmsg"] == "ok") {
                        let time = new Date();
                        let timeData = time.toLocaleTimeString();
                        console.log(timeData + "\t"+ message);
                        let history_tbody = document.getElementById("history_tbody");
                        let line = document.createElement("tr");
                        let timeTd = document.createElement("td");
                        let nodeTimeData = document.createTextNode(timeData);
                        timeTd.appendChild(nodeTimeData);
                        let messageTd = document.createElement("td");
                        let nodeMessage = document.createTextNode(message);
                        messageTd.appendChild(nodeMessage);
                        line.appendChild(timeTd);
                        line.appendChild(messageTd);
                        history_tbody.appendChild(line);
                        mdui.mutation();
                        document.getElementById("message").value = "";
                    } else {
                        mdui.alert(json["errmsg"], '错误');
                    }
                }
            };
            document.getElementById("loading").classList.add("mdui-hidden");
            document.getElementById("send").disabled=false;
        }
        window.onload = function() {
            $username = getCookie('DingAnonymous_Username');
            if($username != "") {
                document.getElementById('username').value = $username;
                mdui.updateTextFields();
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/mdui@0.4.3/dist/js/mdui.min.js"></script>
</footer>
</body>
</html>
