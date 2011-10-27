<!-- $Id$ -->

<script type="text/javascript">
function showConsultations(oTd, plageconsult_id){
	oTd = $(oTd);

	classparent=null;
	n=0;
	if(oTd.parentNode.parentNode.parentNode.tagName=="TD"){
		 classparent=oTd.parentNode.parentNode.parentNode.className;
		 n=1;
	}
  else{
		classparent=oTd.parentNode.parentNode.className;
		n=2;
	}
	
	if (classparent != "selectedPlage hour_start") {
	    $$('td.selectedPlage.hour_start').each(function(elem){
	            elem.className = "nonEmpty hour_start";
	    });
	
	     if (n == 1) {
		   	oTd.parentNode.parentNode.parentNode.className = "selectedPlage hour_start";
		   }
			 else{
			 	 oTd.parentNode.parentNode.className = "selectedPlage hour_start";
			 }
	      var url = new Url("dPcabinet", "inc_consultation_plage");
	      url.addParam("plageconsult_id", plageconsult_id);
	      url.requestUpdate('consultations');
	}
}
function checkPlage() {
  var form = document.editFrm;
  var timeDebut = form._hour_deb.value + ":" +form._min_deb.value;
  var timeFin   = form._hour_fin.value + ":" +form._min_fin.value;

  if(timeDebut >= timeFin) {
    alert("L'heure de fin doit être supérieure à l'heure de début de la plage de consultation");
    return false;
  }  

  if(!checkForm(form)){
    return false;
  }
  
  if(form.nbaffected.value!= 0 && form.nbaffected.value!=""){
    if(timeDebut > form._firstconsult_time.value || timeFin < form._lastconsult_time.value){
      if(!(confirm("Certaines consultations se trouvent en dehors de la plage de consultation.\n\nVoulez-vous appliquer les modifications ?"))){
        return false;
      }
    }  
  }
    
  return true;
}

function putArrivee(oForm) {
  var today = new Date();
  oForm.arrivee.value = today.toDATETIME(true);
  oForm.submit();
}

function goToDate(oForm, date) {
  $V(oForm.debut, date);
}

function showConsultSiDesistement(){
  var url = new Url("dPcabinet", "vw_list_consult_si_desistement");
  url.addParam("chir_id", '{{$chirSel}}');
  url.pop(500, 500, "test");
}

function printPlage(plage_id) {
    var form = document.paramFrm;
    var url = new Url;
    url.setModuleAction("dPcabinet", "print_plages");
    url.addParam("plage_id", plage_id);
    url.addParam("show_tel", 1);
    url.popup(700, 550, "Planning");
  }

Main.add(function () {
  Calendar.regField(getForm("changeDate").debut, null, {noView: true});
});
</script>

<style type="text/css">
	
</style>

