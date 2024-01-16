    <?php

    if($session->hasGet("success")){?>


        <div class="alert alert-success"><?php echo $session->get("success")   ?></div>
        
        <?php }
        
        $session->unset("success");





        ?>