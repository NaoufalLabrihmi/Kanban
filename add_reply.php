<?php
require_once 'App.php'; // Adjust this include based on your project structure

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the comment ID, reply text, and task ID from the form
    $commentId = $_POST['comment_id'];
    $replyText = $_POST['reply_text'];
    $taskId = $_POST['task_id'];

    // Get the user ID from the session
    $userId = $session->get('user_id');

    // Prepare the SQL statement to insert a reply
    $insertReply = $conn->prepare("INSERT INTO comment_replies (comment_id, reply_text, replied_by) VALUES (:comment_id, :reply_text, :replied_by)");

    // Bind parameters
    $insertReply->bindParam(':comment_id', $commentId, PDO::PARAM_INT);
    $insertReply->bindParam(':reply_text', $replyText, PDO::PARAM_STR);
    $insertReply->bindParam(':replied_by', $userId, PDO::PARAM_INT);

    // Execute the statement
    $insertReply->execute();

    // Redirect back to the task details page after adding the reply
    header("Location: task_details.php?id=" . $taskId);
    exit();
} else {
    // Redirect to the home page or handle the situation when the form is not submitted
    header("Location: index.php");
    exit();
}
?>
