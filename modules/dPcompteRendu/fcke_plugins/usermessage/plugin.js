CKEDITOR.plugins.add('usermessage',{
  requires: ['dialog'],
  init: function(editor) {
    editor.addCommand('usermessage', {exec: usermessage_onclick});
    editor.ui.addButton('usermessage', {label:'Envoyer par mail', command:'usermessage',
      icon:'../../style/mediboard/images/buttons/mail.png' });
  }
});

function usermessage_onclick(editor){
  openWindowMail();
}