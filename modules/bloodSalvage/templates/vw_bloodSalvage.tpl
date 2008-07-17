{{assign var="module" value="bloodSalvage"}}
{{assign var="object" value=$blood_salvage}}
<script type="text/javascript">

function submitInfos(oForm) {
	submitFormAjax(oForm, 'systemMsg', { 
  	onComplete : function() { 
  		reloadInfos(oForm.blood_salvage_id.value);
  	} 
  });
}

function submitStartTiming(oForm) {
  submitFormAjax(oForm, 'systemMsg', { 
    onComplete : function() {       
      reloadStartTiming(oForm.blood_salvage_id.value);
    } 
  });
}

function reloadStartTiming(blood_salvage_id){ 
    var url = new Url();
    url.setModuleAction("bloodSalvage", "httpreq_vw_recuperation_start_timing");
    url.addParam("blood_salvage_id", blood_salvage_id);
    url.requestUpdate("start-timing", { waitingText: null } );
}

function reloadInfos(blood_salvage_id) {
	var url = new Url(); 
	  url.setModuleAction("bloodSalvage", "httpreq_vw_bloodSalvage_infos");
	  url.addParam("blood_salvage_id", blood_salvage_id);
	  url.requestUpdate('cell-saver-infos', { waitingText: null } );
}

function pageMain() {  
  var url = new Url;
  url.setModuleAction("bloodSalvage", "httpreq_liste_plages");
  url.addParam("date", "{{$date}}");
  url.addParam("operation_id", "{{$selOp->_id}}");
  url.periodicalUpdate('listplages', { frequency: 90 });
  {{if $selOp->_id}}
	  // Effet sur le programme
	  new PairEffect("listplages", { sEffect : "appear", bStartVisible : true });
  {{/if}}  
}
</script>

<table class="main">
<tr>
		<td style="width: 220px;" id="listplages"></td>
		<td>
			{{if $selOp->_id}}
				{{include file=inc_bloodSalvage.tpl}}
			{{else}}
				<div class="big-info">
					Veuillez sélectionner une opération dans la liste.
				</div>
			{{/if}}
		</td>
		</tr>
</table>
