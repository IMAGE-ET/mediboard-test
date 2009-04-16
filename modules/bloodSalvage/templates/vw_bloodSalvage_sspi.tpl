{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage bloodSalvage
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var="module" value="bloodSalvage"}}
{{assign var="object" value=$blood_salvage}}
{{mb_include_script module="bloodSalvage" script="bloodSalvage"}}

<script type="text/javascript">
Main.add(function () {
  var url = new Url;
  url.setModuleAction("bloodSalvage", "httpreq_liste_patients_bs");
  url.addParam("date","{{$date}}");
  url.periodicalUpdate('listRSPO', { frequency: 90 });
  {{if $selOp->_id}}
      url.setModuleAction("bloodSalvage","httpreq_vw_sspi_bs");
      url.addParam("date","{{$date}}");
      url.requestUpdate("bloodSalvageSSPI", {waitingText: null});
  {{/if}}  
});
</script>

<table class="main">
<tr>
	<td class="halfPane" id="listRSPO"></td>
  <td class="halfPane">
 	{{if $selOp->_id}}
    <div id="bloodSalvageSSPI"></div>
	{{else}}
	  <div class="big-info">Veuillez sélectionner un patient.</div>
	{{/if}}
	</td>
</tr>
</table>