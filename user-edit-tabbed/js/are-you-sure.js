//http://stackoverflow.com/questions/1119289/how-to-show-the-are-you-sure-you-want-to-navigate-away-from-this-page-when-ch

var $j = jQuery;

$j(function () {
    $j("input, textarea, select").on("input change", function() {
        window.onbeforeunload = window.onbeforeunload || function (e) {
            return "You have unsaved changes!";
        };
    });
    $j("form").on("submit", function() {
        window.onbeforeunload = null;
    });
})