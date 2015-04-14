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
  callbackModalMessagerie = function() {
    var systemMsg = window.parent.$("systemMsg").update('{{$messages|smarty:nodefaults}}');
    systemMsg.show();
    if (window.parent.$$("systemMsg div.info")) {
      window.parent.Control.Modal.close();
    }
  }
</script>