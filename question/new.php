<?php
    include ("../db.php");
    
    // Set default response
    $response["success"] = 0;

    if (isset($_POST["teacher_id"],
            $_POST["topic"],
            $_POST["difficulty"],
            $_POST["function_name"],
            $_POST["content"],
            $_POST["solution"]))
    {
        // Connect DB
        $db = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

        // Check DB connection
        if (!mysqli_connect_errno($db))
        {
            // Insert question into DB
            if(isset($_POST["function_type"]) && $_POST["function_type"] != "")
            {
                $sql = "INSERT INTO QUESTION (teacher_id, topic,
                            difficulty, function_name, function_type, content, 
                            solution)
                        VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $db->prepare($sql);
                $stmt->bind_param("issssss",
                    $_POST["teacher_id"],
                    $_POST["topic"],
                    $_POST["difficulty"],
                    $_POST["function_name"],
                    $_POST["function_type"],
                    $_POST["content"],
                    $_POST["solution"]);
            }
            else
            {
                $sql = "INSERT INTO QUESTION (teacher_id, topic,
                            difficulty, function_name, content, solution)
                        VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $db->prepare($sql);
                $stmt->bind_param("isssss",
                    $_POST["teacher_id"],
                    $_POST["topic"],
                    $_POST["difficulty"],
                    $_POST["function_name"],
                    $_POST["content"],
                    $_POST["solution"]);
            }   
            if($stmt->execute())
            {
                // Build JSON response
                $response["test"] = $_POST["content"];
                $response["question_id"] = $db->insert_id; 
                $response["success"] = 1;
            }
            else
            {
                $response["error"] = $db->error;
            }
        }
        else 
        {
            $response["error"] = "SQL connect error: "
                                 . mysqli_connect_error($db);
        }
    }
    else 
    {
        $response["error"] = "Invalid POST";
    }
    // Encode JSON response
    echo json_encode($response);
?>
