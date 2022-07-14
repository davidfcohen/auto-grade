<?php
    include ("../db.php");
    
    // Set default response
    $response["success"] = 0;

    if (isset($_POST["question_id"]))
    {
        // Get question info from POST request
        $question_id = $_POST["question_id"];
        // Connect DB
        $db = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

        // Check DB connection
        if (!mysqli_connect_errno($db))
        {
            $sql = "DELETE FROM QUESTION
                    WHERE question_id = ?";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("i", $question_id);
            if($stmt->execute()) 
            {
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
