<?php
    include ("../db.php");
    
    // Set default response
    $response["success"] = 0;

    if (isset($_POST["attempt_id"]))
    {
        // Get attempt_id from POST request
        $attempt_id = $_POST["attempt_id"];

        $db = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
        if (!mysqli_connect_errno($db))
        {
            // Get ATTEMPT information
            $sql = "SELECT student_id, A.exam_id, A.comment, released, created,
                        ROUND(SUM(AQ.grade_header) + 
                        SUM(AQ.grade_constraint) + 
                        SUM(AQ.grade_test_case), 2) AS grade,
                        SUM(EQ.points) AS grade_max
                    FROM ATTEMPT AS A
                        LEFT JOIN ATTEMPT_QUESTION AS AQ 
                            ON A.attempt_id = AQ.attempt_id
                        LEFT JOIN EXAM_QUESTION AS EQ
                            ON A.exam_id = EQ.exam_id 
                                AND AQ.question_id = EQ.question_id
                    WHERE A.attempt_id = ?
                    GROUP BY A.attempt_id";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("i", $attempt_id);
            $stmt->execute();
            $attempt = $stmt->get_result()->fetch_assoc();

            // Build JSON response
            $response["student_id"] = $attempt["student_id"];
            $response["exam_id"] = $attempt["exam_id"];
            $response["grade"] = $attempt["grade"];
            $response["grade_max"] = $attempt["grade_max"];
            $response["comment"] = $attempt["comment"];
            $response["released"] = $attempt["released"];
            $response["created"] = $attempt["created"];

            // Get ATTEMPT_QUESTION information
            $sql = "SELECT AQ.question_id, AQ.content AS attempt, 
                        AQ.grade_header, AQ.grade_constraint, 
                        AQ.grade_test_case, AQ.comment, Q.function_name, 
                        Q.function_type, Q.content, Q.solution, EQ.points 
                    FROM ATTEMPT_QUESTION AS AQ 
                        JOIN EXAM_QUESTION AS EQ ON AQ.question_id
                        JOIN QUESTION AS Q ON AQ.question_id
                    WHERE attempt_id = ? 
                        AND AQ.question_id = EQ.question_id
                        AND AQ.exam_id = EQ.exam_id 
                        AND EQ.question_id = Q.question_id";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("i", $attempt_id);
            $stmt->execute();
            $questions = $stmt->get_result();

            for ($i = 0; $question = $questions->fetch_assoc(); $i++)
            {
                // Get TEST_CASE information
                $sql = "SELECT test_case_id, driver, result
                        FROM TEST_CASE
                        WHERE question_id = ?";
                $stmt = $db->prepare($sql);
                $stmt->bind_param("i", $question["question_id"]);
                $stmt->execute();
                $test_cases = $stmt->get_result();

                // Build JSON response
                $response["questions"][$i]
                    ["question_id"] = $question["question_id"];
                $response["questions"][$i]
                    ["content"] = $question["content"];
                $response["questions"][$i]
                    ["attempt"] = $question["attempt"];
                $response["questions"][$i]
                    ["solution"] = $question["solution"];
                $response["questions"][$i]
                    ["function_name"] = $question["function_name"];
                $response["questions"][$i]
                    ["function_type"] = $question["function_type"];
                $response["questions"][$i]
                    ["grade_header"] = $question["grade_header"];
                $response["questions"][$i]
                    ["grade_constraint"] = $question["grade_constraint"];
                $response["questions"][$i]
                    ["grade_test_case"] = $question["grade_test_case"];
                $response["questions"][$i]
                    ["comment"] = $question["comment"];
                $response["questions"][$i]
                    ["points"] = $question["points"];
                for ($j = 0; $test_case = $test_cases->fetch_assoc(); $j++) 
                {
                    $sql = "SELECT passed
                        FROM RESULT
                        WHERE attempt_id = ? 
                            AND question_id = ? 
                            AND exam_id = ?
                            AND test_case_id = ?";
                    $stmt = $db->prepare($sql);
                    $stmt->bind_param("iiii", $attempt_id, 
                        $question["question_id"],
                        $attempt["exam_id"],
                        $test_case["test_case_id"]);
                    $stmt->execute();
                    $passed = $stmt->get_result()->fetch_assoc();
                    
                    $response["questions"][$i]["test_cases"][$j]
                        ["driver"] = $test_case["driver"];
                    $response["questions"][$i]["test_cases"][$j]
                        ["result"] = $test_case["result"];
                    $response["questions"][$i]["test_cases"][$j]
                        ["test_case_id"] = $test_case["test_case_id"];
                    $response["questions"][$i]["test_cases"][$j]
                        ["passed"] = $passed["passed"];
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