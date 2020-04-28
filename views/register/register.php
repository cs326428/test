<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>注册</title>
    <script src="https://cdn.bootcss.com/jquery/3.4.1/jquery.min.js"></script>
</head>
<body>
    <form action="register" method="post">
        <div>
            用户名：<input type="text" name="username" value=""><br>
            密 码：<input type="password" name="password" value=""><br>
            重复密码：<input type="password" name="repassword" value=""><br>
            Email：<input type="text" name="email" value=""><br><br>
            验证码：<input type="text" name="verifyCode"><img id="verifyImg" src="<?php echo \yii\helpers\Url::toRoute('register/captcha'); ?>"><br>

            <span style="color: red;"><?php echo Yii::$app->session->getFlash('errorMsg'); ?></span>
            <input name="_csrf" type="hidden" id="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
        </div>
        <div>
            <input type="submit" value="注册">
        </div>
    </form>

    <script type="text/javascript">
        $(function () {
            //处理点击刷新验证码
            $("#verifyImg").on("click", function () {
                $.get("<?php echo \yii\helpers\Url::toRoute('register/captcha') ?>?refresh", function (data) {
                    $("#verifyImg").attr("src", data["url"]);
                }, "json");
            });
        });
    </script>
</body>
</html>