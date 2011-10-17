CKEDITOR.plugins.add('mbmail',{
  requires: ['iframedialog'],
  init:function(editor) {
    editor.addCommand('mbmail', {exec: mbmail_onclick});
    editor.ui.addButton('mbmail', {label:'Envoyer par mail', command:'mbmail',
      icon:'../../style/mediboard/images/buttons/mail.png' });
  }
});

function mbmail_onclick(editor){
  window.parent.openWindowMail();
}