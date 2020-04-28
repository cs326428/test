<?php


namespace app\common;


abstract class Operation
{
    //抽象方法不能包含函数体 子类必须实现该功能函数
    abstract public function getValue($num1,$num2);
}