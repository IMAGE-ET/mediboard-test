<script type="text/javascript">

function pageMain() {
  regRedirectPopupCal("{{$date}}", "?m={{$m}}&tab={{$tab}}&date=");
}

function modeSortieDest(mode_sortie, rpu_id){
  var oFormRPU = document.forms["editRPU-" + rpu_id]; 
  
  // Recuperation du tableau de contrainte modeSortie/Destination en JSON
  var contrainteDestination = {{$contrainteDestination|@json}}
 
  if(mode_sortie == ""){
    $A(oFormRPU.destination).each( function(input) {
      input.disabled = false;
    });
    return;
  }
  
  if(!contrainteDestination[mode_sortie]){
    $A(oFormRPU.destination).each( function(input) {
      input.disabled = true;
    });
    return;
  }
  
  var _contrainteDestination = contrainteDestination[mode_sortie];
  $A(oFormRPU.destination).each( function(input) {
    input.disabled = !_contrainteDestination.include(input.value);
  });
}


function modeSortieOrient(mode_sortie, rpu_id){
  var oFormRPU = document.forms["editRPU-" + rpu_id]; 
  
  // Recuperation du tableau de contrainte modeSortie/Orientation en JSON
  var contrainteOrientation = {{$contrainteOrientation|@json}}
  
  if(mode_sortie == ""){
    $A(oFormRPU.orientation).each( function(input) {
      input.disabled = false;
    });
    return;
  }
  
  if(!contrainteOrientation[mode_sortie]){
    $A(oFormRPU.orientation).each( function(input) {
      input.disabled = true;
    });
    return;
  }
  
  var _contrainteOrientation = contrainteOrientation[mode_sortie];
  $A(oFormRPU.orientation).each( function(input) {
    input.disabled = !_contrainteOrientation.include(input.value);
  });
}


function submitRPU(oForm, action){
  oForm.submit();
  var sejour_id = oForm.sejour_id.value;
  var oFormSejour = document.forms["formSejour-" + sejour_id]; 
  
  if(action == "annuler"){
    // Suppression de l'etablissement_transfert_id du sejour
    oFormSejour.etablissement_transfert_id.value = "";
  }
  
  // Submit en ajax du formulaire de sejour
  submitFormAjax(oFormSejour, 'systemMsg');
}


function submitFormSejour(etablissement_transfert_id, sejour_id){
  var oForm = document.forms["formSejour-" + sejour_id]; 
  oForm.etablissement_transfert_id.value = etablissement_transfert_id;
}


function loadTransfert(mode_sortie, sejour_id){
  // si Transfert, affichage du select
  if(mode_sortie=="7"){
    var url = new Url();
    url.setModuleAction("dPurgences", "httpreq_vw_etab_externes");
    url.requestUpdate('listEtabs-'+sejour_id, { waitingText: null } );
  } else {
    // sinon, on vide le contenu de la div et l'etablissement de transfert du sejour
    $('listEtabs-'+sejour_id).innerHTML = "";
    var oForm = document.forms["formSejour-" + sejour_id]; 
    oForm.etablissement_transfert_id.value = "";
  }
}

</script>


<table style="width:100%">
  <tr>
  <th>
   le
   {{$date|date_format:"%A %d %B %Y"}}
   <img id="changeDate" src="./images/icons/calendar.gif" title="Choisir la date" alt="calendar" />
  </th>
  </tr>
  <tr>
    <td style="text-align: right">
     Type d'affichage
     <form name="selView" action="?m=dPurgences&amp;tab=vw_sortie_rpu" method="post">
	      <select name="selAffichage" onchange="submit();">
	        <option value="tous" {{if $selAffichage == "tous"}}selected = "selected"{{/if}}>Tous</option>
	        <option value="sortie" {{if $selAffichage == "sortie"}} selected = "selected" {{/if}}>Sortie à effectuer</option>
	      </select>
	    </form>
    </td>
  </tr>
</table>

