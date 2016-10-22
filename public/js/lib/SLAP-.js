(function($){

  function slap_links(selector){

    if (typeof selector === "undefined") selector = '.SLAP-menu a, a.SLAP-link'

    $(selector).click(function(){
      $('.SLAP-menu a').removeClass('selected');

      //!!! check all nav a's hrefs incase an in-line link should add the 'selected' class to the nav a

      $(this).addClass('selected');

      var pageUrl = 'ajax'+(this.pathname.indexOf('/') === 0 ? '':'/')+this.pathname;

      // * remove old content
      $('#SLAP-content')
        .fadeOut(300, function(){
          // should do the ajax before the fading to save time
          $(this).html('<p class="loading"><img src="/images/loading.gif"/><br/>Loading...</p>')
          .load(pageUrl, function(){
            slap_links('#SLAP-content a.SLAP-link:not(.SLAP-link-procssed)');
            $('#SLAP-content').fadeIn(300);
          })
        });;

      // * set ajax fail
      return false;
    }).addClass('SLAP-link-processed');
  }

  $(document).ready(function(){
    slap_links();
  });

})(jQuery);
