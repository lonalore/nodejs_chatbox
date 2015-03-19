(function ($) {
    $(document).ready(function () {
        $('#nodejs_chatbox #ncb_message').click(function () {
            storeCaret(this);
        });

        $('#nodejs_chatbox #ncb_message').select(function () {
            storeCaret(this);
        });

        $('#nodejs_chatbox #ncb_message').keyup(function () {
            storeCaret(this);
        });

        $('#nodejs_chatbox #ncb_showemotes').click(function () {
            $('#ncb_emote').toggle();
            return false;
        });
    });
})(jQuery);
