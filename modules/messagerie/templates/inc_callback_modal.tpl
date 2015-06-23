{{*
 * $Id$
 *  
 * @package    Mediboard
 * @subpackage messagerie
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 * @link       http://www.mediboard.org
*}}

<script type="text/javascript">
  callbackModalMessagerie = function(messagerie, object_id) {
    var systemMsg = window.parent.$("systemMsg").update('{{$messages|smarty:nodefaults}}');
    systemMsg.show();
    if (window.parent.$$("systemMsg div.info") && $('closeModal') && $V($('closeModal')) != 0) {
      window.parent.Control.Modal.close();
    }
    else {
      if (object_id && messagerie) {
        if (messagerie == 'internal') {
          var form = getForm('edit_usermessage');
          $V(form.usermessage_id, object_id);
        }
      }
    }
  }
</script>