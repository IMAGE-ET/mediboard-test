CKEDITOR.plugins.add('usermessage',{
  requires: ['iframedialog'],
  init:function(editor) {
    editor.addCommand('usermessage', {exec: usermessage_onclick});
    editor.ui.addButton('usermessage', {label:'Envoyer par mail', command:'usermessage',
      icon:'../../style/mediboard/images/buttons/mail.png' });
  }
});

function usermessage_onclick(editor){
  window.parent.openWindowMail();
}