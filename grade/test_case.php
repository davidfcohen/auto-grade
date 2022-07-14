<?php
    include ("../db.php");

    $response["success"] = 0;

    if (isset($_POST["attempt_id"], 
        $_POST["question_id"], 
        $_POST["exam_id"],
        $_POST["test_case_id"],
        $_POST["passed"]) && $_POST["passed"] == 0 || $_POST["passed"] == 1)
    {
        $attempt_id = $_POST["attempt_id"];
        $question_id = $_POST["question_id"];
        $exam_id = $_POST["exam_id"];
        $test_case_id = $_POST["test_case_id"];
        $passed = $_POST["passed"];

        $db = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

        if (!mysqli_connect_errno($db))
        {
            $sql = "INSERT INTO RESULT (attempt_id, question_id, exam_id, 
                        test_case_id, passed)
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("iiiii", $attempt_id, $question_id, $exam_id, 
                $test_case_id, $passed);
            if($stmt->execute()) 
            {
                $response["success"] = 1;
            }
        }
        else
        {
            $response["error"] = "SQL connect error "
                                 . mysqli_connect_error($db);
        }
    }
    else
    {
        $response["error"] = "Invalid POST";
    }
    echo json_encode($response);
?>