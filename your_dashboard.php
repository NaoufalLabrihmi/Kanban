<?php
// Include necessary files and start the session
require_once 'inc/header.php';
require_once 'App.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    // Redirect to the login page if not logged in
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Function to fetch developer names for a task
function getDeveloperNames($conn, $todo_id) {
    $developerStmt = $conn->prepare("SELECT developers.name FROM developers INNER JOIN todo_developers ON developers.id = todo_developers.developer_id WHERE todo_developers.todo_id = :todo_id");
    $developerStmt->bindParam(':todo_id', $todo_id, PDO::PARAM_INT);

    if ($developerStmt->execute()) {
        // Fetch the result
        $developerNames = $developerStmt->fetchAll(PDO::FETCH_COLUMN);

        // Check if the developer names were found
        if ($developerNames !== false) {
            return $developerNames;
        }
    }

    // If developer names are not found or there's an error, return a default value
    return ['Unknown Developer'];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        body {
            background-image: url("imgs/wp7969113-blurry-ultra-hd-wallpapers.jpg");
            background-size: cover;
            background-repeat: no-repeat;
        }

        .task-container {
            margin-top: 20px;
        }

        .task-card {
            margin-bottom: 20px;
        }
    </style>

</head>

<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="index.php">Home</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item active">
                <a class="nav-link" href="dashboard.php">Created by me</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="your_dashboard.php">Assigned to me</a>
            </li>
        </ul>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="logout.php">Logout</a>
            </li>
        </ul>
    </div>
</nav>
<div class="container mt-4">
    <div class="row">

        <!-- Tasks Assigned to the Logged-In Developer -->
        <div class="col-md-4">
    <div class="card task-container">
        <div class="card-header bg-info text-white">
            <h4 class="mb-0">Tasks Assigned to You</h4>
        </div>
        <div class="card-body">
            <?php
            // Query to fetch tasks assigned to the logged-in developer that are not completed, done, or in progress
            $stmAssignedToUser = $conn->prepare("
                SELECT todo.*, priorities.name AS priority_name, developers.name AS created_by_name
                FROM todo
                LEFT JOIN priorities ON todo.priority_id = priorities.id
                LEFT JOIN developers ON todo.created_by = developers.id
                INNER JOIN todo_developers ON todo.id = todo_developers.todo_id
                WHERE todo_developers.developer_id = :user_id
                  AND todo.`status` NOT IN ('completed', 'done', 'doing')  -- Exclude tasks that are completed, done, or in progress
                ORDER BY todo.id DESC
            ");
            $stmAssignedToUser->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmAssignedToUser->execute();
            ?>

            <?php if ($stmAssignedToUser->rowCount() < 1) : ?>
                <div class="item">
                    <div class="alert-info text-center">
                        No tasks assigned to you
                    </div>
                </div>
            <?php else : ?>
                <?php while ($todo = $stmAssignedToUser->fetch(PDO::FETCH_ASSOC)) : ?>
                    <div class="alert alert-info p-2 task-card">
                        <!-- Display task information as needed -->
                        <h4><?php echo $todo['title']; ?></h4>
                        <h5>Task assigned to: <?php echo implode(', ', getDeveloperNames($conn, $todo['id'])); ?></h5>
                        <h5>Priority: <?php echo $todo['priority_name']; ?></h5>
                        <h5>Created by: <?php echo $todo['created_by_name']; ?></h5>
                        <h5>Created at: <?php echo $todo['created_at']; ?></h5>
                        <!-- Add tags similar to the other sections -->
                        <?php
                        $tagsQuery = $conn->prepare("SELECT tags.name FROM tags INNER JOIN todo_tags ON tags.id = todo_tags.tag_id WHERE todo_tags.todo_id = :todo_id");
                        $tagsQuery->bindParam(':todo_id', $todo['id'], PDO::PARAM_INT);
                        $tagsQuery->execute();
                        $tags = $tagsQuery->fetchAll(PDO::FETCH_ASSOC);
                        ?>
                        <?php if (!empty($tags)) : ?>
                            <div class="mb-2">
                                <strong>Tags: </strong>
                                <?php foreach ($tags as $tag) : ?>
                                    <span class="badge bg-info"><?php echo $tag['name']; ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        <!-- Add your buttons or actions specific to this section -->
                        <div class="d-flex justify-content-between mt-3">
                        <a href="task_details.php?id=<?php echo $todo['id'] ?>" class="btn btn-info p-1 text-white">Details</a>
                        <a href="handle/goto.php?name=doing&id=<?php echo $todo['id'] ?>&referrer=your_dashboard.php" class="btn btn-info p-1 text-white">Doing</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </div>
</div>



        <!-- Tasks In Progress -->
<div class="col-md-4">
    <div class="card task-container">
        <div class="card-header bg-warning text-white">
            <h4 class="mb-0">Tasks In Progress</h4>
        </div>
        <div class="card-body">
            <?php
            // Query to fetch tasks in progress assigned to the logged-in developer
            $stmInProgress = $conn->prepare("
                SELECT todo.*, priorities.name AS priority_name, developers.name AS created_by_name
                FROM todo
                LEFT JOIN priorities ON todo.priority_id = priorities.id
                LEFT JOIN developers ON todo.created_by = developers.id
                INNER JOIN todo_developers ON todo.id = todo_developers.todo_id
                WHERE todo.`status`='doing' AND todo_developers.developer_id = :user_id
                ORDER BY todo.id DESC
            ");
            $stmInProgress->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmInProgress->execute();
            ?>

            <?php if ($stmInProgress->rowCount() < 1) : ?>
                <div class="item">
                    <div class="alert-info text-center">
                        No tasks in progress
                    </div>
                </div>
            <?php else : ?>
                <?php while ($todo = $stmInProgress->fetch(PDO::FETCH_ASSOC)) : ?>
                    <div class="alert alert-warning p-2 task-card">
                        <!-- Display task information as needed -->
                        <h4><?php echo $todo['title']; ?></h4>
                        <h5>Task assigned to: <?php echo implode(', ', getDeveloperNames($conn, $todo['id'])); ?></h5>
                        <h5>Priority: <?php echo $todo['priority_name']; ?></h5>
                        <h5>Created by: <?php echo $todo['created_by_name']; ?></h5>
                        <h5>Created at: <?php echo $todo['created_at']; ?></h5>
                        <!-- Add tags similar to other sections -->
                        <?php
                        $tagsQuery = $conn->prepare("SELECT tags.name FROM tags INNER JOIN todo_tags ON tags.id = todo_tags.tag_id WHERE todo_tags.todo_id = :todo_id");
                        $tagsQuery->bindParam(':todo_id', $todo['id'], PDO::PARAM_INT);
                        $tagsQuery->execute();
                        $tags = $tagsQuery->fetchAll(PDO::FETCH_ASSOC);
                        ?>
                        <?php if (!empty($tags)) : ?>
                            <div class="mb-2">
                                <strong>Tags: </strong>
                                <?php foreach ($tags as $tag) : ?>
                                    <span class="badge bg-info"><?php echo $tag['name']; ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        <!-- Add your buttons or actions specific to this section -->
                        <div class="d-flex justify-content-between mt-3">
                        <a href="task_details.php?id=<?php echo $todo['id'] ?>" class="btn btn-info p-1 text-white">Details</a>
                        <a href="handle/goto.php?name=done&id=<?php echo $todo['id'] ?>&referrer=your_dashboard.php" class="btn btn-info p-1 text-white">Done</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </div>
</div>



<!-- Completed Tasks -->
<div class="col-md-4">
    <div class="card task-container">
        <div class="card-header bg-success text-white">
            <h4 class="mb-0">Completed Tasks</h4>
        </div>
        <div class="card-body">
            <?php
            // Query to fetch completed tasks assigned to the logged-in developer
            $stmCompleted = $conn->prepare("
                SELECT todo.*, priorities.name AS priority_name, developers.name AS created_by_name
                FROM todo
                LEFT JOIN priorities ON todo.priority_id = priorities.id
                LEFT JOIN developers ON todo.created_by = developers.id
                INNER JOIN todo_developers ON todo.id = todo_developers.todo_id
                WHERE todo.`status`='done' AND todo_developers.developer_id = :user_id
                ORDER BY todo.id DESC
            ");
            $stmCompleted->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmCompleted->execute();
            ?>

            <?php if ($stmCompleted->rowCount() < 1) : ?>
                <div class="item">
                    <div class="alert-info text-center">
                        No completed tasks
                    </div>
                </div>
            <?php else : ?>
                <?php while ($todo = $stmCompleted->fetch(PDO::FETCH_ASSOC)) : ?>
                    <div class="alert alert-success p-2 task-card">
                        <!-- Display task information as needed -->
                        <h4><?php echo $todo['title']; ?></h4>
                        <h5>Task assigned to: <?php echo implode(', ', getDeveloperNames($conn, $todo['id'])); ?></h5>
                        <h5>Priority: <?php echo $todo['priority_name']; ?></h5>
                        <h5>Created by: <?php echo $todo['created_by_name']; ?></h5>
                        <h5>Created at: <?php echo $todo['created_at']; ?></h5>
                        <!-- Add tags similar to other sections -->
                        <?php
                        $tagsQuery = $conn->prepare("SELECT tags.name FROM tags INNER JOIN todo_tags ON tags.id = todo_tags.tag_id WHERE todo_tags.todo_id = :todo_id");
                        $tagsQuery->bindParam(':todo_id', $todo['id'], PDO::PARAM_INT);
                        $tagsQuery->execute();
                        $tags = $tagsQuery->fetchAll(PDO::FETCH_ASSOC);
                        ?>
                        <?php if (!empty($tags)) : ?>
                            <div class="mb-2">
                                <strong>Tags: </strong>
                                <?php foreach ($tags as $tag) : ?>
                                    <span class="badge bg-info"><?php echo $tag['name']; ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        <!-- Add your buttons or actions specific to this section -->
                        <a href="task_details.php?id=<?php echo $todo['id'] ?>" class="btn btn-info p-1 text-white">Details</a>
                        <a href="handle/goto.php?name=move-to-doing&id=<?php echo $todo['id'] ?>&referrer=your_dashboard.php" class="btn btn-info p-1 text-white">Move to Doing</a>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

        <!-- Add more sections or modify existing sections as needed -->

    </div>
</div>
<!-- Bootstrap JS CDN -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script src="https://code.jquery.com/jquery-3.6.4.slim.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>

</html>
