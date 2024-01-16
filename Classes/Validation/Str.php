<?php

//namespace Validation ;



require_once 'Validator.php';




class Str implements Validator {

    public function check($key , $value)
    {
        if(is_numeric($value)){
            return "$key Must Be String";
        }else{
            return false ;
        }
    }
}