<?php

namespace app\service\loginsubject;


class LoginLog implements LoginObserver
{
    public function trigger()
    {
        echo '系统已经记录了你这次的登录信息';
    }
}