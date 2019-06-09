<?php
    session_start();
    if(!empty($_POST["id"])){

        require_once("../services/dbConn.php");

        function buttonLikeDislike($snapping_id, $user_id, $snapping, $conn){
            $stmt = $conn->prepare("SELECT * FROM liked_snapping WHERE 
                fk_user_id = :user_id AND fk_snapping_id = :snapping_id");
            $stmt->bindParam(":user_id", $user_id);
            $stmt->bindParam(":snapping_id", $snapping_id);
            $stmt->execute();
            $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $query = $stmt->fetchAll();

            $liked = count($query);

            if ($liked == 0) {
                echo '<button id="likeBtn'.$snapping_id.'" class="likeBtn" 
                onclick="likeDislike(1, '.$_SESSION["user"].', '.$snapping["snapping_id"].')">Like</button>';
                echo '<button id="dislikeBtn'.$snapping_id.'" class="dislikeBtn" 
                onclick="likeDislike(0, '.$_SESSION["user"].', '.$snapping["snapping_id"].')">Dislike</button>';
                echo '<style scoped>';
                echo '#dislikeBtn'.$snapping["snapping_id"].' {display: none;}';
                echo '</style>';
            } else {
                echo '<button id="likeBtn'.$snapping_id.'" class="likeBtn" 
                onclick="likeDislike(1, '.$_SESSION["user"].', '.$snapping["snapping_id"].')">Like</button>';
                echo '<button id="dislikeBtn'.$snapping_id.'" class="dislikeBtn" 
                onclick="likeDislike(0, '.$_SESSION["user"].', '.$snapping["snapping_id"].')">Dislike</button>';
                echo '<style scoped>';
                echo '#likeBtn'.$snapping["snapping_id"].' {display: none;}';
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

            $stmt = $conn->prepare("SELECT * FROM snapping WHERE snapping_id < :lastID ORDER BY snapping_id DESC LIMIT 2");
            $stmt->bindParam(":lastID", $lastID);
            $stmt->execute();
            $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $snappings = $stmt->fetchAll();
        }catch(PDOException $e){
            echo "Connection failed: " . $e->getMessage();
        }

        echo "<br><br><br><br>";

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
                echo '<div class="list-item">';
                echo '<div><a href="profile.php?user='.$user[0]["username"].'"><img src="profile_pics/'.$user[0]["profile_pic"].'"/>
                '.$user[0]["username"].'</a></div>';
                if(strlen($snapping["real_world_location"]) > 0){
                    echo "<p>Location: ".$snapping["real_world_location"]."</p>";
                }
                echo '<a href="snapping.php?snapping='.$snapping["snapping_id"].'"><img src="snappings/'.$snapping["location"].'"/></a>';
                echo '<div>Created on '.$snapping["date"].'</div>';
                echo '<p>'.$snapping["description"].'</p>';
                buttonLikeDislike($snapping["snapping_id"], $_SESSION["user"], $snapping, $conn);
                echo '<div id="'."snapping".$snapping["snapping_id"].'">'.$likes.'</div>';
                echo '</div>';
                echo '</div>';
            }
        } 
        if($num_rows > 2){ 
            echo '<div class="load-more" lastID="'.$postID.'" style="display: none;">';
            echo '<p>loading</p>';
            echo'</div>';
        }else{ 
            echo'<div class="load-more" lastID="0">';
            echo "That's All!";
            echo "</div>";
        }
    }else{ 
    echo '<div class="load-more" lastID="0">';
    echo "That's All!";
    echo "</div>";
    }
    $conn = NULL;
?>
