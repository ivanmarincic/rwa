(function () {
    var progressBar = $(".profile .profile-image .preview .progress-bar").progressJS();
    var progressOverlay = $(".profile .profile-image .preview .overlay");
    var changePassword = false;
    $(".profile .profile-image .picker").on('change', function ($event) {
        var that = $(this).get(0);
        if (that.files && that.files[0]) {
            if (that.files[0].size > 4000 * 1000) {
                $(".profile .profile-image .error-messages").text("Maksimalna veličina 4MB");
                return;
            }
            $(".profile .profile-image .error-messages").empty();
            var reader = new FileReader();
            reader.onload = function (e) {
                $(".profile .profile-image .preview img").attr('src', e.target.result);
                api.users.setProfileImage(that.files[0], function ($event) {
                    if ($event.lengthComputable) {
                        var progress = $event.loaded / $event.total;
                        progressBar.setProgress(progress);
                        if (progress < 1 && progress >= 0) {
                            progressOverlay.css("display", "block");
                        } else {
                            progressOverlay.css("display", "none");
                        }
                    } else {
                        // Unable to compute progress information since the total size is unknown
                        console.log('unable to complete');
                    }
                }).then(function (data) {
                    if (data) {
                        if (data.image && data.image.length > 0) {
                            $(".profile .profile-image .error-messages").text(data.image[0]);
                        } else {
                            $(".profile .profile-image .error-messages").text("");
                        }
                    }
                });
            };
            reader.readAsDataURL(that.files[0]);
        }
    });

    $(".profile .tool-bar .back").on("click", function () {
        window.location.href = "/mi-chat/home";
    });

    $(".profile .form .change-password button").on('click', function () {
        var passwordsContainer = $(".profile .form .passwords-container");
        passwordsContainer.toggleClass("active");
        if (passwordsContainer.hasClass("active")) {
            passwordsContainer.prepend($("<input>")
                .attr("type", "hidden")
                .attr("name", "change_password")
                .val("true")
                .addClass("helper"));
        } else {
            passwordsContainer.removeChild(passwordsContainer.find(".helper"));
        }
    });
})();
