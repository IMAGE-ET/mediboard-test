/* $Id: plugin.js $
 *
 * @package Mediboard
 * @subpackage dPcompteRendu
 * @version $Revision: $
 * @author 
 *
 * Alter line height
 */

CKEDITOR.plugins.add('mblineheight',{
  init: function(editor){
  CKEDITOR.dialog.add('mblineheight_dialog', function() {
    return {
      title : 'Augmenter / Réduire l\'interligne',
      minWidth : 300,
      minHeight : 90,
      contents :
      [
        {
          label : 'Augmenter / Réduire l\'interligne',
          expand : true,
          elements :
          [
            {
              type : 'html',
              html : '<iframe src="modules/dPcompteRendu/fcke_plugins/mblineheight/dialogs/mblineheight.html"></iframe>'
            }
          ]
         }
       ]
     };
   });
   
   editor.addCommand('mblineheight', {exec: mblineheight_onclick});
   editor.ui.addButton('mblineheight', {label:'Interligne de paragraphe', command:'mblineheight',
     icon:'../../modules/dPcompteRendu/fcke_plugins/mblineheight/images/mblineheight.png' });
  }
});

function mblineheight_onclick(editor) {
  CKEDITOR.instances.htmlarea.openDialog('mblineheight_dialog');
}