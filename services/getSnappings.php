<?php
    session_start();
    if(!empty($_POST["id"])){

        require_once("../services/dbConn.php");

        function buttonLikeDislike($snapping_id, $user_id, $conn){
            try {
                $stmt = $conn->prepare("SELECT * FROM liked_snapping WHERE 
                    fk_user_id = :user_id AND fk_snapping_id = :snapping_id");
                $stmt->bindParam(":user_id", $user_id);
                $stmt->bindParam(":snapping_id", $snapping_id);
                $stmt->execute();
                $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
                $query = $stmt->fetchAll();
            } catch (PDOException $e) {
                echo "Connection failed: " . $e->getMessage();
            }

            $liked = count($query);

            if ($liked == 0) {
                echo '<button id="likeBtn'.$snapping_id.'" class="btn btn-primary" 
                onclick="likeDislike(1, '.$_SESSION["user"].', '.$snapping_id.')">Like</button>';
                echo '<button id="dislikeBtn'.$snapping_id.'" class="btn btn-primary" 
                onclick="likeDislike(0, '.$_SESSION["user"].', '.$snapping_id.')">Dislike</button>';
                echo '<style scoped>';
                echo '#dislikeBtn'.$snapping_id.' {display: none;}';
                echo '</style>';
            } else {
                echo '<button id="likeBtn'.$snapping_id.'" class="btn btn-primary" 
                onclick="likeDislike(1, '.$_SESSION["user"].', '.$snapping_id.')">Like</button>';
                echo '<button id="dislikeBtn'.$snapping_id.'" class="btn btn-primary" 
                onclick="likeDislike(0, '.$_SESSION["user"].', '.$snapping_id.')">Dislike</button>';
                echo '<style scoped>';
                echo '#likeBtn'.$snapping_id.' {display: none;}';
                echo '</style>';
            }
        }

        $lastID = $_POST['id'];

        try{
            $stmt = $conn->prepare("SELECT COUNT(*) as num_rows FROM snapping WHERE snapping_id < :lastID ORDER BY snapping_id DESC");
            $stmt->bindParam(":lastID", $lastID);
            $stmt->execute();
            $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $query = $stmt->fetchAll();
            $num_rows = $query[0]["num_rows"];

            $stmt = $conn->prepare("SELECT * FROM snapping WHERE snapping_id < :lastID ORDER BY snapping_id DESC LIMIT 6");
            $stmt->bindParam(":lastID", $lastID);
            $stmt->execute();
            $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $snappings = $stmt->fetchAll();
        }catch(PDOException $e){
            echo "Connection failed: " . $e->getMessage();
        }

        if($stmt->rowCount() > 0){
            foreach($snappings as $snapping){ 
                $postID = $snapping["snapping_id"];

                $stmt = $conn->prepare("SELECT * FROM liked_snapping WHERE fk_snapping_id = :snapping_id");
                $stmt->bindParam(":snapping_id", $snapping["snapping_id"]);
                $stmt->execute();
                $likes = $stmt->fetchAll();
                
                $likes = count($likes);

                $stmt = $conn->prepare("SELECT username, profile_pic FROM account WHERE user_id=:user_id");
                $stmt->bindParam(":user_id", $snapping["fk_user_id"]);
                $stmt->execute();
                $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
                $user = $stmt->fetchAll();
                echo '<div class="row">';
                echo '<div class="col-md-12">';
                echo '<div class="row"><div class="col-md-12"><a href="profile.php?user='.$user[0]["username"].'">
                <img class="profile_pic" src="profile_pics/'.$user[0]["profile_pic"].'"/></div>
                <div class="col-md-12">'.$user[0]["username"].'</a></div></div>';
                if(strlen($snapping["real_world_location"]) > 0){
                    echo '<div class="row"><div class="col-md-12 text-primary">
                    Location: '.$snapping["real_world_location"].'</div></div>';
                }
                echo '<div class="row"><div class="col-md-12">';
                echo '<a href="snapping.php?snapping='.$snapping["snapping_id"].'">
                <img class="img-fluid snapping" src="snappings/'.$snapping["location"].'"/></a>';
                echo '</div></div>';
                echo '<div class="row"><div class="col-md-12">';
                echo '<div>Created on '.$snapping["date"].'</div>';
                echo '</div></div>';
                echo '<div class="row"><div class="col-md-12">';
                echo '<div>'.$snapping["description"].'</div>';
                echo '</div></div>';
                if (strlen($snapping["tags"]) > 0) {
                    echo '<div class="row"><div class="col-md-12">';
                    echo '<div>tags: '.$snapping["tags"].'</div>';
                    echo '</div></div>';
                }
                echo '<div class="row"><div class="col-md-12">';
                buttonLikeDislike($snapping["snapping_id"], $_SESSION["user"], $conn);
                echo '</div>';
                echo '<div class="col-md-12">';
                echo '<div class="m-1" id="'."snapping".$snapping["snapping_id"].'">'.$likes.'</div>';
                echo '</div>';
                echo '<div class="col-md-12">';
                echo '<div class="m-1">likes</div>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
        } 
        if($num_rows > 2){ 
            echo '<div class="load-more" lastID="'.$postID.'" style="display: none;">';
            echo '<p>loading</p>';
            echo'</div>';
        }else{ 
            echo '<div class="row justify-content-center">';
            echo '<div class="col-md-12">';
            echo '<div class="load-more" lastID="0">';
            echo '<div class="text-primary font-weight-bold">That is All! </div>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }
    }else{
        echo '<div class="row justify-content-center">';
        echo '<div class="col-md-12">';
        echo '<div class="load-more" lastID="0">';
        echo '<div class="text-primary font-weight-bold">That is All! </div>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
    $conn = NULL;
?>
