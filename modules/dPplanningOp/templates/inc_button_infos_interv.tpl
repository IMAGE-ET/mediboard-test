{{*
 * $Id$
 *  
 * @category SalleOp
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

{{mb_default var=callback value=""}}

<script>
  editInfoInterv = function() {
    var url = new Url('planningOp', 'ajax_edit_infos_interv');
    url.addParam('operation_id', '{{$operation->_id}}');
    url.requestModal('360px', '260px', {onClose: function() { {{$callback}}() } });
  }
</script>

<button type="button" class="edit notext" onclick="editInfoInterv()"></button>