<?php

require_once '../App.php';

if($request->hasGet("id")){

    $id=$request->get("id");

    $stm=$conn->prepare("select *  from todo where `id`=:id");
    $stm->bindparam(":id",$id,PDO::PARAM_INT);
    $out = $stm->execute();

    if($out){

    $stm=$conn->prepare("delete from todo where id=:id");
    $stm->bindparam(":id",$id,PDO::PARAM_INT);
    $todo_delete=$stm->execute();
        if($todo_delete){
            $session->set("success","Data Deleted Successfuly");
            $request->header("../index.php");
        }else{
            $session->set("errors","Error Happened While Deleting Data");
            $request->header("../index.php");
        }


    }else{
        $session->set("error","Error Happened While Deleting Data");
        $request->header("../index.php"); 
    }

}else{
    $request->header("../index.php"); 
}