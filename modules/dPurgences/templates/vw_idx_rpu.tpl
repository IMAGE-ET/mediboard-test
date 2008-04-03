<script type="text/javascript">

function showLegend() {
  url = new Url;
  url.setModuleAction("dPurgences", "vw_legende");
  url.popup(300, 320, "Legende");
}

// Fonction de refresh du temps d'attente
function updateAttente(sejour_id){
  var url = new Url;
  url.setModuleAction("dPurgences", "httpreq_vw_attente");
  url.addParam("sejour_id", sejour_id);
  url.periodicalUpdate('attente-'+sejour_id, { frequency: 60, waitingText: null });
}


function checkPraticien(oForm){
  var prat = oForm.prat_id.value;
  if(prat == ""){
    alert("Veuillez sélectionner un praticien");
    return false;
  }
  return true;
}

 
function printMainCourante() {
  var url = new Url;
  url.setModuleAction("dPurgences", "print_main_courante");
  url.addParam("date", "{{$date}}");
  url.popup(800, 600, "Impression main courante");
}



function pageMain() {
  regRedirectPopupCal("{{$date}}", "?m={{$m}}&tab={{$tab}}&date=");
}


</script>

<table style="width:100%">
  <tr>
    <td>
      <a class="buttonnew" href="?m=dPurgences&amp;tab=vw_aed_rpu&amp;rpu_id=0">
        Ajouter un patient
      </a> 
    </td>
    <th>
     le
     {{$date|date_format:"%A %d %B %Y"}}
     <img id="changeDate" src="./images/icons/calendar.gif" title="Choisir la date" alt="calendar" />
    </th>
    <td style="text-align: right">
     Type d'affichage
     <form name="selView" action="?m=dPurgences&amp;tab=vw_idx_rpu" method="post">
	      <select name="selAffichage" onchange="submit();">
	        <option value="tous" {{if $selAffichage == "tous"}}selected = "selected"{{/if}}>Tous</option>
	        <option value="prendre_en_charge" {{if $selAffichage == "prendre_en_charge"}} selected = "selected" {{/if}}>A prendre en charge</option>
	      </select>
	    </form>
      <a href="#" onclick="printMainCourante()" class="buttonprint">Main courante</a>
      <a href="#" onclick="showLegend()" class="buttonsearch">Légende</a>
    </td>
  </tr>
</table>

<table class="tbl">
  <tr>
    <th>{{mb_colonne class="CRPU" field="ccmu" order_col=$order_col order_way=$order_way url="?m=$m&amp;tab=vw_idx_rpu"}}</th>
    <th>{{mb_colonne class="CRPU" field="_patient_id" order_col=$order_col order_way=$order_way url="?m=$m&amp;tab=vw_idx_rpu"}}</th>
    <th>{{mb_colonne class="CRPU" field="_entree" order_col=$order_col order_way=$order_way url="?m=$m&amp;tab=vw_idx_rpu"}}</th>
    <th>Temps d'attente</th>
    <th>{{tr}}CRPU-_responsable_id{{/tr}}</th>
    <th>{{tr}}CRPU-diag_infirmier{{/tr}}</th>
    <th>Prise en charge</th>
  </tr>
  {{foreach from=$listSejours item=curr_sejour key=sejour_id}}
  {{assign var=rpu value=$curr_sejour->_ref_rpu}}
  {{assign var=patient value=$curr_sejour->_ref_patient}}
  {{assign var=consult value=$rpu->_ref_consult}}
  {{mb_ternary var=background test=$consult->_id value=#cfc other=none}}
  
  <tr>
    <td class="ccmu-{{$rpu->ccmu}}">
      <a href="?m=dPurgences&amp;tab=vw_aed_rpu&amp;rpu_id={{$rpu->_id}}">
        {{if $rpu->ccmu}}
          {{tr}}CRPU.ccmu.{{$rpu->ccmu}}{{/tr}}
        {{/if}}
      </a>
    </td>

    <td class="text" style="background-color: {{$background}};">
      <a style="float: right;" title="Voir le dossier" href="?m=dPpatients&amp;tab=vw_full_patients&amp;patient_id={{$patient->_id}}&amp;sejour_id={{$sejour_id}}">
        <img src="images/icons/search.png" alt="Dossier patient"/>
      </a>
      <a href="?m=dPurgences&amp;tab=vw_aed_rpu&amp;rpu_id={{$rpu->_id}}">
        <strong>
        {{$patient->_view}}
        </strong>
        {{if $patient->_IPP}}
          [{{$patient->_IPP}}]
        {{/if}}
      </a>
    </td>

    <td class="text" style="background-color: {{$background}};">
      {{if $can->edit}}
      <a style="float: right" title="Modifier le séjour" href="?m=dPplanningOp&amp;tab=vw_edit_sejour&amp;sejour_id={{$sejour_id}}">
        <img src="images/icons/planning.png" alt="Planifier"/>
      </a>
      {{/if}}

      <a href="?m=dPurgences&amp;tab=vw_aed_rpu&amp;rpu_id={{$rpu->_id}}">
        {{$curr_sejour->_entree|date_format:"%d/%m/%Y à %Hh%M"}}
        {{if $curr_sejour->_num_dossier}}
          [{{$curr_sejour->_num_dossier}}]
        {{/if}}
        {{if $rpu->radio_debut && !$rpu->radio_fin}}
        <br />en radiologie
        {{/if}}
      </a>
    </td>
    
    {{if $consult->_id}}
	  <td style="background-color: {{$background}};">
	    <a href="?m=dPurgences&amp;tab=edit_consultation&amp;selConsult={{$consult->_id}}">
	      Consultation à {{$consult->heure|date_format:"%Hh%M"}}
	      {{if $date != $consult->_ref_plageconsult->date}}
	      <br/>le {{$consult->_ref_plageconsult->date|date_format:"%d/%m/%Y"}}
	      {{/if}}
	    </a>
	    ({{$tps_attente.$sejour_id|date_format:"%Hh%M"}})
    </td>
    {{else}}
    <td id="attente-{{$sejour_id}}" style="text-align: center">
      <!-- Affichage du temps d'attente de chaque patient -->
      <script type="text/javascript">
        updateAttente("{{$sejour_id}}");
      </script>
    </td>
    {{/if}}
    
    <td class="text" style="background-color: {{$background}};">
      <a href="?m=dPurgences&amp;tab=vw_aed_rpu&amp;rpu_id={{$rpu->_id}}">
        {{$curr_sejour->_ref_praticien->_view}}
      </a>
    </td>

    <td class="text" style="background-color: {{$background}};">
      <a href="?m=dPurgences&amp;tab=vw_aed_rpu&amp;rpu_id={{$rpu->_id}}">
        {{$rpu->diag_infirmier|nl2br}}
      </a>
    </td>

    <td class="button" style="background-color: {{$background}};">
		  {{include file="inc_pec_praticien.tpl"}}
    </td>
  </tr>
  
  
  {{/foreach}}
</table>
