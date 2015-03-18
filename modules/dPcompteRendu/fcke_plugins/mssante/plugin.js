/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage compteRendu
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 * @link       http://www.mediboard.org
 */

CKEDITOR.plugins.add('mssante', {
  requires: ['dialog'],
  init: function(editor) {
    editor.addCommand('mssante', {exec: mssante_onclick});
    editor.ui.addButton('mssante',
      {
        label: 'Envoyer via MSSanté',
        command: 'mssante',
        icon: '../../style/mediboard/images/buttons/mailMSSante.png'
      });
  }
});

function mssante_onclick(editor) {
  openWindowMSSante();
}
