<?php
    include ("../db.php");

    $response["success"] = 0;

    if (isset($_POST["attempt_id"], 
        $_POST["question_id"], 
        $_POST["exam_id"],
        $_POST["grade_header"],
        $_POST["grade_constraint"],
        $_POST["grade_test_case"]))
    {
        $attempt_id = $_POST["attempt_id"];
        $question_id = $_POST["question_id"];
        $exam_id = $_POST["exam_id"];
        $grade_header = $_POST["grade_header"];
        $grade_constraint = $_POST["grade_constraint"];
        $grade_test_case = $_POST["grade_test_case"];

        $db = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

        if (!mysqli_connect_errno($db))
        {
            if (isset($_POST["comment"])) 
            {
                $comment = $_POST["comment"];
                $sql = "UPDATE ATTEMPT_QUESTION
                        SET grade_header = ?, grade_constraint = ?,
                            grade_test_case = ?, comment = ?
                        WHERE attempt_id = ? AND question_id = ? 
                            AND exam_id = ?";
                $stmt = $db->prepare($sql);
                $stmt->bind_param("dddsiii", $grade_header, $grade_constraint,
                    $grade_test_case, $comment, $attempt_id, $question_id,
                    $exam_id);
            } 
            else
            {
                $sql = "UPDATE ATTEMPT_QUESTION
                        SET grade_header = ?, grade_constraint = ?,
                            grade_test_case = ?
                        WHERE attempt_id = ? AND question_id = ? 
                            AND exam_id = ?";
                $stmt = $db->prepare($sql);
                $stmt->bind_param("dddiii", $grade_header, $grade_constraint,
                    $grade_test_case, $attempt_id, $question_id,
                    $exam_id);
            }
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