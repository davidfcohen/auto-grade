<?php
    include ("../db.php");
    
    // Set default response
    $response["success"] = 0;

    if (isset($_POST["teacher_id"]))
    {
        // Get uuid from POST request
        $teacher_id = $_POST["teacher_id"];

        // Connect DB
        $db = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

        // Check DB connection
        if (!mysqli_connect_errno($db))
        {
            // Pull questions from DB
            $sql = "SELECT question_id, topic, difficulty,
                        function_name, function_type, content, solution,
                        created
                    FROM QUESTION
                    WHERE teacher_id = ?";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("i", $teacher_id);
            $stmt->execute();
            $questions = $stmt->get_result();

            for ($i = 0; $question = $questions->fetch_assoc(); $i++)
            {
                // Pull test cases from DB
                $sql = "SELECT test_case_id, driver, result
                        FROM TEST_CASE
                        WHERE question_id = ?";
                $stmt =  $db->prepare($sql);
                $stmt->bind_param("i", $question["question_id"]);
                $stmt->execute();
                $test_cases = $stmt->get_result();

                // Build JSON response
                $response["questions"][$i]
                    ["question_id"] = $question["question_id"];
                $response["questions"][$i]
                    ["topic"] = $question["topic"];
                $response["questions"][$i]
                    ["difficulty"] = $question["difficulty"];
                $response["questions"][$i]
                    ["function_name"] = $question["function_name"];
                $response["questions"][$i]
                    ["function_type"] = $question["function_type"];
                $response["questions"][$i]
                    ["content"] = $question["content"];
                $response["questions"][$i]
                    ["solution"] = $question["solution"];
                for ($j = 0; $test_case = $test_cases->fetch_assoc(); $j++) 
                {
                    $response["questions"][$i]["test_cases"][$j]
                        ["test_case_id"] = $test_case["test_case_id"];
                    $response["questions"][$i]["test_cases"][$j]
                        ["driver"] = $test_case["driver"];
                    $response["questions"][$i]["test_cases"][$j]
                        ["result"] = $test_case["result"];
                }
            }
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
