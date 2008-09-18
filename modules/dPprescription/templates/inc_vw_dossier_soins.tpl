<script type="text/javascript">

refreshDossierSoin = function(){
  loadTraitement('{{$sejour->_id}}','{{$real_date}}',document.click.nb_decalage.value);
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

addAdministration = function(line_id, quantite, key_tab, object_class, date, heure, administrations){
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
  url.popup(400,300,"Administration");
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
var th_before = $("th-{{$hier}}");
var th_today = $("th-{{$date}}");
var th_after = $("th-{{$demain}}");


var listTdBefore = $$('td.{{$hier}}');
var listTdToday  = $$('td.{{$date}}');
var listTdAfter  = $$('td.{{$demain}}');
 
var reverseBefore = $$('td.{{$hier}}').reverse("false");
var reverseToday = $$('td.{{$date}}').reverse("false");
var reverseAfter = $$('td.{{$demain}}').reverse("false");

window.periodicalBefore = null;
window.periodicalAfter = null;

Main.add(function () {
  listThHoursBefore = $$('.th_hours_{{$hier}}');
  listThHoursAfter = $$('.th_hours_{{$demain}}');
  
  if(th_before && th_after){  
	th_before.hide();
	th_after.hide();
	
	th_before.colSpan = 0;
	th_today.colSpan = 12;
	th_after.colSpan = 0;
	  
	  
	listTdBefore.each(function(elt) { 
	  elt.hide();
	  $(elt.className).hide();
	});
	if(listTdBefore.size() == 0){
	 // On masque les heures
	 listThHoursBefore.each(function(elt) { 
	   elt.hide();
	 });
	}
	  
	  
	listTdAfter.each(function(elt) { 
	  elt.hide();
	  $(elt.className).hide();
	});
	if(listTdAfter.size() == 0){
	 // On masque les heures
	 listThHoursAfter.each(function(elt) { 
	   elt.hide();
	  });
	}
  }
  
  
  new Control.Tabs('tab_dossier_soin');
});

var oFormClick = document.click;
  
showHideElement = function(list_show, list_hide, th1, th2, signe){
  var modif = "0";
  var class_after = "";
  var class_before = "";
  
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

<ul id="tab_dossier_soin" class="control_tabs">
  <li><a href="#jour">Administration</a></li>
  <li onclick="calculSoinSemaine('{{$date}}','{{$prescription_id}}');"><a href="#semaine">Plan</a></li>
</ul>
<hr class="control_tabs" />

<div id="jour" style="display:none">

<table class="tbl">
  <tr>
    <th colspan="3" class="title">{{$sejour->_view}} (Dr {{$sejour->_ref_praticien->_view}})</th>
  </tr>
  <tr>
    <td>Poids: {{$poids}} kg</td>
    <td>Age: {{$patient->_age}}</td>
    <td>Taille: {{$patient->_ref_constantes_medicales->taille}}</td>
  </tr>
</table>

{{if $prescription_id}}
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
	<table style="width: 100%">
	  <tr>
	    <td>
	      <button type="button" class="print" onclick="printDossierSoin('{{$prescription_id}}','{{$date}}');" title="{{tr}}Print{{/tr}}">
		      Imprimer la feuille de soins immédiate
	      </button>
	    </td>
	    <td style="text-align: right">
	      <button type="button" class="search" onclick="viewLegend()">Légende</button>
	    </td>
	  </tr>
	</table>
	

	<table class="tbl">
	  <tr>
	    <th rowspan="2">Type</th>
	    <th rowspan="2">Conditionnelle</th>
	    <th rowspan="2">Libelle</th>
	    <th rowspan="2">Posologie</th>
	    {{foreach from=$tabHours key=_date item=_hours_by_date}}
	     <th id="th-{{$_date}}">{{$_date|date_format:"%d/%m"}}</th>
	    {{/foreach}}
	    <th rowspan="2" colspan="2">Signatures<br /> Prat. / Pharm.</th>
	  </tr>
	  <tr>
	    {{foreach from=$tabHours key=_date item=_hours_by_date}}
          {{foreach from=$_hours_by_date item=_hour}}
		    <th id="{{$_date}} {{$_hour}}:00:00" class="th_hours_{{$_date}}">{{$_hour}}h</th>          
		  {{/foreach}} 
	    {{/foreach}}
	  </tr>
	  
	  {{assign var=administrations value=$prescription->_administrations}}
	  {{assign var=transmissions value=$prescription->_transmissions}}	  
	  {{assign var=prises value=$prescription->_prises}}
	  {{assign var=list_prises value=$prescription->_list_prises}}
	  {{assign var=lines value=$prescription->_lines}}
		
		
	  <!-- Affichage des medicaments -->
	  {{foreach from=$lines.med item=_line name="foreach_med"}}
	    {{foreach from=$_line key=unite_prise item=line_med name="foreach_line"}} 
		  {{include file="../../dPprescription/templates/inc_vw_line_dossier_soin.tpl" 
		            line=$line_med
		            nodebug=true
		            first_foreach=foreach_med
		            last_foreach=foreach_line
		            type=med
		            suffixe=med
		            nb_line=$_line|@count
		            dosql=do_prescription_line_medicament_aed}}	         
		{{/foreach}} 		 
	  {{/foreach}}

		
	  <!-- Affichage des elements -->
	  {{foreach from=$lines.elt key=name_chap item=elements_chap}}
	    {{foreach from=$elements_chap key=name_cat item=elements_cat}}
	      {{assign var=categorie value=$categories.$name_chap.$name_cat}}
	      {{foreach from=$elements_cat item=_element name="foreach_cat"}}
	        {{foreach from=$_element key=unite_prise item=element name="foreach_elt"}}   
	          {{include file="../../dPprescription/templates/inc_vw_line_dossier_soin.tpl" 
	                    line=$element
	                    nodebug=true
	                    first_foreach=foreach_cat
	                    last_foreach=foreach_elt
	                    type=$name_cat
	                    suffixe=elt
	                    nb_line=$_element|@count
	                    dosql=do_prescription_line_element_aed}}
	    	{{/foreach}}
	      {{/foreach}}
	    {{/foreach}}
	  {{/foreach}}
	</table>

	
{{else}}
  <div class="big-info">
	Ce dossier ne possède pas de prescription de séjour
  </div>
{{/if}}
</div>
<div id="semaine" style="display:none"></div>