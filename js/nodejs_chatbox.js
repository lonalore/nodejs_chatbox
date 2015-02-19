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
      $.ajax({
        type: "POST",
        url: $('#nchatbox').attr('action'),
        data: {message: $('#nchatbox textarea').val()},
        dataType: "json"
      }).done(function (data) {
        if (data.status == "error") {
          alert(data.message);
        }
        $('#nchatbox textarea').val('');
      });
      return false;
    });

    $('#nchatbox #showemotes').click(function () {
      $('#ncemote').toggle();
      return false;
    });
  });
})(jQuery);
