<script type="text/javascript">

delCibleTransmission = function() {
  var oDiv = $('cibleTrans');
  if(!oDiv) return;
  var oForm = document.forms['editTrans'];
  $V(oForm.object_class, '');
  $V(oForm.object_id, '');
  $V(oForm.libelle_ATC, '');
  oDiv.innerHTML = "";
}

showListTransmissions = function(page, total) {
	  $$("div.list_trans").invoke("hide");
	  $("list_"+page).show();
	  if (!page){
	    page = 0;
	  }
	  var url = new Url("system", "ajax_pagination");
	  
	  if (total){
	    url.addParam("total",total);
	  }
	  url.addParam("step",'{{$page_step}}');
	  url.addParam("page",page);
	  url.addParam("change_page","showListTransmissions");
	  url.requestUpdate("pagination");

}

function updateFieldsCible(selected) {
  var oForm = document.forms['editTrans'];
  Element.cleanWhitespace(selected);
  if(isNaN(selected.id)){
    $V(oForm.libelle_ATC, selected.id);
  } else {
    $V(oForm.object_id, selected.id);
    $V(oForm.object_class, 'CCategoryPrescription');  
  }
  $('cibleTrans').update(selected.innerHTML.stripTags()).show();
  $V(oForm.cible, '');
}


Main.add(function () {
  var url = new Url("dPprescription", "httpreq_cible_autocomplete");
  url.autoComplete("editTrans_cible", "cible_auto_complete", {
    minChars: 3,
    updateElement: updateFieldsCible
  } );
	if({{$count_trans}} > 0) {
	  showListTransmissions(0, {{$count_trans}});
	}
	
  var options = {
    minHours: '{{$hour-1}}',
    maxHours: '{{$hour+1}}'
  };
	
	var dates = {};
  dates.limit = {
    start: '{{$date}}',
    stop: '{{$date}}'
  };
  Calendar.regField(getForm("editTrans").date, dates, options);
	
	// Initialisation du champ dates
	$("editTrans_date_da").value = "Heure actuelle";
	$V(getForm("editTrans").date, "now");
	
	var oFormTrans = getForm("editTrans");
  new AideSaisie.AutoComplete(oFormTrans.text, {
            objectClass: "CTransmissionMedicale", 
            //contextUserId: "{{$sejour->_ref_praticien->_id}}",
            //contextUserView: "{{$sejour->_ref_praticien->_view}}",
            timestamp: "{{$dPconfig.dPcompteRendu.CCompteRendu.timestamp}}",
						dependField1: oFormTrans.type,
						//dependField2: oFormTrans.degre,
      			validateOnBlur:0
          });
					
					
  var oFormObs = getForm("editObs");
	if(oFormObs){
	  new AideSaisie.AutoComplete(oFormObs.text, {
	            objectClass: "CObservationMedicale", 
	            //contextUserId: "{{$sejour->_ref_praticien->_id}}",
              //contextUserView: "{{$sejour->_ref_praticien->_view}}",
              timestamp: "{{$dPconfig.dPcompteRendu.CCompteRendu.timestamp}}",
	            dependField1: oFormObs.degre,
	            dependField2: '',
	            validateOnBlur:0
	          });
	}
});

</script>

<button class="add" onclick="$('form_trans').toggle(); this.toggleClassName('add').toggleClassName('remove');">Formulaire de transmissions</button>
<table class="form" id="form_trans" {{if !$app->user_prefs.show_transmissions_form}}style="display: none;"{{/if}}>
  <tr>
    <th class="title" style="width: 50%" colspan="4">
      Observations
    </th>
    <th class="title" style="width: 50%" colspan="4">
      Transmissions
    </th>
  </tr>
  <tr>
    <td colspan="4">
      {{if $isPraticien}}
      <form name="editObs" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
	      <input type="hidden" name="dosql" value="do_observation_aed" />
	      <input type="hidden" name="del" value="0" />
	      <input type="hidden" name="m" value="dPhospi" />
	      <input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
	      <input type="hidden" name="user_id" value="{{$user->_id}}" />
	      <input type="hidden" name="date" value="now" /> 
	      
	      {{mb_field object=$observation field="degre"}}
	      <br />
	      {{mb_field object=$observation field="text"}}
	      <button type="button" class="add" onclick="submitSuivi(this.form, '{{$prescription->_id}}')">{{tr}}Add{{/tr}}</button> 
      </form>
      {{/if}}
    </td>     
    <td colspan="4" style="white-space: normal;">
      Cible
      <form name="editTrans" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
	      <input name="cible" type="text" value="" class="autocomplete" />
	      <div style="display:none; width: 350px; white-space: normal;" class="autocomplete" id="cible_auto_complete"></div>
	      <div id="cibleTrans" style="font-style: italic;" onclick="delCibleTransmission();"></div>
	      <input type="hidden" name="dosql" value="do_transmission_aed" />
	      <input type="hidden" name="del" value="0" />
	      <input type="hidden" name="m" value="dPhospi" />
	      <input type="hidden" name="object_class" value="" onchange="$V(this.form.libelle_ATC, '', false);"/>
	      <input type="hidden" name="object_id" value="" />
	      <input type="hidden" name="libelle_ATC" value=""  onchange="$V(this.form.object_class, '', false); $V(this.form.object_id, '', false);"/>
	      <input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
	      <input type="hidden" name="user_id" value="{{$user->_id}}" />

				{{mb_field object=$transmission field="date"}}
        {{mb_field object=$transmission field="degre"}}
        {{mb_field object=$transmission field="type" typeEnum=radio}}
        <button type="button" class="cancel notext" onclick="$V(this.form.type, null);"></button>
        <br />
				{{mb_field object=$transmission field="text"}}<br />
	      <button type="button" class="add" onclick="submitSuivi(this.form, '{{$prescription->_id}}')">{{tr}}Add{{/tr}}</button>
      </form>
    </td>
  </tr>
</table>
<div id="pagination"></div>
{{assign var=start value=0}}
{{assign var=end value=$page_step}}
{{foreach from=$sejour->_ref_suivi_medical name=steps item=_item}}
  {{if $smarty.foreach.steps.index % $page_step == 0}}
    {{assign var=id value=$smarty.foreach.steps.index}}
    <div class="list_trans" id="list_{{$id}}" style="display:none">
	    {{assign var=start value=$smarty.foreach.steps.index}}
	    {{if $start+$end > $count_trans}}
	       {{assign var=end value=$count_trans-$start}}
	    {{/if}}
	    {{assign var=mini_list value=$sejour->_ref_suivi_medical|@array_slice:$start:$end}}
	    {{include file="../../dPhospi/templates/inc_list_transmissions.tpl" without_del_form=false list_transmissions=$mini_list}}
    </div>
  {{/if}}
{{/foreach}}