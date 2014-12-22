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
    {{if $callback != ""}}
      url.requestModal('360px', '260px', {onClose: function() { {{$callback}}() } });
    {{else}}
      url.requestModal('360px', '260px');
    {{/if}}
  }
</script>

<button type="button" class="edit notext" onclick="editInfoInterv()"></button>

{{if @$modules.mvsante->_can->read && "mvsante"|module_active}}
  <span class="countertip" style="margin-top:2px;">
    {{$operation->_ref_liaison_libelles|@count}}
  </span>&nbsp;
{{/if}}