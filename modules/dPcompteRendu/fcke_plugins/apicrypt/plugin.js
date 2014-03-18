CKEDITOR.plugins.add('apicrypt', {
  requires: ['dialog'],
  init: function(editor) {
    editor.addCommand('apicrypt', {exec: apicrypt_onclick});
    editor.ui.addButton('apicrypt',
      {
        label: 'Envoyer via Apicrypt',
        command: 'apicrypt',
        icon: '../../style/mediboard/images/buttons/mailApicrypt.png'
      });
    }
});

function apicrypt_onclick(editor) {
  openWindowApicrypt();
}
