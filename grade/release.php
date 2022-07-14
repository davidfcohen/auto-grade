<?php
    include ("../db.php");

    $response["success"] = 0;

    if (isset($_POST["exam_id"]))
    {
        $exam_id = $_POST["exam_id"];

        $db = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

        if (!mysqli_connect_errno($db))
        {
            $sql = "UPDATE ATTEMPT
                    SET released = 1
                    WHERE exam_id = ?";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("i", $exam_id);
            
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