<?php

// Include the database connection logic
include 'connection.php';

// Function to get comments for a specific task ID
function getCommentsFromDatabase($taskId) {
    global $conn;

    try {
        // Use prepared statements to prevent SQL injection
        $query = $conn->prepare("SELECT * FROM `comments` WHERE `task_id` = :taskId");
        $query->bindParam(':taskId', $taskId, PDO::PARAM_INT);
        $query->execute();

        // Fetch comments
        $comments = $query->fetchAll(PDO::FETCH_ASSOC);

        return $comments;
    } catch (PDOException $ex) {
        // Handle database errors
        return false;
    }
}

// Function to save a comment to the database
function saveCommentToDatabase($taskId, $commentText, $postedBy) {
    global $conn;

    try {
        // Use prepared statements to prevent SQL injection
        $query = $conn->prepare("INSERT INTO `comments` (`task_id`, `comment_text`, `posted_by`) VALUES (:taskId, :commentText, :postedBy)");
        $query->bindParam(':taskId', $taskId, PDO::PARAM_INT);
        $query->bindParam(':commentText', $commentText, PDO::PARAM_STR);
        $query->bindParam(':postedBy', $postedBy, PDO::PARAM_INT);
        $query->execute();

        // Get the inserted comment ID
        $commentId = $conn->lastInsertId();

        return $commentId;
    } catch (PDOException $ex) {
        // Handle database errors
        return false;
    }
}
?>


?>
