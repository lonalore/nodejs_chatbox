(function ($) {
  e107Nodejs.Nodejs.callbacks.nodejsChatbox = {
    callback: function (message) {

      switch (message.type) {
        case 'chatboxMessage':
          console.log(message);
          break;
      }
    }
  };
})(jQuery);