{{mb_script module=dPcabinet script=plage_consultation}}
<table class="main">
  <tr>
    <th style="width: 60%;">
      <form action="?" name="changeDate" method="get">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="tab" value="{{$tab}}" />
        <input type="hidden" name="plageconsult_id" value="0" />
        
        <a href="#1" onclick="$V($(this).getSurroundingForm().debut, '{{$prec}}')">&lt;&lt;&lt;</a>
        
        Semaine du {{$debut|date_format:"%A %d %b %Y"}} au {{$fin|date_format:"%A %d %b %Y"}}
        <input type="hidden" name="debut" class="date" value="{{$debut}}" onchange="this.form.submit()" />
        
        <a href="#1" onclick="$V($(this).getSurroundingForm().debut, '{{$suiv}}')">&gt;&gt;&gt;</a>
        <br />
        <a href="#1" onclick="$V($(this).getSurroundingForm().debut, '{{$today}}')">Aujourd'hui</a>
      </form>
			<br/>
	    <button style="float:left;" class="new" onclick="PlageConsultation.edit('0');">Créer une nouvelle plage</button>
    </th>
    <td style="min-width: 350px;">
      <form action="?" name="selectPrat" method="get">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="tab" value="{{$tab}}" />
        <select name="chirSel" style="width: 15em;" onchange="this.form.submit()">
          <option value="-1" {{if $chirSel == -1}} selected="selected" {{/if}}>&mdash; Choisir un professionnel</option>
          {{foreach from=$listChirs item=curr_chir}}
          <option class="mediuser" style="border-color: #{{$curr_chir->_ref_function->color}};" value="{{$curr_chir->user_id}}" {{if $chirSel == $curr_chir->user_id}} selected="selected" {{/if}}>
            {{$curr_chir->_view}}
          </option>
          {{/foreach}}
        </select>
        
        Cacher les : 
          <label>
            <input type="checkbox" onchange="$V(this.form.hide_payees, this.checked ? 1 : 0); this.form.submit()" {{if $hide_payees}}checked="checked"{{/if}} name="_hide_payees"> payées
            <input type="hidden" name="hide_payees" value="{{$hide_payees}}" />
          </label>
          <label>
            <input type="checkbox" onchange="$V(this.form.hide_annulees, this.checked ? 1 : 0); this.form.submit()" {{if $hide_annulees}}checked="checked"{{/if}} name="_hide_annulees"> annulées
            <input type="hidden" name="hide_annulees" value="{{$hide_annulees}}" />
          </label>
      </form>
      
      <br />
      
      {{if $chirSel && $chirSel != -1}}
        <button type="button" class="lookup" 
                {{if !$count_si_desistement}}disabled="disabled"{{/if}}
                onclick="showConsultSiDesistement()">
          {{tr}}CConsultation-si_desistement{{/tr}} ({{$count_si_desistement}})
        </button>
      {{/if}}
      
      {{if $plageSel->_id}}
        <a class="button new" href="?m={{$m}}&amp;tab=edit_planning&amp;consultation_id=0&amp;plageconsult_id={{$plageSel->_id}}">Planifier une consultation dans cette plage</a>
      {{/if}}
			
    </td>
  </tr>
  <tr>
    <td>
    	
      <table id="weeklyPlanning">
        <tr>
          <!-- Affichage du nom des jours -->
          <th></th>
          {{foreach from=$listDays key=curr_day item=plagesPerDay}}
          <th style="width: {{math equation="100/x" x=$listDays|@count}}%" {{if $smarty.now|date_format:"%A %d" == $curr_day|date_format:"%A %d"}}class="today"{{/if}} scope="col">{{$curr_day|date_format:"%A %d"}}</th>
          {{/foreach}}
        </tr>       
        <!-- foreach sur les heures -->
        {{assign var=hours_start value=$conf.dPcabinet.CPlageconsult.hours_start}}
        {{assign var=hours_stop value=$conf.dPcabinet.CPlageconsult.hours_stop}}
        {{foreach from=$listHours item=curr_hour}}
        <tr>
          <th rowspan="{{$nb_intervals_hour}}" scope="row">{{$curr_hour}}h</th>
          <!-- foreach sur les minutes -->
          {{foreach from=$listMins item=curr_mins key=keyMins}}
            {{if $keyMins}}
              </tr><tr>
            {{/if}}

            {{foreach from=$listDays item=curr_day}}
              {{assign var="keyAff" value="$curr_day $curr_hour:$curr_mins:00"}}
              {{assign var="affichage" value=$affichages.$keyAff}}
             
              {{if $affichage === "empty"}}
                <td class="empty {{if $curr_mins == '00'}}hour_start{{/if}} 
                {{if $curr_hour < $hours_start || $curr_hour > $hours_stop}}opacity-30{{/if}}"></td>
              {{elseif $affichage == "hours"}}
                <td class="empty hour_start" rowspan="{{$nb_intervals_hour}}"></td>
              {{elseif $affichage === "full"}}
              
              {{else}}
                {{assign var="_listPlages" value=$listPlages.$curr_day}}
                {{assign var=plage value=$_listPlages.$affichage}}
              
                <td class="{{if $plageconsult_id == $plage->plageconsult_id}}selectedPlage{{else}}nonEmpty{{/if}} {{if $curr_mins == '00'}}hour_start{{/if}}" rowspan="{{$plage->_nb_intervals}}">
                  <div style="position: relative;">
	                  <div class="toolbar">
											<a class="button list notext" onclick="showConsultations(this,'{{$plage->plageconsult_id}}');" href="#" title="Voir le contenu de la plage"></a>
		                  <a class="button edit notext" href="#" onclick="PlageConsultation.edit('{{$plage->plageconsult_id}}');" title="Modifier cette plage"></a>
		                  <a class="button clock notext" href="?m={{$m}}&amp;tab=edit_planning&amp;consultation_id=0&amp;plageconsult_id={{$plage->plageconsult_id}}"  title="Planifier une consultation dans cette plage"></a>
										</div>
	
	                  <a href="#" onclick="showConsultations(this,'{{$plage->plageconsult_id}}');" title="Voir le contenu de la plage" >
	                    {{if $plage->libelle}}{{$plage->libelle}}<br />{{/if}}
	                    {{$plage->debut|date_format:$conf.time}} - {{$plage->fin|date_format:$conf.time}}
	                  </a>
	                  {{assign var="pct" value=$plage->_fill_rate}}
	                  {{if $pct gt 100}}
	                    {{assign var="pct" value=100}}
	                  {{/if}}
	                  {{if $pct lt 50}}{{assign var="backgroundClass" value="empty"}}
	                  {{elseif $pct lt 90}}{{assign var="backgroundClass" value="normal"}}
	                  {{elseif $pct lt 100}}{{assign var="backgroundClass" value="booked"}}
	                  {{else}}{{assign var="backgroundClass" value="full"}}
	                  {{/if}} 
	                  <a href="?m={{$m}}&amp;tab=edit_planning&amp;consultation_id=0&amp;plageconsult_id={{$plage->plageconsult_id}}" title="Planifier une consultation dans cette plage"> 
	                    <div class="progressBar">
	                      <div class="bar {{$backgroundClass}}" style="width: {{$pct}}%;"></div>
	                      <div class="text">
	                        {{if $plage->locked}}
	                        <img style="float: right; height: 12px;" src="style/mediboard/images/buttons/lock.png" />
	                        {{/if}}
	                        {{$plage->_affected}} {{if $plage->_nb_patients != $plage->_affected}}({{$plage->_nb_patients}}){{/if}} / {{$plage->_total|string_format:"%.0f"}}
	                      </div>
	                    </div>
	                  </a>
									</div>
                </td>
              {{/if}}
            {{/foreach}}
          {{/foreach}}  
        {{/foreach}}
      </table>
      
    <table >
    	<div class="small-info">
    	<strong>L'affichage du semainier a évolué</strong>. Maintenant, vous pouvez utiliser les boutons qui apparaissent au survol de la plage de consultation pour :
		  <ul>
		  <li>
		    <button type="button" class="notext list">Liste</button> :
		    Afficher la liste des patients sur la droite
		  </li>
		  <li>
		    <button type="button" class="notext edit">Edit</button> :
		    Modifier la plage selectionnée
		  </li>
		  <li>
		    <button type="button" class="notext clock">RDV</button> :
		    Prendre un nouveau rendez-vous dans cette plage
		  </li>
		  </ul>
			<br />
		  Les anciennes commandes fonctionnent encore mais seront supprimées prochainement.
		</div>
    </table>
		    <td id="consultations">
		    	{{mb_include module=dPcabinet template=inc_consultations}}
		
		    </td>
		  </tr>
			
		</table>
