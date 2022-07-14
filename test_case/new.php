<?php
    include ("../db.php");
    
    // Set default response
    $response["success"] = 0;

    if (isset($_POST["question_id"],
            $_POST["driver"],
            $_POST["result"]))
    {
        // Get question info from POST request
        $question_id = $_POST["question_id"];
        $driver = $_POST["driver"];
        $result = $_POST["result"];

        // Connect DB
        $db = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

        // Check DB connection
        if (!mysqli_connect_errno($db))
        {
            // Insert test case
            $sql = "INSERT INTO TEST_CASE 
                        (question_id, driver, result)
                    VALUES (?, ?, ?)";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("iss", $question_id, $driver, $result);
            if ($stmt->execute()) 
            {
                $response["test_case_id"] = $db->insert_id;
                $response["success"] = 1; 
            } 
            else 
            {
                $response["error"] = mysqli_error($db);
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