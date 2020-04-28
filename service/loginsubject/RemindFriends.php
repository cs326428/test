<?php

namespace app\service\loginsubject;

class RemindFriends implements LoginObserver
{
    public function trigger()
    {
        echo '您上线的消息消息提醒了您的在好友';
    }
}