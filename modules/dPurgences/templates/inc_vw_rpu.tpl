<script type="text/javascript">

function modeEntreeProv(mode_entree){
  // Recuperation du tableau de contrainte modeEntree/Provenance en JSON
  var contrainteProvenance = {{$contrainteProvenance|@json}}
  
  if(mode_entree == ""){
    $A(document.editRPU.provenance).each( function(input) {
      input.disabled = false;
    });
    return;
  }
  
  if(!contrainteProvenance[mode_entree]){
    $A(document.editRPU.provenance).each( function(input) {
      input.disabled = true;
    });
    return;
  }
 
  var _contrainteProvenance = contrainteProvenance[mode_entree];
  
  $A(document.editRPU.provenance).each( function(input) {
    input.disabled = !_contrainteProvenance.include(input.value);
  });
}

function modeSortieDest(mode_sortie){
  // Recuperation du tableau de contrainte modeSortie/Destination en JSON
  var contrainteDestination = {{$contrainteDestination|@json}}
 
  if(mode_sortie == ""){
    $A(document.editRPUDest.destination).each( function(input) {
      input.disabled = false;
    });
    return;
  }
  
  if(!contrainteDestination[mode_sortie]){
    $A(document.editRPUDest.destination).each( function(input) {
      input.disabled = true;
    });
    return;
  }
 
  var _contrainteDestination = contrainteDestination[mode_sortie];
  $A(document.editRPUDest.destination).each( function(input) {
    input.disabled = !_contrainteDestination.include(input.value);
  });
}


function modeSortieOrient(mode_sortie){
  // Recuperation du tableau de contrainte modeSortie/Orientation en JSON
  var contrainteOrientation = {{$contrainteOrientation|@json}}
  
  if(mode_sortie == ""){
    $A(document.editRPUDest.orientation).each( function(input) {
      input.disabled = false;
    });
    return;
  }
  
  if(!contrainteOrientation[mode_sortie]){
    $A(document.editRPUDest.orientation).each( function(input) {
      input.disabled = true;
    });
    return;
  }
  
  var _contrainteOrientation = contrainteOrientation[mode_sortie];
  $A(document.editRPUDest.orientation).each( function(input) {
    input.disabled = !_contrainteOrientation.include(input.value);
  });
}


function submitRPU(oForm){
  submitFormAjax(oForm, 'systemMsg');
}

function submitSejour(){
  var oForm = document.editSejour;
  submitFormAjax(oForm, 'systemMsg');
}

function loadTransfert(mode_sortie){
  // si Transfert, affichage du select
  if(mode_sortie=="transfert"){
    var url = new Url();
    url.setModuleAction("dPurgences", "httpreq_vw_etab_externes");
    url.requestUpdate('listEtabs', { waitingText: null } );
  } else {
    // sinon, on vide le contenu de la div
    $("listEtabs").innerHTML = "";
    // On vide etablissement_transfert_id
    //submitFormSejour("");
  }
}


function initFields(mode_sortie){
  var oForm = document.editRPUDest;
  oForm.destination.value = ''; 
  oForm.orientation.value = ''; 
  modeSortieDest(mode_sortie);
  modeSortieOrient(mode_sortie); 
}

</script>



