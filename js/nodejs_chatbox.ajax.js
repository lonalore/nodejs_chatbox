var e107 = e107 || {'settings': {}, 'behaviors': {}};

(function ($) {
    $(document).ready(function () {
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
                    var html = data.message;
                    $(html).prependTo('#cb-wrapper .media-list').hide().fadeIn('slow');
                    // Cleanup textarea.
                    $('#nodejs-chatbox #ncb_message').val('');
                }

                // Enable button.
                $button.removeAttr('disabled');
            });
            return false;
        });
    });
})(jQuery);
