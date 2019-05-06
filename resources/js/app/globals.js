globals = {
    getName: function (user) {
        if (user) {
            return user["full_name"] || user["username"];
        }
        return "";
    },
    getProfileImageUrl: function (user) {
        if (user && user['profile_image']) {
            return "/mi-chat/web/users/profileImage/" + user['profile_image'];
        } else {
            return "/mi-chat/resources/images/default_user.png";
        }
    }
};
