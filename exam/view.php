<?php
    include ("../db.php");
    
    // Set default response
    $response["success"] = 0;

    if (isset($_POST["exam_id"]))
    {
        // Get exam_id from POST request
        $exam_id = $_POST["exam_id"];

        $db = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
        if (!mysqli_connect_errno($db))
        {
            // Get EXAM information
            $sql = "SELECT teacher_id, title, created, 
                        SUM(EQ.points) AS grade_max
                    FROM EXAM AS E
                        LEFT JOIN EXAM_QUESTION AS EQ ON E.exam_id = EQ.exam_id
                    WHERE E.exam_id = ?
                    GROUP BY E.exam_id";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("i", $exam_id);
            $stmt->execute();
            $exam = $stmt->get_result()->fetch_assoc();

            // Build JSON response
            $response["teacher_id"] = $exam["teacher_id"];
            $response["title"] = $exam["title"];
            $response["created"] = $exam["created"];
            $response["grade_max"] = $exam["grade_max"];

            // Get QUESTION information
            $sql = "SELECT EQ.question_id, points, topic, difficulty,
                        function_name, function_type, content, solution,
                        created
                    FROM EXAM_QUESTION AS EQ 
                        JOIN QUESTION AS Q ON EQ.question_id = Q.question_id
                    WHERE exam_id = ? AND EQ.question_id = Q.question_id";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("i", $exam_id);
            $stmt->execute();
            $questions = $stmt->get_result();

            for ($i = 0; $question = $questions->fetch_assoc(); $i++)
            {
                // Get TEST_CASE information
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
                    ["points"] = $question["points"];
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
    echo json_encode($response)
?>