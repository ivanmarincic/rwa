(function ($) {
    $.fn.dropdownJS = function () {
        var element = this;
        var targetSelector = element.attr("data-target");
        var selectedTarget = null;
        var closeAll = function () {
            var opened = $(".menu.opened");
            opened.off('transitionend webkitTransitionEnd oTransitionEnd');
            opened.one('transitionend webkitTransitionEnd oTransitionEnd', function () {
                var that = $(this);
                if (that.hasClass(".absolute")) {
                    that.css("display", "none");
                }
                that.find(".dropdown").css("display", "none");
            });
            opened.removeClass("opened");
        };
        element.find(".dropdown").css("display", "none");
        var selectedItem = element.find(".selected-item");
        var arrow = selectedItem.find(".arrow");
        var dropdown = element.find(".dropdown");
        if (arrow.length === 0) {
            arrow = $("<i/>")
                .addClass("arrow")
                .addClass("material-icons")
                .text("arrow_drop_up");
            selectedItem.append(arrow);
        }
        var items = dropdown.find(".item");
        items.on('click', function ($event) {
            var that = $(this);
            if (!that.hasClass("disabled")) {
                closeAll();
                element.trigger('selected', [that.data("action"), selectedTarget]);
                $event.stopPropagation();
            }
        });
        var onClick = function ($event) {
            selectedTarget = $(this);
            if (!element.hasClass('opened')) {
                closeAll();
            }
            element.trigger("opened", [items, selectedTarget]);
            element.css("display", "");
            element.find(".dropdown").css("display", "");
            setTimeout(function () {
                if (element.hasClass("absolute")) {
                    var targetPosition = selectedTarget.offset();
                    var elementPosition = element.parent().offset();
                    var top = targetPosition.top - elementPosition.top + element.parent().scrollTop();
                    if (top + element.height() > element.parent()[0].scrollHeight) {
                        top -= element.height();
                        top += selectedTarget.height();
                    }
                    var left = targetPosition.left - elementPosition.left;
                    if (left + element.width() > element.parent().width()) {
                        left -= element.width();
                    }
                    if (left > element.parent().width() / 2) {
                        left += selectedTarget.width();
                    }
                    if (element.hasClass("opened")) {
                        element.removeClass('opened');
                        element.off('transitionend webkitTransitionEnd oTransitionEnd');
                        element.one('transitionend webkitTransitionEnd oTransitionEnd', function () {
                            element.css("top", top);
                            element.css("left", left);
                            element.addClass("opened");
                        });
                    } else {
                        element.css("top", top);
                        element.css("left", left);
                        element.toggleClass('opened');
                        element.off('transitionend webkitTransitionEnd oTransitionEnd');
                        element.one('transitionend webkitTransitionEnd oTransitionEnd', function () {
                            if (!element.hasClass("opened")) {
                                element.css("display", "none");
                                element.find(".dropdown").css("display", "none");
                            }
                        });
                    }
                } else {
                    element.toggleClass('opened');
                    element.off('transitionend webkitTransitionEnd oTransitionEnd');
                    element.one('transitionend webkitTransitionEnd oTransitionEnd', function () {
                        if (!element.hasClass("opened")) {
                            element.find(".dropdown").css("display", "none");
                        }
                    });
                }
            });
            $event.stopPropagation();
        };
        if (targetSelector) {
            $(document).on('click', targetSelector, onClick);
        } else {
            element.on('click', onClick);
        }
        element.parent().on("scroll", function () {
            closeAll();
        });
        $(window).on('click', function ($event) {
            closeAll();
            $event.stopPropagation();
        });
        return element;
    };
}(jQuery));
