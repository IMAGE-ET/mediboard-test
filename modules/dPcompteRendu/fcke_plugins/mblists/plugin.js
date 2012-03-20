/* $Id: plugin.js $
 *
 * @package Mediboard
 * @subpackage dPcompteRendu
 * @version $Revision: $
 * @author Romain OLLIVIER
 *
 * Add fields in the editor.
 */

CKEDITOR.plugins.add('mblists',{
  requires: ['iframedialog'],
  init:function(editor){
  CKEDITOR.dialog.add('mblists_dialog', function () {
    return {
    title : 'Insérer une liste de choix',
    buttons: [
    {
       id: 'close_button',
       type: 'button',
       title: 'Fermer',
       label: "Fermer",
       onClick: function(e) { CKEDITOR.dialog.getCurrent().hide(); }
     }
  ],
    minWidth : 350,
    minHeight : 210,
    contents :
    [
      {
        id : 'iframe',
        label : 'Insertion de liste de choix',
        expand : true,
        elements :
          [
            {
              type : 'iframe',
              src : 'modules/dPcompteRendu/fcke_plugins/mblists/dialogs/lists.html',
              width : 350,
              height : 210
            }
          ]
     }
   ]
   };
   });
   
   editor.addCommand('mblists', {exec: mblists_onclick});
   editor.ui.addButton('mblists', {label:'Listes de choix', command:'mblists',
     icon:'../../modules/dPcompteRendu/fcke_plugins/mblists/images/mblists.png' });
  }
});

function mblists_onclick(editor) {
  CKEDITOR.instances.htmlarea.openDialog('mblists_dialog');
}