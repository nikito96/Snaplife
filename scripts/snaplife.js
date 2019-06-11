$(document).ready(function(){
    $(window).scroll(function(){
        var lastID = $('.load-more').attr('lastID');
        if(($(window).scrollTop() == $(document).height() - $(window).height()) && (lastID != 0)){
            $.ajax({
                    type:'POST',
                    url:'services/getSnappings.php',
                    data:'id='+lastID,
                    beforeSend:function(){
                        $('.load-more').show();
                    },
                    success:function(html){
                        $('.load-more').remove();
                        $('#postList').append(html);
                    }
                }
            );
        }
    });
});

function likeDislike(action, user_id, snapping_id){
    var xhttp;
    var data = [action, user_id, snapping_id];
    data = JSON.stringify(data);
    xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var likes = xhttp.responseText;
            document.getElementById("snapping"+snapping_id).innerHTML = likes;
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