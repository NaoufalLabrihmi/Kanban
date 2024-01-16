<?php
require_once 'inc/header.php';
require_once 'App.php'; // Adjust this include based on your project structure


require_once 'Classes/Session.php';

// Initialize the $session object
$session = new Session();

// Check if the developer is not logged in
if (!$session->hasGet('user_id')) {
    header("Location: login.php");
    exit();
}

// Get user ID from the session
$user_id = $session->get('user_id');

// Check if the task ID is provided in the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    // Redirect to index.php or handle the situation where the ID is not provided
    header("Location: index.php");
    exit();
}

// Get the task ID from the URL
$task_id = $_GET['id'];

// Fetch the task details from the database
$taskQuery = $conn->prepare("
    SELECT todo.*, priorities.name AS priority_name, developers.name AS created_by_name
    FROM todo
    LEFT JOIN priorities ON todo.priority_id = priorities.id
    LEFT JOIN developers ON todo.created_by = developers.id
    WHERE todo.id = :task_id
");
$taskQuery->bindParam(':task_id', $task_id, PDO::PARAM_INT);
$taskQuery->execute();
$task = $taskQuery->fetch(PDO::FETCH_ASSOC);

// Check if the task with the provided ID exists
if (!$task) {
    // Redirect to index.php or handle the situation where the task is not found
    header("Location: index.php");
    exit();
}

// Fetch additional details for the task, such as developers and tags
$developerStmt = $conn->prepare("SELECT developers.name FROM developers INNER JOIN todo_developers ON developers.id = todo_developers.developer_id WHERE todo_developers.todo_id = :todo_id");
$developerStmt->bindParam(':todo_id', $task['id'], PDO::PARAM_INT);
$developerStmt->execute();
$developerNames = $developerStmt->fetchAll(PDO::FETCH_COLUMN);

$tagsQuery = $conn->prepare("SELECT tags.name FROM tags INNER JOIN todo_tags ON tags.id = todo_tags.tag_id WHERE todo_tags.todo_id = :todo_id");
$tagsQuery->bindParam(':todo_id', $task['id'], PDO::PARAM_INT);
$tagsQuery->execute();
$tags = $tagsQuery->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Details</title>
    <!-- Add your CSS links or stylesheets here -->
    <link rel="stylesheet" href="your-styles.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
</head>
<body>
    <!-- Add your HTML content here for displaying task details -->
    <div class="container my-3">
        <h2>Task Details</h2>
        <div class="alert alert-info p-2">
            <h4><strong>Task name: </strong><?= htmlspecialchars($task['title']) ?></h4>
            <p><h5><strong>Description:</strong> <?= htmlspecialchars($task['description']) ?></h5></p>
            <h5>Task assigned to: <?= implode(', ', $developerNames) ?></h5>
            <h5>Priority: <?= htmlspecialchars($task['priority_name']) ?></h5>
            <h5>Created by: <?= htmlspecialchars($task['created_by_name']) ?></h5>
            <h5>Created at: <?= htmlspecialchars($task['created_at']) ?></h5>
            <!-- Add tags similar to the "All Task" section -->
            <?php if (!empty($tags)) : ?>
                <div class="mb-2">
                    <strong>Tags: </strong>
                    <?php foreach ($tags as $tag) : ?>
                        <span class="badge bg-info"><?= htmlspecialchars($tag['name']) ?></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <!-- Add other details as needed -->
            <div class="mt-3">
                <a href="index.php" class="btn btn-info">Back to All Tasks</a>
            </div>
        </div>
<!-- Comment Section -->
<div class="mt-4">
    <h4><strong>Comments</strong></h4>

    <!-- Add new comment form -->
    <form method="post" action="add_comment.php">
        <div class="mb-3">
            <label for="commentText" class="form-label">Add a Comment:</label>
            <textarea class="form-control" id="commentText" name="comment_text" rows="3" required></textarea>
        </div>
        <input type="hidden" name="task_id" value="<?= $task_id ?>">
        <button type="submit" class="btn btn-primary">Submit Comment</button>
    </form>
    <br>

    <?php
    // Fetch comments for the task
    $commentsQuery = $conn->prepare("SELECT comments.*, developers.name AS commenter_name FROM comments INNER JOIN developers ON comments.posted_by = developers.id WHERE comments.task_id = :task_id");
    $commentsQuery->bindParam(':task_id', $task_id, PDO::PARAM_INT);
    $commentsQuery->execute();
    $comments = $commentsQuery->fetchAll(PDO::FETCH_ASSOC);

    // Display comments
    if (!empty($comments)) {
        foreach ($comments as $comment) {
            ?>
            <div class="card mb-2">
                <div class="card-body">
                    <p class="card-text"><?= htmlspecialchars($comment['comment_text']) ?></p>
                    <p class="card-subtitle text-muted">
                        Posted by: <?= htmlspecialchars($comment['commenter_name']) ?> on <?= htmlspecialchars($comment['created_at']) ?>
                    </p>
                </div>

                <!-- // Display replies for the comment -->
<?php
$repliesQuery = $conn->prepare("SELECT * FROM comment_replies WHERE comment_id = :comment_id");
$repliesQuery->bindParam(':comment_id', $comment['id'], PDO::PARAM_INT);
$repliesQuery->execute();
$replies = $repliesQuery->fetchAll(PDO::FETCH_ASSOC);

// Display replies
if (!empty($replies)) {
    foreach ($replies as $reply) {
        // Fetch the developer name who added the reply
        $replierQuery = $conn->prepare("SELECT name FROM developers WHERE id = :replied_by");
        $replierQuery->bindParam(':replied_by', $reply['replied_by'], PDO::PARAM_INT);
        $replierQuery->execute();
        $replier = $replierQuery->fetch(PDO::FETCH_ASSOC);
        ?>
        <div class="card-footer">
            <div class="card">
                <div class="card-body">
                    <p class="card-text"><?= htmlspecialchars($reply['reply_text']) ?></p>
                    <p class="card-subtitle text-muted">
                        Replied by: <?= htmlspecialchars($replier['name']) ?> on <?= htmlspecialchars($reply['created_at']) ?>
                    </p>
                </div>
            </div>
        </div>
        <?php
    }
}
?>


                <!-- Add a reply form -->
                <div class="card-footer">
                    <form method="post" action="add_reply.php">
                        <div class="mb-3">
                            <label for="replyText" class="form-label">Reply to Comment:</label>
                            <textarea class="form-control" id="replyText" name="reply_text" rows="2" required></textarea>
                        </div>
                        <input type="hidden" name="comment_id" value="<?= $comment['id'] ?>">
                        <input type="hidden" name="task_id" value="<?= $task_id ?>">
                        <button type="submit" class="btn btn-primary btn-sm">Submit Reply</button>
                    </form>
                </div>
            </div>
            <?php
        }
    } else {
        echo "<p>No comments yet.</p>";
    }
    ?>
</div>
    </div>
    
</body>
</html>
