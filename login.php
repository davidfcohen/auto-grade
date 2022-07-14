<?php
    include ("db.php");
    
    // Set default response
    $response["success"] = 0;

    if (isset($_POST["username"], $_POST["password"]))
    {
        // Get user credentials from POST request
        $username = $_POST["username"];
        $password = $_POST["password"];
        
        // Connect DB
        $db = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

        // Check DB connection
        if (!mysqli_connect_errno($db)) 
        {    
            // Pull account info from DB
            $sql = "SELECT A.account_id, username, first_name, last_name, 
                        password_hash, created, student_id, teacher_id
                    FROM ACCOUNT A 
                        LEFT OUTER JOIN STUDENT S 
                            ON A.account_id = S.account_id
                        LEFT OUTER JOIN TEACHER T 
                            ON A.account_id = T.account_id
                    WHERE username = ?";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $row = $stmt->get_result()->fetch_assoc();
            $db->close();

            // Verify username and password
            if ($row && password_verify($password, $row["password_hash"])) 
            {
                // Build JSON response
                $response["username"] = $row["username"];
                $response["first_name"] = $row["first_name"];
                $response["last_name"] = $row["last_name"];
                $response["created"] = $row["created"];
                if (isset($row["student_id"]))
                    $response["student_id"] = $row["student_id"];
                else
                    $response["teacher_id"] = $row["teacher_id"];
                $response["success"] = 1;
            } 
        } 
        else
        {
            $response["error"] = "SQL connect error: "
                                 . mysqli_connect_error($db);
        }
    }
    // Encode JSON response
    echo json_encode($response);
?>
