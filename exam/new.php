<?php
    include ("../db.php");
    
    // Set default response
    $response["success"] = 0;

    if (isset($_POST["teacher_id"], $_POST["title"]))
    {
        // Get question info from POST request
        $teacher_id = $_POST["teacher_id"];
        $title = $_POST["title"];

        // Connect DB
        $db = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

        // Check DB connection
        if (!mysqli_connect_errno($db))
        {
            // Insert exam into DB
            $sql = "INSERT INTO EXAM (teacher_id, title)
                    VALUES (?, ?)";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("is", $teacher_id, $title);
            $stmt->execute();

            // Build JSON response
            $response["exam_id"] = $db->insert_id; 
            $response["success"] = 1;
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
