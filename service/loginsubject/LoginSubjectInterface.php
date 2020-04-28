<?php

namespace app\service\loginsubject;

interface LoginSubjectInterface
{
    public function register(LoginObserver $observer);
    public function notify();
}

interface LoginObserver{
    public function trigger();
}