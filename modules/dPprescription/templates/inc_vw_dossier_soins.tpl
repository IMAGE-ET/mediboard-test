<script type="text/javascript">

refreshDossierSoin = function(){
  loadTraitement('{{$sejour->_id}}','{{$date}}',document.click.nb_decalage.value);
}
printDossierSoin = function(prescription_id, date){
  url = new Url;
  url.setModuleAction("dPprescription", "vw_plan_soin_pdf");
  url.addParam("prescription_id", prescription_id);
  url.popup(900, 600, "Plan de soin");
}

addCibleTransmission = function(object_class, object_id, view) {
  oDiv = $('cibleTrans');
  if(!oDiv) {
    return;
  }
  oForm = document.forms['editTrans'];
  $V(oForm.object_class, object_class);
  $V(oForm.object_id, object_id);
  oDiv.innerHTML = view;
  oForm.text.focus();
}

addAdministration = function(line_id, quantite, key_tab, object_class, date, heure, administrations) {
  var url = new Url;
  url.setModuleAction("dPprescription", "httpreq_add_administration");
  url.addParam("line_id",  line_id);
  url.addParam("quantite", quantite);
  url.addParam("key_tab", key_tab);
  url.addParam("object_class", object_class);
  url.addParam("date", date);
  url.addParam("heure", heure);
  url.addParam("administrations", administrations);
  url.addParam("date_sel", "{{$date}}");
  url.popup(500,400,"Administration");
}

toggleSelectForAdministration = function (element, line_id, quantite, key_tab, object_class, date, heure, administrations) {
  element = $(element);
  if (element._administration) {
    element.removeClassName('administration-selected');
    element._administration = null;
  }
  else {
    element.addClassName('administration-selected');
    element._administration = {
      line_id: line_id,
      quantite: quantite,
      key_tab: key_tab,
      object_class: object_class,
      date: date,
      heure: heure/*,
      administrations: administrations*/,
      date_sel: '{{$date}}'
    };
  }
}

applyAdministrations = function () {
  var administrations = {};
  $('plan_soin').select('div.administration-selected').each(function(element) {
    var adm = element._administration;
    administrations[adm.line_id+'_'+adm.key_tab+'_'+adm.date+'_'+adm.heure] = adm;
  });
  var url = new Url;
  url.setModuleAction("dPprescription", "httpreq_add_multiple_administrations");
  url.addObjectParam("adm", administrations);
  url.popup(700, 600, "Administrations multiples");
}

viewLegend = function(){
  var url = new Url;
  url.setModuleAction("dPhospi", "vw_lengende_dossier_soin");
  url.popup(300,150, "Légende");
}

viewDossier = function(prescription_id){
  var url = new Url;
  url.setModuleAction("dPprescription", "vw_dossier_cloture");
  url.addParam("prescription_id", prescription_id);
  url.popup(500,500,"Dossier cloturé");
}

calculSoinSemaine = function(date, prescription_id){
  var url = new Url;
  url.setModuleAction("dPprescription", "httpreq_vw_dossier_soin_semaine");
  url.addParam("date", date);
  url.addParam("prescription_id", prescription_id);
  url.requestUpdate("semaine", { waitingText: null } );
}

// Initialisation
var planSoin;
var th_before, th_today, th_after;
var listTdBefore, listTdToday, listTdAfter;
var reverseBefore, reverseToday, reverseAfter;

window.periodicalBefore = null;
window.periodicalAfter = null;

