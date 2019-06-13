function deleteSnapping(snapping_id){
	if (confirm("Do you really want to delete this snapping?")) {
		var xhttp;
    	xhttp = new XMLHttpRequest();
    	xhttp.onreadystatechange = function() {
		    if (this.readyState == 4 && this.status == 200) {
		    	window.location = "snaplife.php";
		    }
		};
		xhttp.open("GET", "services/deleteSnapping.php?snapping="+snapping_id, true);
		xhttp.send();
	}
}
function likeDislike(action, user_id, snapping_id){
    var xhttp;
    var data = [action, user_id, snapping_id];
    data = JSON.stringify(data);
    xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var likes = xhttp.responseText;
            document.getElementById("likes").innerHTML = likes;
            if (action == 1) {
                document.getElementById("likeBtn"+snapping_id).style.display = "none";
                document.getElementById("dislikeBtn"+snapping_id).style.display = "block";
            } else {
                document.getElementById("likeBtn"+snapping_id).style.display = "block";
                document.getElementById("dislikeBtn"+snapping_id).style.display = "none";
            }
        }
    };
    xhttp.open("POST", "services/postsActions.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("data="+data);
}