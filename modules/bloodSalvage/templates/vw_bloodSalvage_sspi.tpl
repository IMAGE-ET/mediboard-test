{{mb_include_script module="bloodSalvage" script="bloodSalvage"}}
<script type="text/javascript">
function pageMain() {  
  var url = new Url;
  url.setModuleAction("bloodSalvage", "httpreq_liste_patients_bs");
  url.addParam("date", "{{$date}}");
  url.periodicalUpdate('listRSPO', { frequency: 90 });
  {{if $selOp->_id}}
      new PairEffect("listRSPO", { sEffect : "appear", bStartVisible : true });
      url.setModuleAction("bloodSalvage","httpreq_vw_sspi_bs");
      url.requestUpdate("bloodSalvageSSPI", {waitingText: null});
  {{/if}}  
}
</script>

<table class="main">
<tr>
	<td class="halfPane" id="listRSPO"></td>
  <td class="halfPane">
 	{{if $selOp->_id}}
	  {{include file=inc_bs_sspi_header.tpl}}
    <div id="bloodSalvageSSPI"></div>
	{{else}}
	  <div class="big-info">Veuillez sélectionner un patient.</div>
	{{/if}}
	</td>
</tr>
</table>