jQuery(document).ready(function($) {

  $(".Payamito-ultimate-member-modal ").click(function() {
      debugger;
      $('#Payamito-ultimate-member-modal').modal();
  })

  $('.payamito-gf-tag-modal').click(function(){
      $(this).CopyToClipboard();
   
      });

      $('.payamito-gf-tag-modal').jTippy({trigger:'click' ,theme: 'green',position:'bottom', size: 'small',title:'کپی شد'});
});