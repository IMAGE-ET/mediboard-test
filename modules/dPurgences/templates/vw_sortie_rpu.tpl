<script type="text/javascript">

Main.add(function () {
  Calendar.regRedirectPopup("{{$date}}", "?m={{$m}}&tab={{$tab}}&date=");
});

function modeSortieDest(mode_sortie, rpu_id) {
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


function loadTransfert(mode_sortie, sejour_id){
  if(mode_sortie=="transfert"){
    var url = new Url();
    url.setModuleAction("dPurgences", "httpreq_vw_etab_externes");
    url.requestUpdate('listEtabs-'+sejour_id, { waitingText: null } );
  } else {
    // sinon, on vide le contenu de la div et l'etablissement de transfert du sejour
    $('listEtabs-'+sejour_id).innerHTML = "";
  }
}

function initFields(rpu_id,sejour_id, mode_sortie){
  var oForm = document.forms['editRPU-'+rpu_id];
  oForm.destination.value = '';
  oForm.orientation.value = ''; 
  modeSortieDest(mode_sortie, rpu_id); 
  modeSortieOrient(mode_sortie, rpu_id); 
  loadTransfert(mode_sortie, sejour_id);
}

// Fonction appelée dans inc_vw_etab_externe qui submit le sejour dans le cas de "inc_vw_rpu.tpl"
// Dans la sortie, on ne veut pas déclencher de submit
function submitSejour(){
 // Ne rien faire
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
    {{mb_colonne class="CRPU" field="_pec_transport" order_col=$order_col order_way=$order_way url="?m=$m&amp;tab=vw_sortie_rpu"}}
    </th>
    <th>RPU</th>
    <th>Séjour</th>
  </tr>
  {{foreach from=$listSejours item=sejour}}
  {{assign var=rpu value=$sejour->_ref_rpu}}
  {{assign var=patient value=$sejour->_ref_patient}}
  <tr>
    <td {{if $sejour->annule}}class="cancelled"{{/if}}>
		  <a class="action" style="float: right;" title="Modifier le dossier administratif" href="?m=dPpatients&amp;tab=vw_edit_patients&amp;patient_id={{$patient->_id}}">
	        <img src="images/icons/edit.png" alt="modifier" />
	 	  </a>
 	  
      <a href="?m=dPurgences&amp;tab=vw_aed_rpu&amp;rpu_id={{$rpu->_id}}">
        {{if $patient->_IPP}}
          [{{$patient->_IPP}}]<br />
        {{/if}}
        <strong>{{$patient->_view}}</strong>
      </a>
    </td>
    
    {{if $sejour->annule}}
    <td class="cancelled" colspan="5">
      {{if $rpu->mutation_sejour_id}}
      Hospitalisation
      <a href="?m=dPplanningOp&tab=vw_edit_sejour&sejour_id={{$rpu->mutation_sejour_id}}">
        dossier [{{$rpu->_ref_sejour_mutation->_num_dossier}}]
     	</a> 
      {{else}}
      {{tr}}Cancelled{{/tr}}
      {{/if}}
    </td>
    {{else}}
    <td>
      <a href="?m=dPurgences&amp;tab=vw_aed_rpu&amp;rpu_id={{$rpu->_id}}">
        {{$sejour->_ref_praticien->_view}}
      </a>
    </td>
    
    <td class="button">
       par {{$rpu->_ref_consult->_ref_plageconsult->_ref_chir->_view}}
       <a href="?m=dPurgences&amp;tab=edit_consultation&amp;selConsult={{$rpu->_ref_consult->_id}}">Voir prise en charge</a>
    </td>
    
    <td>
      <!-- Vérification des champs semi obligatoires -->
      {{if !$rpu->ccmu           }}<div class="warning">Champ manquant {{mb_label object=$rpu field=ccmu           }}</div>{{/if}}
      {{if !$rpu->gemsa          }}<div class="warning">Champ manquant {{mb_label object=$rpu field=gemsa          }}</div>{{/if}}

	    {{if $dPconfig.dPurgences.old_rpu == "1"}}
      {{if !$rpu->type_pathologie}}<div class="warning">Champ manquant {{mb_label object=$rpu field=type_pathologie}}</div>{{/if}}
      {{if !$rpu->urtrau         }}<div class="warning">Champ manquant {{mb_label object=$rpu field=urtrau         }}</div>{{/if}}
      {{if !$rpu->urmuta         }}<div class="warning">Champ manquant {{mb_label object=$rpu field=urmuta         }}</div>{{/if}}
			{{/if}}
			      
      {{if $sejour->sortie_reelle}}
         {{if $rpu->destination}}
           <strong>{{tr}}CRPU-destination{{/tr}}:</strong>
           {{mb_value object=$rpu field="destination"}} <br />
				 {{/if}}
				 {{if $rpu->orientation}}
				   <strong>{{tr}}CRPU-orientation{{/tr}}:</strong>
				   {{mb_value object=$rpu field="orientation"}}	     
         {{/if}}
      {{else}}
	      <form name="editRPU-{{$rpu->_id}}" method="post" action="?">
	        <input type="hidden" name="m" value="dPurgences" />
	        <input type="hidden" name="dosql" value="do_rpu_aed" />
	        <input type="hidden" name="del" value="0" />
	        <input type="hidden" name="rpu_id" value="{{$rpu->_id}}" />
	        
	        {{mb_field object=$rpu field="destination" defaultOption="&mdash; Destination" onchange="submitFormAjax(this.form, 'systemMsg');"}}<br />
				  {{mb_field object=$rpu field="orientation" defaultOption="&mdash; Orientation" onchange="submitFormAjax(this.form, 'systemMsg');"}}
	      </form>
      {{/if}}
    </td>
    
    <td>
      {{if $can->edit}}
      <a style="float: right" title="Modifier le séjour" href="?m=dPplanningOp&amp;tab=vw_edit_sejour&amp;sejour_id={{$sejour->_id}}">
        <img src="images/icons/planning.png" alt="Planifier"/>
      </a>
      {{/if}}
    
      <form name="editSejour-{{$sejour->_id}}" action="?m=dPurgences" method="post"> 
			  <input type="hidden" name="dosql" value="do_sejour_aed" />
			  <input type="hidden" name="m" value="dPplanningOp" />
			  <input type="hidden" name="del" value="0" />
			  <input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
			  
			  <table>
				<!-- Annulation de la sortie -->
			  {{if $sejour->sortie_reelle}}
		    <tr>
		      <td>
		        {{if $sejour->_num_dossier}}[{{$sejour->_num_dossier}}]{{/if}}
            {{if $sejour->mode_sortie}}
              {{mb_value object=$sejour field=mode_sortie}}
              {{if $sejour->mode_sortie == "transfert" && $sejour->etablissement_transfert_id}}
                {{assign var=etab_externe_id value=$sejour->etablissement_transfert_id}}
                {{assign var=etab_externe value=$listEtab.$etab_externe_id}}
                <br />vers {{$etab_externe->_view}}<br />
              {{/if}}
            {{/if}}
            {{mb_value object=$sejour field=sortie_reelle}}<br />
		        
		        <input type="hidden" name="mode_sortie" value="" />
		        <input type="hidden" name="etablissement_transfert_id" value="" />
  	        <input type="hidden" name="sortie_reelle" value="" />
		        <button class="cancel" type="button" onclick="this.form.submit()">
		          Annuler la sortie
		         </button>
		       </td>
		     </tr>
			    
			   <!-- Sortie à effectuer -->
			   {{else}}
			   <tr>
			     <td class="text">
			      {{if $sejour->_num_dossier}}
              [{{$sejour->_num_dossier}}]
              <br />
            {{/if}}
            {{assign var=rpu_id value=$rpu->_id}}
            {{assign var=sejour_id value=$sejour->_id}}
            
            {{mb_field object=$sejour field="mode_sortie" defaultOption="&mdash; Mode de sortie" onchange="initFields($rpu_id,$sejour_id,this.value);"}}
            <input type="hidden" name="_modifier_sortie" value="1" />
			      <button class="tick" type="button" onclick="this.form.submit();">
			        Effectuer la sortie
			      </button>
			     </td>
			   </tr>
			   <tr>
			    <td>
			     <div id="listEtabs-{{$sejour->_id}}">
	           {{if $sejour->mode_sortie == "transfert"}}
	             {{assign var=_transfert_id value=$sejour->etablissement_transfert_id}}
	             {{include file="../../dPurgences/templates/inc_vw_etab_externes.tpl"}}
	           {{/if}}
	         </div>
	        </td>
			   </tr>
			  {{/if}}
			  </table>
			</form>
    </td>
    {{/if}}
  </tr>
  {{/foreach}}
</table>