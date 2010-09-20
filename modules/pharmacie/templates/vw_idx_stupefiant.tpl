<script type="text/javascript">

Main.add(function(){
  new Control.Tabs.create("stupefiants");
  oFilterForm = getForm("filterForm");
	updateVisibleList();
});

updateListPrescriptions = function(print){
	var url = new Url("pharmacie", "ajax_vw_list_prescriptions_stup");
	url.addParam("date_min", $V(oFilterForm._date_entree));
	url.addParam("date_max", $V(oFilterForm._date_sortie));
	url.addParam("print", print);
	if(print){
	  url.popup(600, 600, "Prescriptions de stupéfiants");
	} else {
	  url.requestUpdate("prescriptions");
	}
}

updateListAdministrations = function(print){
  var url = new Url("pharmacie", "ajax_vw_list_administrations_stup");
  url.addParam("date_min", $V(oFilterForm._date_entree));
  url.addParam("date_max", $V(oFilterForm._date_sortie));
  url.addParam("print", print);
	if(print){
	  url.popup(600, 600, "Administrations de stupéfiants");
	} else {
    url.requestUpdate("administrations");
	}
}

updateVisibleList = function(){
  if($('prescriptions').visible()){
	  updateListPrescriptions();
  }
	if($('administrations').visible()){
    updateListAdministrations();
	}
}

function viewDossierSoin(sejour_id, date){
  var oForm = document.viewSoin;
  $V(oForm.sejour_id, sejour_id);
	$V(oForm.date, date);
  oForm.submit();
}

</script>

{{mb_include_script module="dPprescription" script="prescription"}}

<form name="viewSoin" method="get" action="?">
  <input type="hidden" name="m" value="soins" />
  <input type="hidden" name="tab" value="vw_idx_sejour" />
  <input type="hidden" name="sejour_id" value="" />
  <input type="hidden" name="date" value="{{$filter_sejour->_date_entree}}" />
  <input type="hidden" name="mode" value="1" />
  <input type="hidden" name="_active_tab" value="dossier_soins" /> 
</form>

<form name="filterForm" action="?">
	<table>
	  <tr>
		  <th>A partir du</th>
		  <td>  
		    {{mb_field object=$filter_sejour field="_date_entree" form=filterForm canNull=false register=true onchange="updateVisibleList();"}}
		  </td>
		  <th>Jusqu'au</th>
		  <td>
		    {{mb_field object=$filter_sejour field="_date_sortie" form=filterForm canNull=false register=true onchange="updateVisibleList();"}}
		  </td>
	  </tr>
  </table>
</form>

<ul id="stupefiants" class="control_tabs">
	<li onmousedown="updateListPrescriptions();">
		<a href="#prescriptions">
			Prescriptions <button type="button" class="print notext" onmousedown="if($('prescriptions').visible()){ Event.stop(event);}  updateListPrescriptions(true);">{{tr}}Print{{/tr}}</button>
	  </a>
	</li>
	<li onmousedown="updateListAdministrations();">
		<a href="#administrations">
			Administrations <button type="button" class="print notext" onmousedown="if($('administrations').visible()) { Event.stop(event);} updateListAdministrations(true);">{{tr}}Print{{/tr}}</button>
		</a>
	</li>
</ul>

<hr class="control_tabs" />

<div id="prescriptions"></div>
<div id="administrations"></div>