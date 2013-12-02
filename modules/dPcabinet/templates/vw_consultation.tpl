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
  {{mb_script module="dPplanningOp" script="sejour"}}
{{/if}}

<script>
  Main.add(function() {
    ListConsults.init("{{$consult->_id}}", "{{$userSel->_id}}", "{{$date}}", "{{$vue}}", "{{$current_m}}", "{{$auto_refresh_frequency}}");
  })
</script>

<table class="main">
  <tr>
    <td id="listConsult" style="width: 240px;"></td>
    <td>{{mb_include module=cabinet template=inc_new_full_consult}}</td>
  </tr>
</table>
