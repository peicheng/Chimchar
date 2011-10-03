$(document).ready(function() {
    $(".minisites").hide();
});

function toggle(name) {
    $(".minisites").hide();
    $(".posts").hide();
    $("." + name).fadeIn(750);
}
