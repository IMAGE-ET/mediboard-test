/* $Id: ckplugin.js $
 *
 * @package Mediboard
 * @subpackage dPcompteRendu
 * @version $Revision: $
 * @author Sébastien Fillonneau
 *
 */

CKEDITOR.plugins.add('mbfields', {
  requires: ['dialog'],
  init: function(editor) {
  CKEDITOR.dialog.add('mbfields_dialog', function() {
    return {
      title : 'Insérer un champ',
      buttons: [
        {
          id: 'close_button',
          type: 'button',
          title: 'Fermer',
          label: "Fermer",
          onClick: function(e) { CKEDITOR.dialog.getCurrent().hide(); }
        }
      ],
      minWidth : 770,
      minHeight : 340,
      contents :
      [
        {
          label : 'Insertion de champs',
          expand : true,
          elements :
          [
            {
              type : 'html',
              html : '<iframe src="modules/dPcompteRendu/fcke_plugins/mbfields/dialogs/fields.html" style="width: 100%; height: 100%"></iframe>'
            }
          ]
        }
      ]
    };
  });
   
   editor.addCommand('mbfields', {exec: mbfields_onclick});
   editor.ui.addButton('mbfields', {label:'Champs', command:'mbfields',
     icon:'../../modules/dPcompteRendu/fcke_plugins/mbfields/images/mbfields.png' });
  }
});

function mbfields_onclick(editor) {
  CKEDITOR.instances.htmlarea.openDialog('mbfields_dialog');
}
