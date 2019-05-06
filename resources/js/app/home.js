(function () {
    var selectedUsers = [];
    var currentUser = null;
    var selectedChat = null;
    var selectedParticipant = null;
    var currentMessagesOffset = 0;
    var currentMessages = [];
    var lastMessage = null;
    var editMode = false;
    var selectedMessage = null;
    var newChatDialog = $("#new-chat").dialogJS();
    var userInfoDialog = $("#user-info").dialogJS();
    var chatParticipantsDialog = $("#chat-participants").dialogJS();
    var inputBar = $(".selected-chat .input-bar");
    var messageInput = inputBar.find(".message");
    var sendButton = inputBar.find(".send");

    api.users.getLoggedInUser().then(function (data) {
        currentUser = data;
        Echo.private('user.' + currentUser.id)
            .listen('MessageSent', function (e) {
                var message = e.message;
                if (selectedChat && message.chat_id === selectedChat.id) {
                    addMessage(message, true);
                } else {
                    notifyNewMessage(message)
                }
            })
            .listen('MessageUpdated', function (e) {
                var message = e.message;
                updateMessage(message);
            })
            .listen('MessageDeleted', function (e) {
                var message = e.message;
                deleteMessage(message);
            });
        if ($(window).width() >= 767) {
            $(".chats .chats-list .chat:first").trigger('click');
        }
    });

    newChatDialog.onSave(function (dialog) {
        api.chats.save(JSON.stringify({
            name: dialog.find("input[name='name']").val(),
            participants: selectedUsers
        })).then(function (data) {
            $(".chats .chats-list").prepend(getNewChat(data));
        });
    });


    chatParticipantsDialog.onSave(function (dialog) {
        var participants = selectedChat.participants.map(a => Object.assign({}, a));
        var list = dialog.find(".participants-list .participant-item");
        for (var i = 0; i < participants.length; i++) {
            participants[i]["is_admin"] = list.filter('[data-id="' + participants[i].id + '"]').find(".is-admin input[type='checkbox']").prop("checked") ? 1 : 0;
        }
        api.chats.updatePermissions(selectedChat.id, participants).then(function (data) {
            selectedChat.participants = participants;
        });
    });

    $(window).on("hashchange", function ($event) {
        if (!window.location.hash && $(window).width() < 767) {
            $(".chats.selected").removeClass("selected");
        }
    });

    $("#open-add-dialog").on('click',
        function ($event) {
            newChatDialog.open(function (dialog) {
                selectedUsers = [];
                dialog.find("input[name='name']").val(null);
                getFilteredUsers('');
            });
        });

    $(".back").on('click', function ($event) {
        $(".chats.selected").removeClass("selected");
    });

    $("#filter-users").on('keyup', $.debounce(400, function ($event) {
        getFilteredUsers($(this).val());
    }));

    $(".chats .top-bar .search").on('keyup', $.debounce(400, function ($event) {
        getFilteredChats($(this).val());
    }));

    $(document).on('click', ".chats .chats-list .chat", function ($event) {
        var that = $(this);
        window.location.hash = "selected";
        api.chats.getById(that.data("id")).then(function (data) {
            selectParticipant(data);
            selectChat(data);
            that.find(".unread").removeClass("active").empty();
        });
    });

    $(document).on('click', ".selected-chat .conversation .wrapper .message-header", function ($event) {
        var that = $(this);
        var user = null;
        var userId = parseInt(that.data("id"));
        if (selectedChat && selectedChat.participants) {
            for (var i = 0; i < selectedChat.participants.length; i++) {
                var participantUser = selectedChat.participants[i].user;
                if (participantUser && participantUser.id === userId) {
                    user = participantUser;
                    break;
                }
            }
        }
        userInfoDialog.open(function (dialog) {
            dialog.find(".profile-image img").attr("src", globals.getProfileImageUrl(user));
            dialog.find(".username").text(user['username']);
            dialog.find(".name").text(user['full_name']);
            dialog.find(".email").text(user['email']);
        });
    });

    sendButton.addClass("disabled").on('click', function ($event) {
        messageInput.focus();
        sendMessage(messageInput.val());
    });

    messageInput.on('keypress', function ($event) {
        if ($event.which === 13) {
            messageInput.focus();
            sendMessage(messageInput.val());
        }
    });

    messageInput.on("change paste keyup", function () {
        var message = messageInput.val();
        if (message && message.length > 0) {
            sendButton.removeClass("disabled");
        } else {
            sendButton.addClass("disabled");
        }
    });

    inputBar.find(".cancel-edit").on('click', function ($event) {
        editMode = false;
        messageInput.val("");
        selectedMessage = null;
        inputBar.removeClass("edit-mode");
    });

    var lastScroll = 0;
    $(".selected-chat .conversation").on('scroll', function ($event) {
        var scrollTop = $(this).scrollTop();
        if (scrollTop <= 50 && scrollTop <= lastScroll && lastScroll >= 50) {
            getMessagesByChat(currentMessagesOffset + 15)
        }
        lastScroll = scrollTop;
    });

    $(".selected-chat .options")
        .dropdownJS()
        .on('selected', function ($event, action) {
            switch (action) {
                case "participants":
                    chatParticipantsDialog.open(function (dialog) {
                        var list = dialog.find(".participants-list");
                        list.empty();
                        if (selectedChat.participants) {
                            for (var i = 0; i < selectedChat.participants.length; i++) {
                                var participant = selectedChat.participants[i];
                                var participantDiv = $("<div/>")
                                    .append($("<div/>")
                                        .addClass("participant-item")
                                        .attr("data-id", participant.id)
                                        .append($("<div/>")
                                            .addClass("name")
                                            .text(globals.getName(participant.user)))
                                        .append($("<div/>")
                                            .addClass("is-admin")
                                            .append($("<input/>")
                                                .addClass(selectedParticipant["is_admin"] === 1 ? "" : "disabled")
                                                .attr("type", "checkbox")
                                                .attr("id", "admin-checkbox-" + i)
                                                .attr("checked", parseInt(participant["is_admin"]) === 1))
                                            .append($("<label/>")
                                                .attr("for", "admin-checkbox-" + i))));
                                list.append(participantDiv);
                            }
                        }
                        if (selectedParticipant["is_admin"] === 1) {
                            dialog.find(".save").removeClass("disabled");
                        } else {
                            dialog.find(".save").addClass("disabled");
                        }
                    });
                    break;
                case "leave":
                    api.chats.leave(selectedChat).then(function (data) {
                        window.location.reload();
                    });
                    break;
            }
        });

    $(".selected-chat #message-popup")
        .dropdownJS()
        .on('opened', function ($event, items, target) {
            var deleteItem = items.filter('[data-action="delete"]');
            if (selectedParticipant["is_admin"] === 1 || currentMessages[target.data("id")].participant.id === selectedParticipant["id"]) {
                deleteItem.removeClass("disabled");
            } else {
                deleteItem.addClass("disabled");
            }
            var editItem = items.filter('[data-action="edit"]');
            if (currentMessages[target.data("id")].participant.id === selectedParticipant["id"]) {
                editItem.removeClass("disabled");
            } else {
                editItem.addClass("disabled");
            }
        })
        .on('selected', function ($event, action, target) {
            switch (action) {
                case "edit":
                    selectedMessage = currentMessages[target.data("id")];
                    inputBar.addClass("edit-mode");
                    messageInput.val(selectedMessage["content"]);
                    sendButton.removeClass("disabled");
                    editMode = true;
                    break;
                case "delete":
                    api.messages.delete(target.data("id"), selectedChat.id);
                    break;
            }
        });

    function getFilteredChats(searchQuery) {
        api.chats.search(searchQuery).then(function (data) {
            var list = $(".chats .chats-list");
            list.empty();
            if (data.length > 0) {
                for (var i = 0; i < data.length; i++) {
                    list.append(getNewChat(data[i]));
                }
            } else if (searchQuery.length === 0 && data.length === 0) {
                list.append($("<p/>")
                    .addClass("empty")
                    .text("Nema razgovora"));
            } else {
                list.append($("<p/>")
                    .addClass("empty")
                    .text("Nema rezultata"));
            }
            bindFilteredItems();
        });
    }

    function getNewChat(chat) {
        if (chat) {
            var chatDiv = $("<div/>")
                .addClass("chat")
                .attr("data-id", chat.id);
            var image = $("<div/>")
                .addClass("image");
            for (var i = 0; i < chat.participants.length; i++) {
                var participant = chat.participants[i];
                if (participant.user.id !== currentUser.id) {
                    image.append($("<img/>")
                        .attr("src", globals.getProfileImageUrl(participant.user)));
                }
            }
            var name = $("<div/>")
                .addClass("name")
                .text(chat.name);
            var unread = $("<span/>")
                .addClass("unread");
            chatDiv.append(image);
            chatDiv.append(name);
            chatDiv.append(unread);
            return chatDiv;
        }
        return null;
    }

    function getFilteredUsers(searchQuery) {
        api.users.search(searchQuery).then(function (data) {
            var list = $(".users-list .filtered-list");
            list.html("");
            for (var i = 0; i < data.length; i++) {
                var user = data[i];
                var item = $("<div/>")
                    .addClass('item')
                    .data("id", user.id)
                    .append($("<span/>").addClass("name").text(user.username))
                    .append($("<span/>").addClass("selection").append($("<span/>").addClass("material-icons").html("done")));
                if (selectedUsers.indexOf(user.id) !== -1) {
                    item.addClass('selected');
                }
                list.append(item);
            }
            bindFilteredItems();
        });
    }


    function bindFilteredItems(callback) {
        $(".add-new-chat .users-list .filtered-list .item").on('click', function ($event) {
            var item = $(this);
            var userId = item.data('id');
            item.toggleClass('selected');
            if (item.hasClass('selected')) {
                if (userId) {
                    if (selectedUsers.indexOf(userId) === -1) {
                        selectedUsers.push(userId);
                    }
                }
                if (callback) {
                    callback();
                }
            } else {
                if (userId) {
                    var index = selectedUsers.indexOf(userId);
                    if (index > -1) {
                        selectedUsers.splice(index, 1);
                    }
                }
            }
        });
    }

    function sendMessage(message) {
        if (message && message.length > 0) {
            sendButton.addClass("disabled");
            if (!editMode) {
                api.messages.save(message, selectedChat.id, selectedParticipant.id).then(function (data) {
                    messageInput.val(null);
                    messageInput.focus();
                    sendButton.addClass("disabled");
                });
            } else {
                api.messages.update(selectedMessage.id, message, selectedChat.id).then(function (data) {
                    messageInput.val(null);
                    messageInput.focus();
                    sendButton.addClass("disabled");
                });
            }
        }
    }

    function addMessage(message, animated, prepend) {
        var conversation = $(".selected-chat .conversation");
        var conversationWrapper = conversation.find(".wrapper");
        var messageDiv = $("<div/>").addClass("message");
        var isMe = false;
        if (message.participant.id === selectedParticipant.id) {
            messageDiv.addClass("me");
            isMe = true;
        } else {
            messageDiv.addClass("other");
            isMe = false;
        }
        messageDiv.data("id", message.id);
        messageDiv.attr("data-id", message.id);
        messageDiv
            .append($("<p/>")
                .addClass("text")
                .text(message.content))
            .append($("<i/>")
                .addClass("material-icons")
                .addClass("options")
                .addClass("menu")
                .attr("data-target", "#message-popup")
                .attr("data-id", message.id)
                .text("more_vert"))
            .append($("<span/>")
                .addClass("edit")
                .text("Uređeno"))
            .addClass(message["edited_at"] ? "edited" : "");
        currentMessages[message.id] = message;
        if (prepend === true) {
            var firstMessage = conversationWrapper.children().eq(0);
            var messageHeader = null;
            if ((firstMessage.hasClass("other") || firstMessage.length === 0) && isMe) {
                messageHeader = getMessageHeader('me');
                conversationWrapper.prepend(messageHeader);
            } else if ((firstMessage.hasClass("me") || firstMessage.length === 0) && !isMe) {
                messageHeader = getMessageHeader('other', message.participant.user);
                conversationWrapper.prepend(messageHeader);
            } else if ((firstMessage.hasClass("other")) && !isMe && message.participant.user.id !== parseInt(firstMessage.data("id"))) {
                messageHeader = getMessageHeader('other', message.participant.user);
                conversationWrapper.prepend(messageHeader);
            } else {
                messageHeader = firstMessage;
            }
            messageDiv.insertAfter(messageHeader);
        } else {
            if (!lastMessage || lastMessage.length === 0) {
                lastMessage = conversationWrapper.find(".message:last");
            }
            if (!lastMessage || lastMessage.length === 0) {
                if (isMe) {
                    conversationWrapper.append(getMessageHeader('me'));
                } else {
                    conversationWrapper.append(getMessageHeader('other', message.participant.user));
                }
            } else if (lastMessage.hasClass("other") && isMe) {
                conversationWrapper.append(getMessageHeader('me'));
            } else if (lastMessage.hasClass("me") && !isMe) {
                conversationWrapper.append(getMessageHeader('other', message.participant.user));
            }
            conversationWrapper.append(messageDiv);
            lastMessage = messageDiv;
            currentMessagesOffset += 1;
        }
        if (animated && animated === true) {
            setTimeout(function () {
                conversation.animate({scrollTop: conversationWrapper.height()}, "slow");
            });
        }
    }

    function updateMessage(message) {
        var conversation = $(".selected-chat .conversation");
        var conversationWrapper = conversation.find(".wrapper");
        if (message.chat.id === selectedChat.id) {
            var messageDiv = conversationWrapper.find(".message[data-id='" + message.id + "']");
            messageDiv.find(".text").text(message.content);
            messageDiv.addClass("edited");
            editMode = false;
            messageInput.val("");
            selectedMessage = null;
            inputBar.removeClass("edit-mode");
        }
    }

    function deleteMessage(message) {
        var conversation = $(".selected-chat .conversation");
        var conversationWrapper = conversation.find(".wrapper");
        var messageDiv = conversationWrapper.find('.message[data-id="' + message + '"]');
        var isMe = messageDiv.hasClass("me");
        var messageDivBefore = messageDiv.prev();
        var messageDivAfter = messageDiv.next();
        if (messageDivBefore.hasClass("message-header") && ((messageDivAfter.hasClass("other") && isMe) || (messageDivAfter.hasClass("me") && !isMe) || messageDivAfter.length === 0)) {
            if (messageDivBefore.prev().length > 0) {
                messageDivAfter.remove();
            }
            messageDivBefore.remove();
        }
        messageDiv.remove();
    }

    function getMessageHeader(headerFor, user) {
        if (!user && headerFor === "me") {
            user = currentUser;
        }
        var header = $("<div/>")
            .addClass("message-header")
            .addClass(headerFor);
        var image = $("<img/>");
        image.attr("src", globals.getProfileImageUrl(user));
        header.append(image);
        var name = $("<span/>");
        name.text(globals.getName(user));
        header.append(name);
        header.attr("data-id", user.id);
        return header
    }

    function getMessagesByChat(offset, callback) {
        api.messages.getByChat(selectedChat.id, offset).then(function (data) {
            var lastFirstMessage = $('.selected-chat .conversation .wrapper .message:first');
            for (var i = 0; i < data.length; i++) {
                addMessage(data[i], false, true);
            }
            if (data.length > 0) {
                currentMessagesOffset = offset;
                if (lastFirstMessage.length > 0 && offset > 0) {
                    $('.selected-chat .conversation').scrollTop(lastFirstMessage.offset().top);
                }
            }
            if (callback) {
                callback();
            }
        });
    }

    function notifyNewMessage(message) {
        var unreadChat = $('.chats .chats-list .chat[data-id="' + message.chat_id + '"]');
        var unread = unreadChat.find(".unread");
        var unreadCount = unread.text();
        if (unreadCount) {
            unread.text(parseInt(unreadCount) + 1);
        } else {
            unread.text(1);
        }
        unread.addClass("active");
        var first = $(".chats .chats-list .chat:first");
        if (first.length > 0) {
            unreadChat.insertBefore(first);
        }
    }

    function selectChat(chat) {
        if (!selectedChat || selectedChat.id !== chat.id) {
            $(".selected-chat .name").text(chat.name);
            selectedChat = chat;
            messageInput.val(null);
            var conversation = $(".selected-chat .conversation");
            var conversationWrapper = conversation.find(".wrapper");
            conversationWrapper.empty();
            getMessagesByChat(0, function () {
                conversation.scrollTop(conversationWrapper.height());
            });
        }
        $(".chats").addClass("selected");
        $(".selected-chat").addClass("selected");
    }

    function selectParticipant(chat) {
        selectedParticipant = null;
        for (var i = 0; i < chat.participants.length; i++) {
            var participant = chat.participants[i];
            if (participant.user.id === currentUser.id) {
                selectedParticipant = participant;
                break;
            }
        }
    }
})();
