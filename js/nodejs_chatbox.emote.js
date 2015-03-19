(function ($) {
    $(document).ready(function () {
        $('#nodejs-chatbox #ncb_message').click(function () {
            storeCaret(this);
        });

        $('#nodejs-chatbox #ncb_message').select(function () {
            storeCaret(this);
        });

        $('#nodejs-chatbox #ncb_message').keyup(function () {
            storeCaret(this);
        });

        $('#nodejs-chatbox #ncb_showemotes').click(function () {
            $('#ncb_emote').toggle();
            return false;
        });
    });
})(jQuery);
