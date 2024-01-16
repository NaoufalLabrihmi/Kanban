    <?php
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

    // Fetch tasks for each section (todo, doing, done)
    $todoHtml = fetchTasksByStatus($conn, $user_id, 'todo');
    $doingHtml = fetchTasksByStatus($conn, $user_id, 'doing');
    $doneHtml = fetchTasksByStatus($conn, $user_id, 'done');

    // Prepare the response
    $response = [
        'todoHtml' => $todoHtml,
        'doingHtml' => $doingHtml,
        'doneHtml' => $doneHtml,
    ];

    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode($response);

    /**
     * Fetch tasks by status for a specific user.
     *
     * @param PDO $conn Database connection
     * @param int $user_id User ID
     * @param string $status Task status ('todo', 'doing', 'done')
     * @return string HTML content for the tasks
     */
    function fetchTasksByStatus($conn, $user_id, $status)
    {
        $stm = $conn->prepare("
        SELECT todo.*, priorities.name AS priority_name, developers.name AS created_by_name
        FROM todo
        LEFT JOIN priorities ON todo.priority_id = priorities.id
        LEFT JOIN developers ON todo.created_by = developers.id
        WHERE todo.`status` = :status
        ORDER BY todo.id DESC
    ");
    $stm->bindParam(':status', $status, PDO::PARAM_STR);
    $stm->execute();
    

        $html = '';

        if ($stm->rowCount() > 0) {
            while ($todo = $stm->fetch(PDO::FETCH_ASSOC)) {
                // Fetch developer names
                $developerStmt = $conn->prepare("SELECT developers.name FROM developers INNER JOIN todo_developers ON developers.id = todo_developers.developer_id WHERE todo_developers.todo_id = :todo_id");
                $developerStmt->bindParam(':todo_id', $todo['id'], PDO::PARAM_INT);

                if ($developerStmt->execute()) {
                    // Fetch the result
                    $developerNames = $developerStmt->fetchAll(PDO::FETCH_COLUMN);

                    // Check if the developer names were found
                    if ($developerNames !== false) {
                        // Set the developer names in the $todo array
                        $todo['developer_names'] = implode(', ', $developerNames);
                    } else {
                        // If developer names are not found, set a default value or handle accordingly
                        $todo['developer_names'] = 'Unknown Developer';
                    }
                } else {
                    // If there was an error executing the query, set a default value or handle accordingly
                    $todo['developer_names'] = 'Unknown Developer';
                }

                // Fetch tags for the task
                $tagsQuery = $conn->prepare("SELECT tags.name FROM tags INNER JOIN todo_tags ON tags.id = todo_tags.tag_id WHERE todo_tags.todo_id = :todo_id");
                $tagsQuery->bindParam(':todo_id', $todo['id'], PDO::PARAM_INT);
                $tagsQuery->execute();
                $tags = $tagsQuery->fetchAll(PDO::FETCH_ASSOC);

                $html .= "
                <div class='alert alert-info p-2'>
                    <h4>{$todo['title']}</h4>
                    <p><strong>Assigned To:</strong> {$todo['developer_names']}</p>
                    <p><strong>Priority:</strong> {$todo['priority_name']}</p>
                    <p><strong>Created by:</strong> {$todo['created_by_name']}</p>
                    <p><strong>Created at:</strong> {$todo['created_at']}</p>
                    " . (!empty($tags) ? "<p><strong>Tags:</strong> " . implode(', ', array_column($tags, 'name')) . "</p>" : "") . "
                    <!-- Add more details as needed -->
                    <div class='d-flex justify-content-between mt-3'>
                        <a href='task_details.php?id={$todo['id']}' class='btn btn-info p-1 text-white'>Details</a>
                        
                        "/* . ($status === 'todo' ? "<a href='handle/goto.php?name=doing&id={$todo['id']}&referrer=index.php' class='btn btn-info p-1 text-white'>Doing</a>" : "") */ ."
                        "/* . ($status === 'doing' ? "<a href='handle/goto.php?name=done&id={$todo['id']}&referrer=index.php' class='btn btn-info p-1 text-white'>Done</a>" : "") */ ."
                        
                        " . ($status === 'done' ? "
                            <div class='d-flex'>
                                <a href='handle/delete.php?id={$todo['id']}' onclick='return confirm(\"Are you sure?\")' class='btn btn-danger p-1 mr-2'>
                                    <i class='fa fa-times'></i>
                                </a>
                            </div>
                        " : "") . "
                    </div>
                </div>
            ";
            //<a href='handle/goto.php?name=doing&id={$todo['id']}&referrer=index.php' class='btn btn-info p-1 text-white'>Move to Doing</a>

        }
        } else {
            // Handle the case when there are no tasks for the given status
            $html = "<div class='item'><div class='alert-info text-center'>No tasks for {$status}</div></div>";
        }

        return $html;
    }