Main.add(function () {

{{if $mode_bloc}}
  loadSuivi('{{$sejour->_id}}');
{{/if}}

  planSoin = $('plan_soin');
  
  if (planSoin) {
    // Initialisation
    th_before = $("th-{{$hier}}");
    th_today = $("th-{{$date}}");
    th_after = $("th-{{$demain}}");
  
    listTdBefore = planSoin.select('td.{{$hier}}');
    listTdToday  = planSoin.select('td.{{$date}}');
    listTdAfter  = planSoin.select('td.{{$demain}}');
    
    reverseBefore = listTdBefore.reverse(false);
    reverseToday  = listTdToday.reverse(false);
    reverseAfter  = listTdAfter.reverse(false);
  
    listThHoursBefore = planSoin.select('.th_hours_{{$hier}}');
    listThHoursAfter = planSoin.select('.th_hours_{{$demain}}');
  
    if(th_before && th_after) {
    	th_before.hide();
    	th_after.hide();
    	
    	th_before.colSpan = 0;
    	th_today.colSpan = 12;
    	th_after.colSpan = 0;
    	  
    	  
    	listTdBefore.each(function(elt) { 
    	  elt.hide();
    	  $(elt.className).hide();
    	});
    	if(listTdBefore.length == 0){
        // On masque les heures
        listThHoursBefore.each(function(elt) { 
          elt.hide();
        });
    	}
    	  
    	listTdAfter.each(function(elt) { 
    	  elt.hide();
    	  $(elt.className).hide();
    	});
    	if(listTdAfter.length == 0){
        // On masque les heures
        listThHoursAfter.each(function(elt) { 
          elt.hide();
        });
    	}
    }
  }
  {{if !$mode_bloc}}
    new Control.Tabs('tab_dossier_soin');
  {{/if}}
  var tabs = Control.Tabs.create('tab_categories', true);
});

var oFormClick = document.click;
  
showHideElement = function(list_show, list_hide, th1, th2, signe){
  var modif = "0";
  var class_after = "";
  var class_before = "";
  
  if (list_show) {
    list_show.each(function(elt) { 
      // si l'element n'est pas visible, on sauvegarde sa classe _class
      if(!elt.visible() && (class_after == "" || class_after == elt.className)){
        modif = "1";
        elt.show();
        class_after = elt.className;
        if(!$(elt.className).visible()){
          signe == "-" ? oFormClick.nb_decalage.value-- : oFormClick.nb_decalage.value++;
          $(elt.className).show();
        }
      }
    });
  }
  
  if (list_hide) {
    // On affiche le premier element qui est caché dans listTdAfter
    list_hide.each(function(elt) { 
      // si l'element n'est pas visible, on sauvegarde sa classe _class
      if(elt.visible() && (class_before == "" || class_before == elt.className) && class_after != ""){
        elt.hide();
        class_before = elt.className;
        if($(elt.className).visible()){
          $(elt.className).hide();
        }
      }
    });
  }
  if(modif == 1){
    th1.colSpan++;
    th1.colSpan > 0 ? th1.show() : th1.hide();
    th2.colSpan--;
    th2.colSpan > 0 ? th2.show() : th2.hide();
  }
}

showAfter = function(){
  if(th_before && th_before.colSpan == 0){
    showHideElement(listTdAfter, listTdToday, th_after, th_today, "+");
  } else {
    showHideElement(listTdToday, listTdBefore, th_today, th_before, "+");
  }
}

showBefore = function(){
  if(th_after && th_after.colSpan == 0){
    showHideElement(reverseBefore, reverseToday, th_before, th_today, "-");
  } else {
    showHideElement(reverseToday, reverseAfter, th_today, th_after, "-");
  }
}

// Décalage
{{if $signe_decalage == "+"}}
  for(i=0; i< {{$nb_decalage}}; i++){
    showAfter();
  }
{{else}}
  for(i=0; i< {{$nb_decalage}}; i++){
    showBefore();
  }
{{/if}}

</script>

<form name="click">
  <input type="hidden" name="nb_decalage" value="0" />
</form>

