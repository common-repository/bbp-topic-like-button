(function ($) {

  var BBPTLB = {

    $like_button_wrap: null,
    $like_button: null,

    /**
     * Called on doc ready. Fires off the initial ajax request to fetch the html for the like button.
     */
    init: function () {
      this.$like_button_wrap = $('.bbpress-topic-like-button');
      if (!this.$like_button_wrap.length) {
        return;
      }
      var data = {
        'action': 'bbpress_topic_like_button_get_button',
        'args': bbptlp_object.args
      };
      jQuery.post(bbptlp_object.ajax_url, data, this.data_loaded);
    },

    /**
     * Callback for after the inital ajax request has returned the html for the like button.
     *
     * @param html
     */
    data_loaded: function (html) {
      BBPTLB.$like_button_wrap.html(html);
      BBPTLB.$like_button = BBPTLB.$like_button_wrap.find('a');
      BBPTLB.$like_button.on('click', BBPTLB.click)
    },

    /**
     * Handler for when the user clicks on the like button.
     */
    click: function (e) {
      e.preventDefault();
      BBPTLB.$like_button_wrap.addClass('bbpress-topic-like-button--loading');
      var action = 'bbpress_topic_like_button_click';
      var data = {
        'action': action,
        'method': BBPTLB.$like_button.html() === bbptlp_object.args.like ? 'like' : 'unlike',
        'args': bbptlp_object.args
      };
      jQuery.post(bbptlp_object.ajax_url, data, BBPTLB.click_ajax_callback);
      return false;
    },

    /**
     * Callback for after the click ajax request has completed.
     *
     * @param data
     */
    click_ajax_callback: function (data) {
      data = JSON.parse(data);
      var html = data.res === 'liked' ? bbptlp_object.args.unlike : bbptlp_object.args.like;
      BBPTLB.$like_button.html(html).attr('href', '#' + html.toLowerCase());
      BBPTLB.$like_button_wrap.removeClass('bbpress-topic-like-button--loading');
    }

  };

  $(document).ready(function () {
    BBPTLB.init();
  });

})(jQuery);
