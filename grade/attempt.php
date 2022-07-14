<?php
    include ("../db.php");

    $response["success"] = 0;

    if (isset($_POST["attempt_id"], $_POST["comment"]))
    {
        $attempt_id = $_POST["attempt_id"];
        $comment = $_POST["comment"];

        $db = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

        if (!mysqli_connect_errno($db))
        {
            $comment = $_POST["comment"];
            $sql = "UPDATE ATTEMPT
                    SET comment = ?
                    WHERE attempt_id = ?";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("si", $comment, $attempt_id);
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