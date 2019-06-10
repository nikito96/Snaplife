$(document).ready(function(){
    $(window).scroll(function(){
        var lastID = $('.load-more').attr('lastID');
        var user_id = $('.load-more').attr("userID");
        var data = [lastID, user_id];

        data = JSON.stringify(data);
        if(($(window).scrollTop() == $(document).height() - $(window).height()) && (lastID != 0)){
            $.ajax({
                    type:'POST',
                    url:'services/getAccountSnappings.php',
                    data:'data='+data,
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