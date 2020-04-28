<?php


namespace app\common;


class OperationFactory
{
    public static function createObj($operate){
        switch ($operate){
            case '+':
                return new OperationAdd();
                break;
            case '-':
                return new OperationSub();
                break;
        }
    }
}