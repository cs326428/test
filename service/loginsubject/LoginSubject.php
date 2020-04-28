<?php

namespace app\service\loginsubject;

class LoginSubject implements LoginSubjectInterface {
    private $_observers = [];

    public function register(LoginObserver $observer)
    {
        $this->_observers[] = $observer;
    }

    public function notify()
    {
        foreach ($this->_observers as $observer) {
            $observer->trigger();
        }
    }
}