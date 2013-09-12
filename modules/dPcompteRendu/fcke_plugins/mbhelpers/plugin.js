/* $Id: plugin.js $
 *
 * @package Mediboard
 * @subpackage dPcompteRendu
 * @version $Revision: $
 * @author 
 *
 * Add fields in the editor
 */

CKEDITOR.plugins.add('mbhelpers',{
  requires: ['dialog'],
  init:function(editor){
  CKEDITOR.dialog.add('mbhelpers_dialog', function() {
    return {
      title : 'Insérer une aide à la saisie',
      buttons: [
      {
         id: 'close_button',
         type: 'button',
         title: 'Fermer',
         label: "Fermer",
         onClick: function(e) { CKEDITOR.dialog.getCurrent().hide(); }
       }
    ],
      minWidth : 450,
      minHeight : 210,
      contents :
      [
        {
          label : 'Insertion d\'aide à la saisie',
          expand : true,
          elements :
          [
            {
              type : 'html',
              html : '<iframe src="modules/dPcompteRendu/fcke_plugins/mbhelpers/dialogs/helpers.html" style="width: 100%; height: 100%"></iframe>'
            }
          ]
         }
       ]
     };
   });
   
   editor.addCommand('mbhelpers', {exec: mbhelpers_onclick});
   editor.ui.addButton('mbhelpers', {label: 'Aides à la saisie', command: 'mbhelpers',
   	 icon:'../../modules/dPcompteRendu/fcke_plugins/mbhelpers/images/mbhelpers.png' });
  }
});

function mbhelpers_onclick(editor) {
  CKEDITOR.instances.htmlarea.openDialog('mbhelpers_dialog');
}
