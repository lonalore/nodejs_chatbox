(function ($) {

    e107.behaviors.nodejsChatbox = {
        attach: function (context, settings) {
            $(context).find('#nodejs-chatbox #ncb_submit').once('nodejs-chatbox').each(function () {
                $('#nodejs-chatbox #ncb_submit').click(function () {
                    $button = $(this);

                    // Disable button while posting.
                    $button.attr('disabled', 'disabled');

                    var nickname = '';
                    if ($('#nodejs-chatbox #ncb_nickname').length) {
                        nickname = $('#nodejs-chatbox #ncb_nickname').val();
                    }

                    // Do an Ajax request to backend.
                    $.ajax({
                        type: "POST",
                        url: $('#nodejs-chatbox').attr('action'),
                        data: {
                            nickname: nickname,
                            message: $('#nodejs-chatbox #ncb_message').val(),
                            ncb_sent: 1,
                            ncb_sent_ajax: 1
                        },
                        dataType: "json"
                    }).done(function (data) {
                        if (data.status == "error") {
                            alert(data.message);
                        }

                        if (data.status == "ok") {
                            // Cleanup textarea.
                            $('#nodejs-chatbox #ncb_message').val('');
                        }

                        // Enable button.
                        $button.removeAttr('disabled');
                    });
                    return false;
                });
            });
        }
    };

    /**
     * Register Node.js callback function to handle pushed messages.
     *
     * @type {{callback: Function}}
     */
    e107.Nodejs.callbacks.nodejsChatbox = {
        callback: function (message) {
            switch (message.type) {
                case 'chatboxMessage':
                    var html = message.data;
                    $(html).prependTo('#cb-wrapper .media-list').hide().fadeIn('slow');
                    break;
            }
        }
    };

})(jQuery);
