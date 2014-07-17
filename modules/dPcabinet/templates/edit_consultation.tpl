{{*
 * $Id$
 *  
 * @category Cabinet
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

{{assign var=auto_refresh_frequency value=$conf.dPcabinet.CConsultation.auto_refresh_frequency}}

{{if $current_m == "dPurgences"}}
  {{mb_script module="planningOp" script="sejour"}}
{{/if}}

<script>
  Main.add(function() {
    ListConsults.init("{{$consult->_id}}", "{{$userSel->_id}}", "{{$date}}", "{{$vue}}", "{{$current_m}}", "{{$auto_refresh_frequency}}");
  })
</script>

{{if $consult->_ref_consult_anesth->_id}}
  {{assign var=operation value=$consult->_ref_consult_anesth->_ref_operation}}
  {{if $operation->_id}}
    <form name="addOpFrm" action="?m={{$m}}" method="post">
      <input type="hidden" name="dosql" value="do_consult_anesth_aed" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="m" value="dPcabinet" />
      <input type="hidden" name="sejour_id" value="{{$operation->sejour_id}}" />
      <input type="hidden" name="operation_id" value="{{$operation->_id}}" />
    </form>
  {{/if}}
{{/if}}

<table class="main">
  <tr>
    <td id="listConsult" style="width: 240px;"></td>
    <td>{{mb_include module=cabinet template=inc_full_consult}}</td>
  </tr>
</table>
