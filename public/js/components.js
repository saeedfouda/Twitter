function showAlert(msg){
    var item = $('<div class="alert-message"><div class="card"><div class="card-body" style="border-top: 0;"><i class="fa fa-times" onclick="$(\'.alert-message\').hide(\'fast\', function(){$(this).remove()})"></i>' + msg + '<br /></div></div></div>');
    $("body").prepend(item);
    setTimeout(function(){
        item.fadeOut('slow', function(){
            $(this).remove();
        });
    }, 2500);
}
