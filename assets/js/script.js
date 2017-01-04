jQuery(function($) {
    $('#ldw-mco .ldw_mco_backtotop a').click(function(e){
        e.preventDefault;
        $('html, body').animate({  
            scrollTop:0
        }, 'slow');  
    })
});