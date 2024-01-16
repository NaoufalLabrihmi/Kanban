<?php

if($session->hasGet("errors")){
    foreach($session->get("errors") as $error){ ?>

    <div class="alert alert-danger"><?php echo $error    ?></div>

 <?php    }
  $session->unset("errors");
}



if($session->hasGet("error")){?>


    <div class="alert alert-danger"><?php echo $session->get("error")   ?></div>
    
    <?php }
    
    $session->unset("error");




    ?>