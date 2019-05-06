(function () {
    $("header .menu")
        .dropdownJS()
        .on('selected', function ($event, action) {
            switch (action) {
                case "logout":
                    api.auth.logout().then(function () {
                        window.location.href = "/mi-chat/login";
                    });
                    break;
                case "profile":
                    window.location.href = "/mi-chat/profile";
                    break;
            }
        });

    $(window).on("load", function () {
        setTimeout(function () {
            $("body").addClass("loaded");
        }, 500);
    });
})();
