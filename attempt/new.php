<?php
    include ("../db.php");

    $response["success"] = 0;

    if (isset($_POST["student_id"], $_POST["exam_id"]))
    {
        $student_id = $_POST["student_id"];
        $exam_id = $_POST["exam_id"];

        $db = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

        if (!mysqli_connect_errno($db))
        {
            $sql = "INSERT INTO ATTEMPT (student_id, exam_id)
                    VALUES (?, ?)";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("ii", $student_id, $exam_id);
            if($stmt->execute()) 
            {
                $response["attempt_id"] = $db->insert_id;
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