<table class="tbl">
  <tr>
    <th colspan="10" class="title">{{$sejour->_view}} (Dr {{$sejour->_ref_praticien->_view}})</th>
  </tr>
  <tr>
    <td style="width: 25%;">
      {{mb_title object=$patient->_ref_constantes_medicales field=poids}}:
      {{if $patient->_ref_constantes_medicales->poids}}
        {{mb_value object=$patient->_ref_constantes_medicales field=poids}} kg
      {{else}}??{{/if}}
    </td>
    <td style="width: 25%;">
      {{mb_title object=$patient field=naissance}}: 
      {{mb_value object=$patient field=naissance}} ({{$patient->_age}} ans)
    </td>
    <td style="width: 25%;">
      {{mb_title object=$patient->_ref_constantes_medicales field=taille}}:
      {{if $patient->_ref_constantes_medicales->taille}}
        {{mb_value object=$patient->_ref_constantes_medicales field=taille}} cm
      {{else}}??{{/if}}
    </td>
    <td style="width: 25%;">
      {{mb_title object=$patient->_ref_constantes_medicales field=_imc}}:
      {{if $patient->_ref_constantes_medicales->_imc}}
        {{mb_value object=$patient->_ref_constantes_medicales field=_imc}}
      {{else}}??{{/if}}
    </td>
  </tr>
</table>

{{if !$mode_bloc}}
	<ul id="tab_dossier_soin" class="control_tabs">
	  <li onclick="loadTraitement('{{$sejour->_id}}','{{$date}}');"><a href="#jour">Administration</a></li>
	  <li onclick="calculSoinSemaine('{{$date}}','{{$prescription_id}}');"><a href="#semaine">Plan</a></li>
	</ul>
  <hr class="control_tabs" />
{{/if}}

<div id="jour" {{if !$mode_bloc}}style="display:none"{{/if}}>

