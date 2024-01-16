<?php
require_once 'App.php'; // Adjust this include based on your project structure

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the comment text and task ID from the form
    $commentText = $_POST['comment_text'];
    $taskId = $_POST['task_id'];

    // Get the user ID from the session
    $userId = $session->get('user_id');

    // Prepare the SQL statement to insert a comment
    $insertComment = $conn->prepare("INSERT INTO comments (task_id, comment_text, posted_by) VALUES (:task_id, :comment_text, :posted_by)");

    // Bind parameters
    $insertComment->bindParam(':task_id', $taskId, PDO::PARAM_INT);
    $insertComment->bindParam(':comment_text', $commentText, PDO::PARAM_STR);
    $insertComment->bindParam(':posted_by', $userId, PDO::PARAM_INT);

    // Execute the statement
    $insertComment->execute();

    // Redirect back to the task details page after adding the comment
    header("Location: task_details.php?id=" . $taskId);
    exit();
} else {
    // Redirect to the home page or handle the situation when the form is not submitted
    header("Location: index.php");
    exit();
}
?>
