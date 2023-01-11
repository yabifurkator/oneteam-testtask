$("td").live('click', function() {
    var class_name = $(this).attr('class').split(' ')[0];

    $("tr").removeClass('match-selected');
    $("td").removeClass('match-selected');
    $("td").not(this).removeClass('selected');
    $("td").not(this).removeClass('double-clicked');

    if (!$(this).hasClass('selected')) {
        $(this).addClass('selected');
        $("tr").filter('.' + class_name).addClass('match-selected');
    }
    else if(!$(this).hasClass('double-clicked')) {
        $("tr").removeClass('match-selected');
        $("td").filter('.' + class_name).not(this).addClass('match-selected');
        $(this).addClass('double-clicked');
    }
    else {
        $("tr").removeClass('match-selected');
        $("td").removeClass('match-selected');
        $("td").removeClass('selected');
        $("td").removeClass('double-clicked');
    }
    console.log(class_name);
});
