{{*
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage reservation
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 *}}

<script type="text/javascript">
  sendMail = function() {
    var url = new Url("reservation", "ajax_send_mail");
    url.addParam("operation_id", "{{$operation_id}}");
    url.requestUpdate("systemMsg");
  }
</script>
<button type="button" class="mail" onclick="sendMail();">Envoyer l'email</button>
