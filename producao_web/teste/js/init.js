(function($){
  $(function(){

    $('.button-collapse').sideNav();

  }); // end of document ready
  $(document).ready(function(){
    $('.tooltipped').tooltip({delay: 50});
  });

  $(document).ready(function() {
    $('select').material_select();
  });

  $(document).ready(function(){
    $("#preload").fadeOut();
      // the "href" attribute of .modal-trigger must specify the modal ID that wants to be triggered
      $('.modal').modal();
    });

    $('.dropdown-button').dropdown({
    alignLeft: false
    });


})(jQuery); // end of jQuery name space
