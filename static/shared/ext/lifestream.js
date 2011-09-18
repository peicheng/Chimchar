$(document).ready(function() {
    list = [
        {
            service: 'github',
            user: 'bcxx',
        }
    ];

    $(".stream_content").lifestream({
        limit: 150,
        list: list
    });

    $("a.label").click(
        function() {
            $(".stream_content").fadeToggle(500, "linear");
        }    
    );
});
