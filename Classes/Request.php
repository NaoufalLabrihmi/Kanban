<?php

//namespace Classes ;

class Request {
    public function get( string $key=null)
    {
     //return   ( !empty($_GET[$key])) ? (isset($_GET[$key]) ?$_GET[$key] : null ) : null  ;
     return   ( !empty($key)) ? (isset($_GET[$key]) ?$_GET[$key] : null ) : null  ;
     //return   ( $key != null) ? (isset($_GET[$key]) ?$_GET[$key] : null ) : null  ;
    }

    public function post( string $key=null)
    {
     //return   ( !empty($_POST[$key])) ? (isset($_POST[$key]) ?$_POST[$key] : null ) : null  ;
     return   ( !empty($key)) ? (isset($_POST[$key]) ? $_POST[$key] : null ) : null  ;
     //return   ( $key != null) ? (isset($_POST[$key]) ?$_POST[$key] : null ) : null  ;
    }

    public function hasGet($key)
    {
        return isset($_GET[$key]) ;

    }

    public function hasPost($key)
    {
        return isset($_POST[$key]) ;

    }

    public function clean($key)
    {
        return trim(htmlspecialchars($_POST[$key]))  ;

    }
    public function header($link){
        return header("location:$link") ;
    }


}