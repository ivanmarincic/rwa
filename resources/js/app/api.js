const api = {
    chats: {
        getById: function (id) {
            return $.ajax({
                type: 'GET',
                url: '/mi-chat/web/chats/getById/' + id
            });
        },
        save: function (data) {
            return $.ajax({
                type: 'POST',
                contentType: 'application/json',
                url: '/mi-chat/web/chats/save',
                data: data,
            });
        },
        updatePermissions: function (chat, participants) {
            return $.ajax({
                type: 'POST',
                contentType: 'application/json',
                url: '/mi-chat/web/chats/updatePermissions',
                data: JSON.stringify({
                    chat: chat,
                    participants: participants
                }),
            });
        },
        leave: function (chat) {
            return $.ajax({
                type: 'POST',
                contentType: 'application/json',
                url: '/mi-chat/web/chats/leave',
                data: JSON.stringify({
                    chat: chat
                }),
            });
        },
        search: function (searchQuery) {
            return $.ajax({
                type: 'POST',
                contentType: 'application/json',
                url: '/mi-chat/web/chats/search',
                data: JSON.stringify({
                    searchQuery: searchQuery
                }),
            });
        }
    },
    users: {
        search: function (searchQuery) {
            return $.ajax({
                type: 'POST',
                contentType: 'application/json',
                url: '/mi-chat/web/users/search',
                data: JSON.stringify({
                    searchQuery: searchQuery
                })
            });
        },
        getLoggedInUser: function () {
            return $.ajax({
                type: 'GET',
                url: '/mi-chat/web/users/getLoggedInUser'
            });
        },
        setProfileImage: function (file, onProgress) {
            var formData = new FormData();
            formData.append('image', file);
            return $.ajax({
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                xhr: function () {
                    var xhr = $.ajaxSettings.xhr();
                    if (xhr.upload && onProgress) {
                        xhr.upload.addEventListener('progress', onProgress, false);
                    }
                    return xhr;
                },
                url: '/mi-chat/web/users/setProfileImage'
            });
        },
        updateProfile: function (data) {
            return $.ajax({
                type: 'POST',
                data: data,
                url: '/mi-chat/web/users/updateProfile'
            });
        },
        getProfileImage: function (filename) {
            return $.ajax({
                type: 'POST',
                data: data,
                url: '/mi-chat/web/users/profileImage/' + filename
            });
        }
    },
    auth: {
        logout: function () {
            return $.ajax({
                type: 'POST',
                url: '/mi-chat/auth/logout'
            });
        }
    },
    messages: {
        save: function (content, chat, user) {
            return $.ajax({
                type: 'POST',
                contentType: 'application/json',
                url: '/mi-chat/web/messages/save',
                data: JSON.stringify({
                    content: content,
                    chat: chat,
                    user: user
                })
            });
        },
        delete: function (message, chat) {
            return $.ajax({
                type: 'POST',
                contentType: 'application/json',
                url: '/mi-chat/web/messages/delete',
                data: JSON.stringify({
                    message: message,
                    chat: chat
                })
            });
        },
        update: function (message, content, chat) {
            return $.ajax({
                type: 'POST',
                contentType: 'application/json',
                url: '/mi-chat/web/messages/update',
                data: JSON.stringify({
                    message: message,
                    content: content,
                    chat: chat
                })
            });
        },
        getByChat: function (chat, offset) {
            return $.ajax({
                type: 'POST',
                contentType: 'application/json',
                url: '/mi-chat/web/messages/getByChat',
                data: JSON.stringify({
                    chat: chat,
                    offset: offset
                })
            });
        }
    }
};
