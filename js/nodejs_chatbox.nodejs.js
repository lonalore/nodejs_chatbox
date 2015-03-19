(function ($) {
    /**
     * Register Node.js callback function to handle pushed messages.
     *
     * @type {{callback: Function}}
     */
    e107Nodejs.Nodejs.callbacks.nodejsChatbox = {
        callback: function (message) {
            switch (message.type) {
                case 'chatboxMessage':
                    var html = message.data;
                    $(html).prependTo('#cb-wrapper .media-list').hide().fadeIn('slow');
                    break;
            }
        }
    };

    $(document).ready(function () {
        $('#nodejs_chatbox #ncb_submit').click(function () {
            $button = $(this);

            // Disable button while posting.
            $button.attr('disabled', 'disabled');

            var nickname = '';
            if ($('#nodejs_chatbox #ncb_nickname').length) {
                nickname = $('#nodejs_chatbox #ncb_nickname').val();
            }

            // Do an Ajax request to backend.
            $.ajax({
                type: "POST",
                url: $('#nodejs_chatbox').attr('action'),
                data: {
                    nickname: nickname,
                    message: $('#nodejs_chatbox #ncb_message').val(),
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
                    $('#nodejs_chatbox #ncb_message').val('');
                }

                // Enable button.
                $button.removeAttr('disabled');
            });
            return false;
        });
    });
})(jQuery);
