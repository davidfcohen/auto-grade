<?php
    include ("../db.php");
    
    // Set default response
    $response["success"] = 0;

    if (isset($_POST["question_id"]) &&
        isset($_POST["exam_id"]))
    {
        // Get exam question info from POST request
        $question_id = $_POST["question_id"];
        $exam_id = $_POST["exam_id"];
        // Connect DB
        $db = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

        // Check DB connection
        if (!mysqli_connect_errno($db))
        {
            // Delete exam question from DB
            $sql = "DELETE FROM EXAM_QUESTION 
                    WHERE question_id = ? AND exam_id = ?";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("ii", $question_id, $exam_id);
            if($stmt->execute()) 
            {
                // Build JSON response
                $response["success"] = 1;
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
