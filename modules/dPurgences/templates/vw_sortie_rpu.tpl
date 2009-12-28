{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

Main.add(function () {
  Calendar.regField(getForm("changeDate").date, null, {noView: true});
  setInterval(refreshCanLeaveSince, 60000);
});

function modeSortieDest(mode_sortie, rpu_id) {
  var oFormRPU = document.forms["editRPU-" + rpu_id]; 
  
  // Recuperation du tableau de contrainte modeSortie/Destination en JSON
  var contrainteDestination = {{$contrainteDestination|@json}};
 
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
    url.requestUpdate('listEtabs-'+sejour_id);
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

function refreshTime(rpus) {
	$H(rpus).each(function(s) {
		$("rpu-"+s.key).className = "";
		$("rpu-"+s.key).addClassName(s.value.alert).update(s.value.value);
	});
}

function refreshCanLeaveSince() {
	var url = new Url();
  url.setModuleAction("dPurgences", "ajax_reload_can_leave_since");
  {{foreach from=$listSejours item=_sejour}}
    url.addParam("rpus[{{$_sejour->_ref_rpu->_id}}]", {{$_sejour->_ref_rpu->_id}});
  {{/foreach}}
  url.requestJSON(refreshTime);
}

function validCotation(consutation_id) {
	submitFormAjax(getForm('validCotation-'+consutation_id), 'systemMsg');
}

// Fonction appelée dans inc_vw_etab_externe qui submit le sejour dans le cas de "inc_vw_rpu.tpl"
// Dans la sortie, on ne veut pas déclencher de submit
function submitSejour(){
 // Ne rien faire
}

</script>

<table class="main">
  <tr>
    <th>
      <div style="float: right">
        Type d'affichage
        <form name="selView" action="?m=dPurgences&amp;tab=vw_sortie_rpu" method="post">
  	      <select name="selAffichage" onchange="submit();">
  	        <option value="tous" {{if $selAffichage == "tous"}}selected = "selected"{{/if}}>Tous</option>
  	        <option value="sortie" {{if $selAffichage == "sortie"}} selected = "selected" {{/if}}>Sortie à effectuer</option>
  	      </select>
  	    </form>
      </div>
      
      le {{$date|date_format:$dPconfig.longdate}}
      <form action="?" name="changeDate" method="get">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="tab" value="{{$tab}}" />
        <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
      </form>
    </th>
  </tr>
</table>

<table class="tbl">
  <tr>
    <th>{{mb_title class=CRPU field="_patient_id"}}</th>
    <th>{{mb_title class=CRPU field="_responsable_id"}}</th>
    <th>
    {{mb_colonne class="CRPU" field="_pec_transport" order_col=$order_col order_way=$order_way url="?m=$m&amp;tab=vw_sortie_rpu"}}
    </th>
    <th>{{mb_title class=CRPU field="rpu_id"}}</th>
    <th>{{mb_title class=CRPU field="sejour_id"}}</th>
    <th>{{mb_title class=CRPU field="_can_leave"}}</th>
  </tr>
  {{foreach from=$listSejours item=sejour}}
  {{assign var=rpu value=$sejour->_ref_rpu}}
  {{assign var=patient value=$sejour->_ref_patient}}
  <tr>
    <td {{if $sejour->annule}}class="cancelled"{{/if}}>
      <form name="validCotation-{{$sejour->_ref_consult_atu->_id}}" action="" method="post"> 
		    <input type="hidden" name="dosql" value="do_consultation_aed" />
		    <input type="hidden" name="m" value="dPcabinet" />
		    <input type="hidden" name="del" value="0" />
		    <input type="hidden" name="consultation_id" value="{{$sejour->_ref_consult_atu->_id}}" />
		    <input type="hidden" name="valide" value="1" />
		  </form>
  
		  <a class="action" style="float: right;" title="Modifier le dossier administratif" href="?m=dPpatients&amp;tab=vw_edit_patients&amp;patient_id={{$patient->_id}}">
	        <img src="images/icons/edit.png" alt="modifier" />
	 	  </a>
 	  
      <a href="?m=dPurgences&amp;tab=vw_aed_rpu&amp;rpu_id={{$rpu->_id}}">
        {{mb_include module=dPpatients template=inc_vw_ipp ipp=$patient->_IPP}}<br />
        <strong>{{$patient->_view}}</strong>
      </a>
    </td>
    
    {{if $sejour->annule}}
    <td class="cancelled" colspan="5">
      {{if $rpu->mutation_sejour_id}}
      Hospitalisation
      <a href="?m=dPplanningOp&tab=vw_edit_sejour&sejour_id={{$rpu->mutation_sejour_id}}">
        dossier {{mb_include module=dPplanningOp template=inc_vw_numdos num_dossier=$rpu->_ref_sejour_mutation->_num_dossier}}
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
      {{if !$rpu->_ref_consult->_ref_actes}}<div class="warning">Codage des actes manquant</div>{{/if}}
			{{if $sejour->sortie_reelle && !$rpu->_ref_consult->valide}}<div class="warning">La cotation n'est pas validée</div>{{/if}}

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
		        {{mb_include module=dPplanningOp template=inc_vw_numdos num_dossier=$sejour->_num_dossier}}
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
			       {{mb_include module=dPplanningOp template=inc_vw_numdos num_dossier=$sejour->_num_dossier}}
             <br />
            {{assign var=rpu_id value=$rpu->_id}}
            {{assign var=sejour_id value=$sejour->_id}}
            
            {{mb_field object=$sejour field="mode_sortie" defaultOption="&mdash; Mode de sortie" onchange="initFields($rpu_id,$sejour_id,this.value);"}}
            <input type="hidden" name="_modifier_sortie" value="1" />
			      <button class="tick" type="button" onclick="validCotation('{{$sejour->_ref_consult_atu->_id}}'); this.form.submit();">
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
    <td id="rpu-{{$rpu->_id}}" {{if !$sejour->sortie_reelle}}class="{{if !$rpu->sortie_autorisee}}arretee{{/if}} {{if $rpu->_can_leave_error}}error{{elseif $rpu->_can_leave_warning}}warning{{else}}ok{{/if}}"{{/if}}>
      {{if $sejour->sortie_reelle}}
        
      {{elseif $rpu->_can_leave == -1}}
        {{tr}}CConsultation{{/tr}} {{tr}}CConsultation.chrono.48{{/tr}} <br />
        {{tr}}CRPU-sortie_assuree.{{$rpu->sortie_autorisee}}{{/tr}}
      {{elseif $rpu->_can_leave != -1 && !$rpu->sortie_autorisee}}
        {{tr}}CConsultation{{/tr}} {{tr}}CConsultation.chrono.64{{/tr}} <br />
        {{tr}}CRPU-sortie_assuree.0{{/tr}}
      {{else}}
        {{if $rpu->_can_leave_since}}
          {{tr}}CRPU-_can_leave_since{{/tr}}
        {{/if}}
        {{if $rpu->_can_leave_about}}
          {{tr}}CRPU-_can_leave_about{{/tr}}
        {{/if}}
        <span title="{{$sejour->sortie_prevue}}">{{mb_value object=$rpu field="_can_leave"}}</span><br />
        {{tr}}CRPU-sortie_assuree.{{$rpu->sortie_autorisee}}{{/tr}}
      {{/if}}
    </td>
    {{/if}}
  </tr>
  {{/foreach}}
</table>