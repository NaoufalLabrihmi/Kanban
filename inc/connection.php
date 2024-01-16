<?php

try{
    $conn=new PDO("mysql:host=localhost;port=3306;dbname=br","root","");
}catch(Exception $ex){
    echo "DB Error : " . $ex->getMessage();
}


    