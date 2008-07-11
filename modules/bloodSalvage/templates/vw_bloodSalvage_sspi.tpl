<script type="text/javascript">
function submitTiming(oForm) {
  submitFormAjax(oForm, 'systemMsg', { 
    onComplete : function() { 
      reloadTiming(oForm.blood_salvage_id.value);
    } 
  });
}

function submitInfos(oForm) {
  submitFormAjax(oForm, 'systemMsg', { 
    onComplete : function() { 
      reloadInfos(oForm.blood_salvage_id.value);
    } 
  });
}

function reloadInfos(blood_salvage_id) {
  var url = new Url(); 
    url.setModuleAction("bloodSalvage", "httpreq_vw_bloodSalvage_infos");
    url.addParam("blood_salvage_id", blood_salvage_id);
    url.requestUpdate('cell-saver-infos', { waitingText: null } );
}

function reloadTotalTime(blood_salvage_id) {
  var url = new Url();
  url.setModuleAction("bloodSalvage", "httpreq_total_time");
  url.addParam("date", "{{$date}}");
  url.addParam("blood_salvage_id", blood_salvage_id);
  url.requestUpdate("totaltime", { waitingText: null } );
}

function reloadTiming(blood_salvage_id){ 
  var url = new Url();
    url.setModuleAction("bloodSalvage", "httpreq_vw_bs_sspi_timing");
    url.addParam("blood_salvage_id", blood_salvage_id);
    url.requestUpdate("timing", { waitingText: null } );
    reloadTotalTime(blood_salvage_id);  
}

function reloadNurse(blood_salvage_id){
  var url = new Url;
  url.setModuleAction("bloodSalvage", "httpreq_vw_blood_salvage_personnel");
  url.addParam("blood_salvage_id", blood_salvage_id);
  url.requestUpdate("listNurse", {
    waitingText: null
  } );
}


function pageMain() {  
  var url = new Url;
  url.setModuleAction("bloodSalvage", "httpreq_liste_patients_bs");
  url.addParam("date", "{{$date}}");
  url.periodicalUpdate('listRSPO', { frequency: 90 });
  {{if $blood_salvage->_id}}
      new PairEffect("listRSPO", { sEffect : "appear", bStartVisible : true });
  
      url.setModuleAction("bloodSalvage", "httpreq_total_time");
      url.addParam("blood_salvage_id", "{{$blood_salvage->_id}}");
      url.periodicalUpdate("totaltime", { frequency: 60 });
  {{/if}}  
}
</script>

<table class="main">
<tr>
<td style="width: 390px;" id="listRSPO"></td>
<td>
{{if $blood_salvage->_id}}
  {{include file=inc_vw_sspi_bs.tpl}}
{{else}}
  <div class="big-info">Veuillez sélectionner un patient.</div>
{{/if}}
</td>
</tr>
</table>