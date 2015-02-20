(function ($) {
  e107Nodejs.Nodejs.callbacks.nodejsChatbox = {
    callback: function (message) {
      switch (message.type) {
        case 'chatboxMessage':
          var html = message.data;
          $(html).prependTo('.nodejs-chatbox-body .media-list');
          break;
      }
    }
  };

  $(document).ready(function () {
    $('#nchatbox #ncmessage').click(function () {
      storeCaret(this);
    });

    $('#nchatbox #ncmessage').select(function () {
      storeCaret(this);
    });

    $('#nchatbox #ncmessage').keyup(function () {
      storeCaret(this);
    });

    $('#nchatbox #submitmessage').click(function () {
      $button = $(this);

      // Disable button while posting.
      $button.attr('disabled','disabled');

      var nickname = '';
      if ($('#nchatbox #ncnickname').length) {
        nickname = $('#nchatbox #ncnickname').val();
      }

      // Do an Ajax request to backend.
      $.ajax({
        type: "POST",
        url: $('#nchatbox').attr('action'),
        data: {nickname: nickname, message: $('#nchatbox #ncmessage').val()},
        dataType: "json"
      }).done(function (data) {
        if (data.status == "error") {
          alert(data.message);
        }

        if (data.status == "ok") {
          // Cleanup textarea.
          $('#nchatbox textarea').val('');
        }

        // Enable button.
        $button.removeAttr('disabled');
      });
      return false;
    });

    $('#nchatbox #showemotes').click(function () {
      $('#ncemote').toggle();
      return false;
    });
  });
})(jQuery);
