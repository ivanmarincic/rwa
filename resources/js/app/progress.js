(function ($) {
    $.fn.progressJS = function () {
        var element = this;
        element.addClass("progress-bar");
        var bar = $("<div/>")
            .addClass("bar");
        element.append(bar);
        element.css("display", "none");
        return {
            setProgress: function (progress) {
                bar.css("width", (100 * progress) + "%");
                if (progress === 1 || progress === 0) {
                    setTimeout(function () {
                        element.css("display", "none");
                    }, 500);
                } else {
                    element.css("display", "");
                }
            }
        }
    };
}(jQuery));