<table class="tbl">
  <tr>
    <th>{{tr}}CRPU-_patient_id{{/tr}}</th>
    <th>{{tr}}CRPU-_responsable_id{{/tr}}</th>
    <th>
    {{mb_colonne class="CRPU" field="_prise_en_charge" order_col=$order_col order_way=$order_way url="?m=$m&amp;tab=vw_sortie_rpu"}}
    </th>
    <th>Sortie</th>
    
  </tr>
  {{foreach from=$listSejours item=curr_sejour}}
  {{assign var="rpu" value=$curr_sejour->_ref_rpu}}
  <tr>
    <td>
      <a href="?m=dPurgences&amp;tab=vw_aed_rpu&amp;rpu_id={{$curr_sejour->_ref_rpu->_id}}">
        <strong>
        {{$curr_sejour->_ref_patient->_view}}
        {{if $curr_sejour->_ref_patient->_IPP}}
          [{{$curr_sejour->_ref_patient->_IPP}}]
        {{/if}}
        </strong>
      </a>
    </td>
    <td>
      <a href="?m=dPurgences&amp;tab=vw_aed_rpu&amp;rpu_id={{$curr_sejour->_ref_rpu->_id}}">
        {{$curr_sejour->_ref_praticien->_view}}
      </a>
    </td>
    <td>
       <a href="?m=dPurgences&amp;tab=edit_consultation&amp;selConsult={{$curr_sejour->_ref_rpu->_ref_consult->_id}}">Voir prise en charge</a><br />
       Praticien: {{$curr_sejour->_ref_rpu->_ref_consult->_ref_plageconsult->_ref_chir->_view}}
    </td>
    <td>
      <form name="editRPU-{{$rpu->_id}}" action="?m=dPurgences" method="post"> 
			  <input type="hidden" name="dosql" value="do_rpu_aed" />
			  <input type="hidden" name="m" value="dPurgences" />
			  <input type="hidden" name="del" value="0" />
			  <input type="hidden" name="rpu_id" value="{{$rpu->_id}}" />
			  
			  <input type="hidden" name="sejour_id" value="{{$rpu->_ref_sejour->_id}}" />
			  
			  <table>
				 <!-- Annulation de la sortie -->
			  {{if $rpu->sortie}}
			   <tr>
			     <td>
			      {{if $curr_sejour->_num_dossier}}
              [{{$curr_sejour->_num_dossier}}]
              <br />
            {{/if}}
			       <input type="hidden" name="mode_sortie" value="" />
			       <input type="hidden" name="destination" value="" />
			       <input type="hidden" name="orientation" value="" />
			       <input type="hidden" name="sortie" value="" />
			       <button class="cancel" type="button" onclick="submitRPU(this.form, 'annuler')">
			         Annuler la sortie
			        </button>
			      </td>
			    </tr>
			  <!-- Sortie à effectuer -->
			  {{else}}
			   <tr>
			     <td class="text">
			      {{if $curr_sejour->_num_dossier}}
              [{{$curr_sejour->_num_dossier}}]
              <br />
            {{/if}}
            {{assign var="sejour_id" value=$rpu->_ref_sejour->_id}}
			      {{mb_field object=$rpu field="mode_sortie" defaultOption="&mdash; Mode de sortie" onchange="this.form.destination.value = ''; this.form.orientation.value = ''; modeSortieDest(this.value, this.form.rpu_id.value); modeSortieOrient(this.value, this.form.rpu_id.value); loadTransfert(this.value, $sejour_id);"}}
			      {{mb_field object=$rpu field="destination" defaultOption="&mdash; Destination"}} 
			      {{mb_field object=$rpu field="orientation" defaultOption="&mdash; Orientation"}}
			      <input type="hidden" name="sortie" value="current" />
			      <button class="tick" type="button" onclick="submitRPU(this.form, 'effectuer');">
			        Effectuer la sortie
			      </button>
			     </td>
			   </tr>
			   <tr>
			    <td>
			     <div id="listEtabs-{{$rpu->_ref_sejour->_id}}">
	           {{if $rpu->mode_sortie == "7"}}
	             {{assign var="_transfert_id" value=$rpu->_ref_sejour->etablissement_transfert_id}}
	             {{assign var="modeSortieRPU" value="1"}}
	             {{include file="../../dPurgences/templates/inc_vw_etab_externes.tpl"}}
	           {{/if}}
	         </div>
	        </td>
			   </tr>
			  {{/if}}
			  </table>
			</form>
    </td>
  </tr>
  {{/foreach}}
</table>


{{foreach from=$listSejours item=curr_sejour}}
	{{assign var="rpu" value=$curr_sejour->_ref_rpu}} 
	<table>
	  <tr>
	    <td>
			 <!-- Formulaire permettant de sauvegarder l'etablissement de transfert du RPU -->
	     <form name="formSejour-{{$rpu->_ref_sejour->_id}}" action="?" method="post">
	       <input type="hidden" name="dosql" value="do_sejour_aed" />
	       <input type="hidden" name="m" value="dPplanningOp" />
	       <input type="hidden" name="del" value="0" />
	       <input type="hidden" name="sejour_id" value="{{$rpu->_ref_sejour->_id}}" />
	       <input type="hidden" name="etablissement_transfert_id" value="" />
	       </form>
		  </td>
	  </tr>
	</table>
{{/foreach}}


<script type="text/javascript">

{{foreach from=$listSejours item=curr_sejour}}
  {{assign var="rpu" value=$curr_sejour->_ref_rpu}}
  
  {{if $rpu->mode_sortie}}
    modeSortieDest("{{$rpu->mode_sortie}}", "{{$rpu->_id}}");
    modeSortieOrient("{{$rpu->mode_sortie}}", "{{$rpu->_id}}");
  {{/if}}
{{/foreach}}
</script>