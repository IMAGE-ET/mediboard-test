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
  url.setModuleAction("bloodSalvage", "httpreq_liste_plages");
  url.addParam("date", "{{$date}}");
  url.addParam("operation_id", "{{$selOp->_id}}");
  url.periodicalUpdate('listplages', { frequency: 90 });
  {{if $selOp->_id}}
	  // Effet sur le programme
	  new PairEffect("listplages", { sEffect : "appear", bStartVisible : true });
	  url.setModuleAction("bloodSalvage","httpreq_vw_bloodSalvage");
	  url.requestUpdate('bloodSalvage', { waitingText: null });
  {{/if}}  
});
</script>

<table class="main">
<tr>
		<td class="halfPane" id="listplages"></td>
    <td class="halfPane">
			{{if $selOp->_id}}
			  {{include file=inc_bloodSalvage_header.tpl}}
			  <div id="bloodSalvage"></div>
			{{else}}
				<div class="big-info">
					Veuillez sélectionner une intervention dans la liste.
				</div>
			{{/if}}
		</td>
		</tr>
</table>
