<?php
    include ("../db.php");

    $response["success"] = 0;

    if (isset($_POST["attempt_id"], $_POST["question_id"], $_POST["exam_id"]))
    {
        $attempt_id = $_POST["attempt_id"];
        $question_id = $_POST["question_id"];
        $exam_id = $_POST["exam_id"];
        $content = $_POST["content"];

        $db = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

        if (!mysqli_connect_errno($db))
        {
            $sql = "INSERT INTO ATTEMPT_QUESTION 
                        (attempt_id, question_id, exam_id, content)
                    VALUES (?, ?, ?, ?)";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("iiis", $attempt_id, $question_id, $exam_id, 
                $content);
            if($stmt->execute()) 
            {
                $response["success"] = 1;
            }
            else 
            {
                $response["error"] = mysqli_error($db);
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