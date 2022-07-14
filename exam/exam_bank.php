<?php
    include ("../db.php");
    
    // Set default response
    $response["success"] = 0;

    // Get teacher_id from POST request
    $teacher_id = $_POST["teacher_id"];

    // Connect DB
    $db = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

    // Check DB connection
    if (!mysqli_connect_errno($db))
    {
        // Pull questions from DB
        if (isset($_POST["teacher_id"]))
        {
            $teacher_id = $_POST["teacher_id"];
            $sql = "SELECT exam_id, teacher_id, title, created
                    FROM EXAM
                    WHERE teacher_id = ?";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("i", $teacher_id);
        } else {
            $sql = "SELECT exam_id, teacher_id, title, created
                    FROM EXAM";
            $stmt = $db->prepare($sql);
        }
        $stmt->execute();
        $exams = $stmt->get_result();

        for ($i = 0; $exam = $exams->fetch_assoc(); $i++)
        {
            $response["exams"][$i]["exam_id"] = $exam["exam_id"];
            $response["exams"][$i]["teacher_id"] = $exam["teacher_id"];
            $response["exams"][$i]["title"] = $exam["title"];
            $response["exams"][$i]["created"] = $exam["created"];
        }
        $response["success"] = 1;
    }
    else 
    {
        $response["error"] = "SQL connect error: "
                                . mysqli_connect_error($db);
    }

    // Encode JSON response
    echo json_encode($response);
?>
