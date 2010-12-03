/* $Id: plugin.js $
 *
 * @package Mediboard
 * @subpackage dPcompteRendu
 * @version $Revision: $
 * @author 
 *
 * Adding free text area in the editor 
 */
 
CKEDITOR.plugins.add('mbfreetext',{
  requires: ['iframedialog'],
  init:function(editor){
  var dialog = CKEDITOR.dialog.add('mbfreetext_dialog', function () {
    return {
    title : 'Insérer une zone de texte libre',
    minWidth : 410,
    minHeight : 150,
    contents :
    [
      {
        id : 'iframe',
        label : 'Insertion de zone de texte libre',
        expand : true,
        elements :
          [
            {
              type : 'iframe',
              name: "iframe_mbfreetext",
              src : 'modules/dPcompteRendu/fcke_plugins/mbfreetext/dialogs/insert_area.html',
              width : 410,
              height : 150
            }
          ]
     }
   ]
   };
   });
   editor.addCommand('mbfreetext', {exec: mbfreetext_onclick});
   editor.ui.addButton('mbfreetext', {label:'Texte libre', command:'mbfreetext',
   	 icon:'../../modules/dPcompteRendu/fcke_plugins/mbfreetext/images/mbfreetext.png' });
  }
});

function mbfreetext_onclick(editor) {
  CKEDITOR.instances.htmlarea.openDialog('mbfreetext_dialog');
}
