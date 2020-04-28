<?php


namespace app\common;


class OperationSub extends Operation
{
    public function getValue($num1,$num2){
        return $num1-$num2;
    }
}