<table style="width:100%">
  <tr>
    <td>
      <form name="editRPU" action="?" method="post">
			  <input type="hidden" name="dosql" value="do_rpu_aed" />
			  <input type="hidden" name="m" value="dPurgences" />
			  <input type="hidden" name="del" value="0" />
			  <input type="hidden" name="rpu_id" value="{{$rpu->_id}}" />
				<table class="form">
				  <tr>
				    <th class="category" colspan="2">Prise en charge infirmier</th>
				  </tr> 
				  <tr>
				    <td colspan="2">{{mb_label object=$rpu field="diag_infirmier"}}
				    <!-- Aide a la saisie -->
			        <select name="_helpers_diag_infirmier" size="1" onchange="pasteHelperContent(this); this.form.diag_infirmier.onchange();">
			          <option value="">&mdash; Choisir une aide</option>
			          {{html_options options=$rpu->_aides.diag_infirmier.no_enum}}
			        </select>
			        <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('CRPU', this.form.diag_infirmier)">{{tr}}New{{/tr}}</button><br />  
			        </td>
				  </tr>
				  <tr>
				    <td colspan="2">
				      {{mb_field object=$rpu field="diag_infirmier" onchange="submitRPU(this.form);"}}
			      </td>
				  </tr>
				  <tr>
				    <th>{{mb_label object=$rpu field="_entree"}}</th>
				    <td>{{$rpu->_entree|date_format:"%d %b %Y à %Hh%M"}}</td>
				  </tr>
				  <tr>
				    <th>{{mb_label object=$rpu field="ccmu"}}</th>
				    <td>{{mb_field object=$rpu field="ccmu" defaultOption="&mdash; Degré d'urgence" onchange="submitRPU(this.form);"}}</td>
				  </tr>
				  <tr> 
				    <th>{{mb_label object=$rpu field="mode_entree"}}</th>
				    <td>{{mb_field object=$rpu field="mode_entree" defaultOption="&mdash; Mode d'entrée" onchange="this.form.provenance.value = ''; modeEntreeProv(this.value); submitRPU(this.form);"}}</td>
			    </tr>	  
				  <tr>
				    <th>{{mb_label object=$rpu field="provenance"}}</th>
				    <td>{{mb_field object=$rpu field="provenance" defaultOption="&mdash; Provenance" onchange="submitRPU(this.form);"}}</td>
				  </tr>
				  <tr>   
				    <th>{{mb_label object=$rpu field="transport"}}</th>
				    <td>{{mb_field object=$rpu field="transport" defaultOption="&mdash; Type de transport" onchange="submitRPU(this.form);"}}</td>
				  
				  </tr>
				  <tr>
				    <th>{{mb_label object=$rpu field="prise_en_charge"}}</th>
				    <td>{{mb_field object=$rpu field="prise_en_charge" defaultOption="&mdash; Prise en charge" onchange="submitRPU(this.form);"}}</td>
				  </tr>
			  </table>
			</form>	      
    </td>
    <td>
      <form name="editRPUMotif" action="?" method="post">
			  <input type="hidden" name="dosql" value="do_rpu_aed" />
			  <input type="hidden" name="m" value="dPurgences" />
			  <input type="hidden" name="del" value="0" />
			  <input type="hidden" name="rpu_id" value="{{$rpu->_id}}" />
					<table class="form">
					  <tr>
					    <th class="category" colspan="2">Prise en charge praticien</th>
					  </tr> 
					  <tr>
					    <td colspan="2">{{mb_label object=$rpu field="motif"}}
					      <select name="_helpers_motif" size="1" onchange="pasteHelperContent(this); this.form.motif.onchange();">
				          <option value="">&mdash; Choisir une aide</option>
				          {{html_options options=$rpu->_aides.motif.no_enum}}
				        </select>
				        <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('CRPU', this.form.motif)">{{tr}}New{{/tr}}</button><br />      
					      </td>
					  </tr>
					  <tr>
					    <td colspan="2">
					      {{mb_field object=$rpu field="motif" onchange="submitRPU(this.form);"}}
					    </td>
				   </tr>
				 </table>
			 </form>
	 
	     {{assign var=sejour value=$rpu->_ref_sejour}}
	     <form name="editSejour" action="?" method="post">
			   <input type="hidden" name="dosql" value="do_sejour_aed" />
			   <input type="hidden" name="m" value="dPplanningOp" />
			   <input type="hidden" name="del" value="0" />
			   <input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
			  
				  <table class="form">
				  <tr>
				    <th>{{mb_label object=$sejour field="sortie_reelle"}}</th>
				    <td class="date">{{mb_field object=$sejour field="sortie_reelle" form="editSejour" onchange="submitSejour();"}}</td> 
				  </tr>
				  <tr>
				    <th style="width: 120px;">{{mb_label object=$sejour field="mode_sortie"}}</th>
				    <td>
				      {{mb_field object=$sejour field="mode_sortie" defaultOption="&mdash; Mode de sortie" onchange="initFields(this.value);submitSejour();loadTransfert(this.value);"}}
				      <div id="listEtabs">
				        {{if $sejour->mode_sortie == "transfert"}}
				          {{include file="../../dPurgences/templates/inc_vw_etab_externes.tpl" _transfert_id=$sejour->etablissement_transfert_id}}
				        {{/if}}
				      </div>
		 		    </td>
				   </tr>
		     </table>
	     </form>
	     
	     <form name="editRPUDest" action="?" method="post">
		     <input type="hidden" name="dosql" value="do_rpu_aed" />
		     <input type="hidden" name="m" value="dPurgences" />
		     <input type="hidden" name="del" value="0" />
		     <input type="hidden" name="rpu_id" value="{{$rpu->_id}}" />
			
	      <table class="form">
				  <tr> 
				    <th style="width: 120px;">{{mb_label object=$rpu field="destination"}}</th>
				    <td>{{mb_field object=$rpu field="destination" defaultOption="&mdash; Destination" onchange="submitRPU(this.form);"}}</td> 
				  </tr>	  
				  <tr>
				    <th>{{mb_label object=$rpu field="orientation"}}</th>
				    <td>{{mb_field object=$rpu field="orientation" defaultOption="&mdash; Orientation" onchange="submitRPU(this.form);"}}</td>
				  </tr>
				  <tr>   
				    <td colspan="2" />
				  </tr>
				  <tr>
				    <td colspan="2" />  
				  </tr>
			  </table>
      </form>
    </td>
  </tr>
</table>

 
<table class="form">    
  <tr>
	  <td class="button" colspan="4">
	    <!-- Reconvocation => formulaire de creation de consultation avec champs pre-remplis -->
	    <button class="new" type="button" onclick="newConsultation({{$consult->_ref_plageconsult->chir_id}},{{$consult->patient_id}},{{$consult->_id}})">
        Reconvoquer
      </button>
       
      <!-- Hospitalisation immediate, creation d'un sejour et transfert des actes dans le nouveau sejour --> 
      <form name="transfertHospi" method="post">
        <input type="hidden" name="dosql" value="do_transfert_aed" />
        <input type="hidden" name="m" value="dPurgences" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="rpu_id" value="{{$rpu->_id}}" />
        <button class="new" type="button" onclick="submit(this.form);">Hospitaliser</button>
      </form>
	  </td>
	</tr>
</table>

<script type="text/javascript">

// Lancement des fonctions de contraintes entre les champs
{{if $rpu->mode_entree}}
modeEntreeProv("{{$rpu->mode_entree}}");
{{/if}}

{{if $sejour->mode_sortie}}
modeSortieDest("{{$sejour->mode_sortie}}");
modeSortieOrient("{{$sejour->mode_sortie}}");
{{/if}}

</script>