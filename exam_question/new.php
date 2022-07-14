<?php
    include ("../db.php");
    
    // Set default response
    $response["success"] = 0;

    if (isset($_POST["exam_id"], $_POST["question_id"], $_POST["points"]))
    {
        // Get question info from POST request
        $exam_id = $_POST["exam_id"];
        $question_id = $_POST["question_id"];
        $points = $_POST["points"];

        // Connect DB
        $db = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

        // Check DB connection
        if (!mysqli_connect_errno($db))
        {
            // Insert exam into DB
            $sql = "INSERT INTO EXAM_QUESTION
                        (exam_id, question_id, points)
                    VALUES (?, ?, ?)";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("iid", $exam_id, $question_id, $points);
            if($stmt->execute())
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
