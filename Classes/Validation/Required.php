<?php

//namespace Validation ;



require_once 'Validator.php';



class Required implements Validator {

    public function check($key , $value)
    {
        if(empty($value)){
            return "$key is Required";
        }else{
            return false ;
        }
    }
}