{{if $prescription_id}}
  {{if !$mode_bloc}}
  <button type="button" class="search" style="float: right" onclick="viewDossier('{{$prescription_id}}');">Dossier cloturé</button>
	<h2 style="text-align: center">
	    <a href="#1" onclick="showBefore()" onmousedown="periodicalBefore = new PeriodicalExecuter(showBefore, 0.2);" onmouseup="periodicalBefore.stop();">
        <img src="images/icons/prev.png" alt="&lt;"/>
      </a>
	    Dossier de soin du {{$date|@date_format:"%d/%m/%Y"}}
      <a href="#1" onclick="showAfter()" onmousedown="periodicalAfter = new PeriodicalExecuter(showAfter, 0.2);" onmouseup="periodicalAfter.stop();">
	      <img src="images/icons/next.png" alt="&gt;" />
      </a>
	 </h2>
	 {{/if}}
	<table style="width: 100%">
	  <tr>
	    <td>
	    {{if !$mode_bloc}}
	      <button type="button" class="print" onclick="printDossierSoin('{{$prescription_id}}','{{$date}}');" title="{{tr}}Print{{/tr}}">
		      Imprimer la feuille de soins immédiate
	      </button>
	    {{/if}}
        <button type="button" class="tick" onclick="applyAdministrations();">
          Appliquer les administrations séléctionnées
        </button>
	    </td>
	    <td style="text-align: right">
	      <button type="button" class="search" onclick="viewLegend()">Légende</button>
	    </td>
	  </tr>
	</table>

	{{assign var=transmissions value=$prescription->_transmissions}}	  

  <table style="width: 100%">
	  <tr>
	    <td style="width: 1%">
		 	 <table>
			 	 <tr>
					 <td>
					   <ul id="tab_categories" class="control_tabs_vertical">
						    {{if $prescription->_ref_lines_med_for_plan|@count}}
						      <li><a href="#_med">Médicaments</a></li>
						    {{/if}}
							  {{assign var=specs_chapitre value=$categorie->_specs.chapitre}}
							  {{foreach from=$specs_chapitre->_list item=_chapitre}}
							    {{if @is_array($prescription->_ref_lines_elt_for_plan.$_chapitre)}}
							    <li><a href="#_cat-{{$_chapitre}}">{{tr}}CCategoryPrescription.chapitre.{{$_chapitre}}{{/tr}}</a></li>
							    {{/if}}
							  {{/foreach}}
							</ul>	
				 	 	</td>
			 	 	</tr>
		 	 	</table>
		 	 	</td>
		 	 	<td>
				<table class="tbl" id="plan_soin">
				  {{if $prescription->_ref_lines_med_for_plan|@count || $prescription->_ref_lines_elt_for_plan|@count}}
					  <tr>
					    <th rowspan="2">Catégorie</th>
					    <th rowspan="2">Cond.</th>
					    <th rowspan="2">Libellé</th>
					    <th rowspan="2">Posologie</th>
					    {{foreach from=$tabHours key=_date item=_hours_by_date}}
					     <th id="th-{{$_date}}">{{$_date|date_format:"%d/%m"}}</th>
					    {{/foreach}}
					    <th colspan="2">Sign.</th>
					  </tr>
					  <tr>
					    {{foreach from=$tabHours key=_date item=_hours_by_date}}
				          {{foreach from=$_hours_by_date item=_hour}}
				          <th id="{{$_date}} {{$_hour}}:00:00" class="th_hours_{{$_date}}" 
				              style='text-align: center; 
				              {{if array_key_exists("$_date $_hour:00:00", $operations)}}border-right: 3px solid black;{{/if}}'>
				              {{$_hour}}h
					             {{if array_key_exists("$_date $_hour:00:00", $operations)}}
					                {{assign var=_hour_op value="$_date $_hour:00:00"}}
					                <a style="color: white; font-weight: bold; font-style: normal;" href="#" title="Intervention à {{$operations.$_hour_op|date_format:'%Hh%M'}}">Interv.</a>
					              {{/if}}
				              </th>   
						  {{/foreach}} 
					    {{/foreach}}
					    <th>Dr</th>
					    <th>Ph</th>
					  </tr>
			    {{/if}}
			
				  <!-- Affichage des medicaments -->
				  <tbody id="_med" style="display: none;">
				    {{foreach from=$prescription->_ref_lines_med_for_plan item=_cat_ATC key=_key_cat_ATC name="foreach_cat"}}
				      {{foreach from=$_cat_ATC item=_line name="foreach_med"}}
				        {{foreach from=$_line key=unite_prise item=line_med name="foreach_line"}} 
					        {{include file="../../dPprescription/templates/inc_vw_line_dossier_soin.tpl" 
					            line=$line_med
					            nodebug=true
					            first_foreach=foreach_med
					            last_foreach=foreach_line
					            nb_line=$_line|@count
					            dosql=do_prescription_line_medicament_aed}}
					      {{/foreach}}
					    {{/foreach}} 		 
				    {{/foreach}}
			    </tbody>
					
				  <!-- Affichage des elements -->
				  {{foreach from=$prescription->_ref_lines_elt_for_plan key=name_chap item=elements_chap name="foreach_element"}}
				       {{if !$smarty.foreach.foreach_element.first}}
				         </tbody>
				       {{/if}}
					     <tbody id="_cat-{{$name_chap}}" style="display: none;">  
							    {{foreach from=$elements_chap key=name_cat item=elements_cat}}
							      {{assign var=categorie value=$categories.$name_chap.$name_cat}}
							      {{foreach from=$elements_cat item=_element name="foreach_cat"}}
							        {{foreach from=$_element key=unite_prise item=element name="foreach_elt"}} 	          
							          {{include file="../../dPprescription/templates/inc_vw_line_dossier_soin.tpl" 
							                    line=$element
							                    nodebug=true
							                    first_foreach=foreach_cat
							                    last_foreach=foreach_elt
							                    nb_line=$_element|@count
							                    dosql=do_prescription_line_element_aed}}
							   {{/foreach}}
							  {{/foreach}}
							{{/foreach}}
						{{/foreach}}
					 </tbody>
				</table>
	    </td>
	  </tr>
  </table>
{{else}}
  <div class="big-info">
	Ce dossier ne possède pas de prescription de séjour
  </div>
{{/if}}
</div>

{{if $mode_bloc}}
  <!-- Affichage des transmissions dans le cas de l'affichage au bloc -->
  <div id="dossier_suivi"></div>
{{else}}
  <div id="semaine" style="display:none"></div>
{{/if}}