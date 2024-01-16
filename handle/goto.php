<?php

require_once '../App.php';

if ($request->hasGet("id") && $request->hasGet("name") && $request->hasGet("referrer")) {
    $id = $request->get("id");
    $name = $request->get("name");
    $referrer = $request->get("referrer");

    $stm = $conn->prepare("SELECT * FROM todo WHERE `id` = :id");
    $stm->bindParam(":id", $id, PDO::PARAM_INT);
    $out = $stm->execute();

    if ($out) {
        switch ($name) {
            case 'doing':
            case 'done':
                $stm = $conn->prepare("UPDATE todo SET `status` = :status WHERE id = :id");
                $stm->bindParam(":id", $id, PDO::PARAM_INT);
                $stm->bindParam(":status", $name, PDO::PARAM_STR);
                $output = $stm->execute();
                break;
            case 'move-to-doing':
                // Additional logic for moving to "Doing" from another section
                $stmMoveToDoing = $conn->prepare("UPDATE todo SET `status` = 'doing' WHERE id = :id");
                $stmMoveToDoing->bindParam(":id", $id, PDO::PARAM_INT);
                $output = $stmMoveToDoing->execute();
                break;
            default:
                $output = false;
        }

        if ($output) {
            // Redirect back to the referring page
            $request->header("../" . $referrer);
        } else {
            $request->header("../" . $referrer);
        }
    } else {
        $request->header("../" . $referrer);
    }
} else {
    $request->header("../" . $referrer);
}






