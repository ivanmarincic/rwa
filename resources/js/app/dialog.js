(function ($) {
    $.fn.dialogJS = function () {
        var dialog = this;
        var dialogContainer = $(".dialog-container");
        var close = function (isSave) {
            dialogContainer.off('transitionend webkitTransitionEnd oTransitionEnd');
            dialogContainer.one('transitionend webkitTransitionEnd oTransitionEnd', function ($event) {
                if($event.originalEvent.propertyName === "opacity"){
                    dialog.css("display", "none");
                    dialogContainer.css("display", "none");
                    if (isSave === true) {
                        dialog.trigger('saved');
                    } else {
                        dialog.trigger('closed');
                    }
                }
            });
            dialogContainer.removeClass("active");
            dialog.removeClass("opened");
        };
        var open = function (initializer) {
            if (initializer) {
                initializer(dialog);
            }
            dialog.css("display", "");
            dialogContainer.css("display", "");
            setTimeout(function () {
                dialogContainer.off('transitionend webkitTransitionEnd oTransitionEnd');
                dialogContainer.one('transitionend webkitTransitionEnd oTransitionEnd', function () {
                    dialog.trigger('opened');
                });
                dialogContainer.addClass("active");
                dialog.addClass("opened");
            })
        };
        var setOpenEvent = function (callback) {
            dialog.on('opened', function ($event) {
                if (callback) {
                    callback(dialog, $event);
                }
            });
        };
        var setCloseEvent = function (callback) {
            dialog.on('closed', function ($event) {
                if (callback) {
                    callback(dialog, $event);
                }
            });
        };
        var setSaveEvent = function (callback) {
            dialog.on('saved', function ($event) {
                if (callback) {
                    callback(dialog, $event);
                }
            });
        };
        dialog.css("display", "none");
        var closeButton = dialog.find(".close");
        closeButton.on('click', function () {
            close();
        });
        var saveButton = dialog.find(".save");
        saveButton.on('click', function () {
            close(true);
        });
        return {
            open: open,
            onOpen: setOpenEvent,
            close: close,
            onClose: setCloseEvent,
            onSave: setSaveEvent,
        };
    };
}(jQuery));

