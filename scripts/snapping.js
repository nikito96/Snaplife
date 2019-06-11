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