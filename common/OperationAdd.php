<?php


namespace app\common;


class OperationAdd extends Operation
{
    public function getValue($num1,$num2){
        return $num1+$num2;
    }
}