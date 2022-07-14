<?php
    include ("../db.php");
    
    // Set default response
    $response["success"] = 0;

    if (isset($_POST["exam_id"]))
    {
        // Get exam info from POST request
        $exam_id = $_POST["exam_id"];
        // Connect DB
        $db = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

        // Check DB connection
        if (!mysqli_connect_errno($db))
        {
            // Delete exam from DB
            $sql = "DELETE FROM EXAM WHERE exam_id = ?";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("i", $exam_id);
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
