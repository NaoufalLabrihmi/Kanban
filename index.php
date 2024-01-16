    <?php
    require_once 'inc/header.php';
    require_once 'App.php';
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
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
         <!-- Bootstrap CSS CDN -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script>
    $(document).ready(function () {
        // Function to fetch and update tasks
        function fetchTasks() {
            $.ajax({
                type: "GET",
                url: "fetchTasks.php", // Corrected URL
                dataType: "json",
                success: function (response) {
                    // Update the task sections with the latest data
                    $(".all-task").html(response.todoHtml);
                    $(".doing-task").html(response.doingHtml);
                    $(".done-task").html(response.doneHtml);
                },
                error: function (error) {
                    console.error("Failed to fetch tasks:", error);
                    // Handle errors if needed
                }
            });
        }

        // Initial fetch of tasks
        fetchTasks();

        // Periodically fetch tasks every 5 seconds
        setInterval(fetchTasks, 5000);

        // Form submission AJAX code
        $("form").submit(function (event) {
            event.preventDefault();

            var formData = $(this).serialize();

                $.ajax({
                    type: "POST",
                    url: "handle/addToDo.php",
                    data: formData,
                    dataType: "json",
                    success: function (response) {
                        if (response.success) {
                            var newTask = response.newTask;

                            // Fetch developer names
                            var developerNames = newTask.developer_names.join(', ');

                            // Append the new task to the appropriate section
                            var taskHtml = `
                                <div class="alert alert-info p-2">
                                    <h4>${newTask.title}</h4>
                                    <h5>Task assigned to: ${developerNames}</h5>
                                    <h5>Priority: ${newTask.priority_name}</h5>
                                    ${newTask.tags.length > 0 ? `<h5>Tags: ${newTask.tags.join(', ')}</h5>` : ''}
                                    <h5>Created by: ${newTask.created_by}</h5> <!-- Updated line -->
                                    <h5>Created at: ${newTask.created_at}</h5>
                                    <div class="d-flex justify-content-between mt-3">
                                        <a href="handle/goto.php?name=doing&id=${newTask.id}" class="btn btn-info p-1 text-white">doing</a>
                                    </div>
                                </div>
                            `;

                            // Determine the section based on the task status
                            if (newTask.status === 'todo') {
                                $(".all-task").prepend(taskHtml);
                            } else if (newTask.status === 'doing') {
                                $(".doing-task").prepend(taskHtml);
                            } else if (newTask.status === 'done') {
                                $(".done-task").prepend(taskHtml);
                            }

                            // Clear the form
                            $("form")[0].reset();

                            // Reinitialize Bootstrap components
                            $('[data-toggle="tooltip"]').tooltip();
                        } else {
                            console.error(response.error);
                            // Handle errors and display messages
                        }
                    },

                    error: function (error) {
                        console.error("AJAX request failed:", error);
                        // Handle AJAX errors
                    }
                });
            });
        });
    </script>

    </head>
    <body style="background-image: url(imgs/wp9349630-blur-pc-4k-wallpapers.jpg); background-size: cover; background-repeat: no-repeat; ">
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

        <div class="container my-3 ">

            <div class="container">
                <?php
                require_once 'inc/errors.php';
                require_once 'inc/success.php';
                ?>

            </div>

            <div class="row d-flex justify-content-center">
            

    <div class="container mb-5 d-flex justify-content-center">
        <div class="col-md-4">
            <form action="handle/addToDo.php" method="post">
                <input type="text" class="form-control" rows="3" name="title" id="" placeholder="Task name">
                <br>
                  <!-- Add a textarea for task description -->
                  <textarea class="form-control" rows="5" name="description" placeholder="Task description"></textarea>

                <!-- Add a select dropdown for developers with multiple selection -->
                <div class="mb-3">
                    <label for="developer" class="form-label">Assign to Developer(s):</label>
                    <select class="form-select" name="developer_ids[]" id="developer" multiple>
                        <!-- Select multiple developers -->
                        <?php
                        $developersQuery = $conn->query("SELECT * FROM developers");
                        while ($developer = $developersQuery->fetch(PDO::FETCH_ASSOC)) {
                            echo "<option value='{$developer['id']}'>{$developer['name']}</option>";
                        }
                        ?>
                    </select>
                </div>
                            <!-- Add a select dropdown for priorities -->
                            <div class="mb-3">
                                <label for="priority" class="form-label">Select Priority:</label>
                                <select class="form-select" name="priority_id" id="priority">
                                    <option value="">Select Priority</option>
                                    <?php
                                    // Fetch priorities from the database
                                    $prioritiesQuery = $conn->query("SELECT * FROM priorities");
                                    if (!$prioritiesQuery) {
                                        echo "Error: " . $conn->error;
                                    } else {
                                        while ($priority = $prioritiesQuery->fetch(PDO::FETCH_ASSOC)) {
                                            echo "<option value='{$priority['id']}'>{$priority['name']}</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>

                            <!-- Add a select dropdown for tags -->
                            <div class="mb-3">
                                <label class="form-label">Select Tags:</label>
                                <br>
                                
                                <?php
                                // Fetch tags from the database
                                $tagsQuery = $conn->query("SELECT * FROM tags");
                                if (!$tagsQuery) {
                                    echo "Error: " . $conn->error;
                                } else {
                                    while ($tag = $tagsQuery->fetch(PDO::FETCH_ASSOC)) {
                                        echo '<div class="form-check form-check-inline">';
                                        echo '<input class="form-check-input" type="checkbox" name="tags[]" id="tag' . $tag['id'] . '" value="' . $tag['id'] . '">';
                                        echo '<label class="form-check-label btn btn-secondary" for="tag' . $tag['id'] . '">' . $tag['name'] . '</label>';
                                        echo '</div>';
                                    }
                                }
                                ?>
                            </div>


                            <div class="text-center">
                                <button type="submit" name="submit" class="form-control text-white bg-info mt-3 ">Add Task</button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>

            <div class="row d-flex justify-content-between">
                <!-- all -->
            <!-- All Task Section -->
            <div class="col-md-3">
    <h4 class="text-white">All Task</h4>
    <div class="m-2 py-3 show-to-do all-task">
        <?php
        $stm = $conn->query("
            SELECT todo.*, priorities.name AS priority_name, developers.name AS created_by_name
            FROM todo
            LEFT JOIN priorities ON todo.priority_id = priorities.id
            LEFT JOIN developers ON todo.created_by = developers.id
            WHERE todo.`status`='todo'
            ORDER BY todo.id DESC
        ");
        ?>
        <?php if ($stm->rowCount() < 1) : ?>
            <div class="item">
                <div class="alert-info text-center">
                    Empty to do task
                </div>
            </div>
        <?php else : ?>
            <?php while ($todo = $stm->fetch(PDO::FETCH_ASSOC)) : ?>
                <div class="alert alert-info p-2">
                    <h4><?php echo $todo['title']; ?></h4>
                    <?php
                    // Fetch the developer names for the task
                    $developerStmt = $conn->prepare("SELECT developers.name FROM developers INNER JOIN todo_developers ON developers.id = todo_developers.developer_id WHERE todo_developers.todo_id = :todo_id");
                    $developerStmt->bindParam(':todo_id', $todo['id'], PDO::PARAM_INT);

                    if ($developerStmt->execute()) {
                        // Fetch the result
                        $developerNames = $developerStmt->fetchAll(PDO::FETCH_COLUMN);

                        // Check if the developer names were found
                        if ($developerNames !== false) {
                            // Set the developer names in the $todo array
                            $todo['developer_names'] = $developerNames;
                        } else {
                            // If developer names are not found, set a default value or handle accordingly
                            $todo['developer_names'] = ['Unknown Developer'];
                        }
                    } else {
                        // If there was an error executing the query, set a default value or handle accordingly
                        $todo['developer_names'] = ['Unknown Developer'];
                    }

                    // Fetch the created by name
                    $createdByStmt = $conn->prepare("SELECT name FROM developers WHERE id = :created_by");
                    $createdByStmt->bindParam(':created_by', $todo['created_by'], PDO::PARAM_INT);
                    $createdByStmt->execute();
                    $createdByName = $createdByStmt->fetch(PDO::FETCH_COLUMN);
                    ?>
                    <h5>Task assigned to: <?php echo implode(', ', $todo['developer_names']); ?></h5>
                    <h5>Priority: <?php echo $todo['priority_name']; ?></h5>
                    <h5>Created by: <?php echo $createdByName; ?></h5>
                    <h5>Created at: <?php echo $todo['created_at']; ?></h5>
                    <!-- Add tags similar to the "Doing" and "Done" sections -->
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
                    <div class="d-flex justify-content-between mt-3">
                        <a href="task_details.php?id=<?php echo $todo['id'] ?>" class="btn btn-info p-1 text-white">Details</a>
                        <a href="handle/goto.php?name=doing&id=<?php echo $todo['id'] ?>&referrer=index.php" class="btn btn-info p-1 text-white">Doing</a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
</div>


    <!-- Doing Section -->
    <div class="col-md-3">
        <h4 class="text-white">Doing</h4>
        <div class="m-2 py-3 show-to-do doing-task">
            <?php
            $stm = $conn->query("
                SELECT todo.*, priorities.name AS priority_name, developers.name AS created_by_name
                FROM todo
                LEFT JOIN priorities ON todo.priority_id = priorities.id
                LEFT JOIN developers ON todo.created_by = developers.id
                WHERE `status`='doing'
                ORDER BY todo.id DESC
            ");
            ?>
            <?php if ($stm->rowCount() < 1) : ?>
                <div class="item">
                    <div class="alert-success text-center ">
                        No tasks in progress
                    </div>
                </div>
            <?php endif; ?>
            <?php while ($todo = $stm->fetch(PDO::FETCH_ASSOC)) : ?>
                <div class="alert alert-success p-2">
                    <h4><?php echo $todo['title']; ?></h4>
                    <?php
                    // Fetch the developer names for the task
                    $developerStmt = $conn->prepare("SELECT developers.name FROM developers INNER JOIN todo_developers ON developers.id = todo_developers.developer_id WHERE todo_developers.todo_id = :todo_id");
                    $developerStmt->bindParam(':todo_id', $todo['id'], PDO::PARAM_INT);

                    if ($developerStmt->execute()) {
                        // Fetch the result
                        $developerNames = $developerStmt->fetchAll(PDO::FETCH_COLUMN);

                        // Check if the developer names were found
                        if ($developerNames !== false) {
                            // Set the developer names in the $todo array
                            $todo['developer_names'] = $developerNames;
                        } else {
                            // If developer names are not found, set a default value or handle accordingly
                            $todo['developer_names'] = ['Unknown Developer'];
                        }
                    } else {
                        // If there was an error executing the query, set a default value or handle accordingly
                        $todo['developer_names'] = ['Unknown Developer'];
                    }
                    ?>
                    <h5>Task assigned to: <?php echo implode(', ', $todo['developer_names']); ?></h5>
                    <h5>Priority: <?php echo $todo['priority_name']; ?></h5>
                    <h5>Created by: <?php echo $todo['created_by_name']; ?></h5>
                    <h5>Created at: <?php echo $todo['created_at']; ?></h5>
                    <!-- Add tags similar to the "All Task" section -->
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
                    <div class="d-flex justify-content-between mt-3">
                        <!-- Add your buttons or actions for the "Doing" section here -->
                        <a href="handle/goto.php?name=done&id=<?php echo $todo['id'] ?>&referrer=index.php" class="btn btn-info p-1 text-white">Done</a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
    <!-- Done Section -->
    <div class="col-md-3">
        <h4 class="text-white">Done</h4>
        <div class="m-2 py-3 show-to-do done-task">
            <?php
            $stm = $conn->query("
                SELECT todo.*, priorities.name AS priority_name, developers.name AS created_by_name
                FROM todo
                LEFT JOIN priorities ON todo.priority_id = priorities.id
                LEFT JOIN developers ON todo.created_by = developers.id
                WHERE `status`='done'
                ORDER BY todo.id DESC
            ");
            ?>
            <?php if ($stm->rowCount() < 1) : ?>
                <div class="item">
                    <div class="alert-warning text-center">
                        No tasks completed
                    </div>
                </div>
            <?php endif; ?>
            <?php while ($todo = $stm->fetch(PDO::FETCH_ASSOC)) : ?>
                <div class="alert alert-warning p-2">
                    <a href="handle/delete.php?id=<?php echo $todo['id'] ?>" onclick="confirm('are you sure')" class="remove-to-do text-dark d-flex justify-content-end "><i class="fa fa-close" style="font-size:16px;"></i></a>
                    <h4><?php echo $todo['title']; ?></h4>
                    <?php
                    // Fetch the developer names for the task
                    $developerStmt = $conn->prepare("SELECT developers.name FROM developers INNER JOIN todo_developers ON developers.id = todo_developers.developer_id WHERE todo_developers.todo_id = :todo_id");
                    $developerStmt->bindParam(':todo_id', $todo['id'], PDO::PARAM_INT);

                    if ($developerStmt->execute()) {
                        // Fetch the result
                        $developerNames = $developerStmt->fetchAll(PDO::FETCH_COLUMN);

                        // Check if the developer names were found
                        if ($developerNames !== false) {
                            // Set the developer names in the $todo array
                            $todo['developer_names'] = $developerNames;
                        } else {
                            // If developer names are not found, set a default value or handle accordingly
                            $todo['developer_names'] = ['Unknown Developer'];
                        }
                    } else {
                        // If there was an error executing the query, set a default value or handle accordingly
                        $todo['developer_names'] = ['Unknown Developer'];
                    }
                    ?>
                    <h5>Task assigned to: <?php echo implode(', ', $todo['developer_names']); ?></h5>
                    <h5>Priority: <?php echo $todo['priority_name']; ?></h5>
                    <h5>Created by: <?php echo $todo['created_by_name']; ?></h5>
                    <h5>Created at: <?php echo $todo['created_at']; ?></h5>
                    <!-- Add tags similar to the "All Task" section -->
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
                    <!-- Add your buttons or actions for the "Done" section here -->
                    <a href="handle/goto.php?name=doing&id=<?php echo $todo['id'] ?>&referrer=index.php" class="btn btn-info p-1 text-white">Move to Doing</a>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

<!-- Bootstrap JS CDN -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>





    </body>

    </html>
