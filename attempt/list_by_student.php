<?php
    include ("../db.php");
    
    // Set default response
    $response["success"] = 0;

    if (isset($_POST["student_id"]))
    {
        // Get uuid from POST request
        $student_id = $_POST["student_id"];

        // Connect DB
        $db = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

        // Check DB connection
        if (!mysqli_connect_errno($db))
        {
            // Pull questions from DB
            $sql = "SELECT A.attempt_id, student_id, A.exam_id, A.comment, released, 
                        A.created, E.title, 
                        ROUND(SUM(AQ.grade_header) +
                        SUM(AQ.grade_constraint) +
                        SUM(AQ.grade_test_case), 2) AS grade,
                        SUM(EQ.points) AS grade_max
                    FROM ATTEMPT AS A
                        JOIN EXAM AS E
                            ON A.exam_id = E.exam_id
                        LEFT JOIN ATTEMPT_QUESTION AS AQ
                            ON A.attempt_id = AQ.attempt_id
                        LEFT JOIN EXAM_QUESTION AS EQ
                            ON A.exam_id = EQ.exam_id
                                AND AQ.question_id = EQ.question_id
                    WHERE student_id = ?
                    GROUP BY A.attempt_id";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("i", $student_id);
            $stmt->execute();
            $attempts = $stmt->get_result();

            for ($i = 0; $attempt = $attempts->fetch_assoc(); $i++)
            {
                $response["attempts"][$i]["attempt_id"] = $attempt["attempt_id"];
                $response["attempts"][$i]["student_id"] = $attempt["student_id"];
                $response["attempts"][$i]["exam_id"] = $attempt["exam_id"];
                $response["attempts"][$i]["title"] = $attempt["title"];
                $response["attempts"][$i]["grade"] = $attempt["grade"];
                $response["attempts"][$i]["grade_max"] = $attempt["grade_max"];
                $response["attempts"][$i]["comment"] = $attempt["comment"];
                $response["attempts"][$i]["released"] = $attempt["released"];
                $response["attempts"][$i]["created"] = $attempt["created"];
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
