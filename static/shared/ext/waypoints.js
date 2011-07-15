$(document).ready(function() {
    // TODO 从下滚上时怎样获取上一个 h3 (sub title) 的值?
    // DIRTY_FIX 获取所有 h3 的 text() ，然后找出当前的
    // 位置，再获得上一个元素的字面值， 默认 h3 不会重复
    
    var h3_list = new Array();
    $(".content > h3").each(function(index) {
        h3_list.push($(this).text());
    });

    $(".content > h3").waypoint(function(event, direction) {
        var current_h3 = $(this);
    
        if (direction === "down") {
            $(".nav > .section_header").text(current_h3.text());
        } else {
            var position = h3_list.indexOf(current_h3.text());
            if (position !== -1 && position > 0) {
                $(".nav > .section_header").text(h3_list[position-1]);
            } else {
                $(".nav > .section_header").text('');
            }
        }
    });
});
