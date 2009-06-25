{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include_script module=dPurgences script=contraintes_rpu}}

<script type="text/javascript">

ContraintesRPU.contraintesProvenance  = {{$contrainteProvenance|@json}};
ContraintesRPU.contraintesDestination = {{$contrainteDestination|@json}};
ContraintesRPU.contraintesOrientation = {{$contrainteOrientation|@json}};

function submitSejour(){
  var oForm = document.editSejour;
  submitFormAjax(oForm, 'systemMsg');
}

function loadTransfert(mode_sortie){
  // si Transfert, affichage du select
  if($('listEtabs')){
	  if(mode_sortie=="transfert"){
	    var url = new Url();
	    url.setModuleAction("dPurgences", "httpreq_vw_etab_externes");
	    url.requestUpdate('listEtabs', { waitingText: null } );
	  } else {
	    // sinon, on vide le contenu de la div
	    $("listEtabs").innerHTML = "";
	  }
  }
}

function initFields(mode_sortie){
  ContraintesRPU.updateDestination(mode_sortie, true);
  ContraintesRPU.updateOrientation(mode_sortie, true); 
}

</script>

<table style="width:100%">
  <tr>
    <td>
      <form name="editRPU" action="?" method="post" onsubmit="return onSubmitFormAjax(this);">
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
			        <select name="_helpers_diag_infirmier" style="width: 80px;" size="1" onchange="pasteHelperContent(this); this.form.diag_infirmier.onchange();">
			          <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
			          {{html_options options=$rpu->_aides.diag_infirmier.no_enum}}
			        </select>
			        <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('CRPU', this.form.diag_infirmier)">
			        	{{tr}}New{{/tr}}
			        </button>
			        <br />  
			        </td>
				  </tr>
				  
				  <tr>
				    <td colspan="2">
				      {{mb_field object=$rpu field="diag_infirmier" onchange="this.form.onsubmit();"}}
			      </td>
				  </tr>
				  
				  <tr>
				    <th>{{mb_label object=$rpu field="_entree"}}</th>
				    <td>{{$rpu->_entree|date_format:"%d %b %Y à %Hh%M"}}</td>
				  </tr>
				  
				  <tr>
				    <th>{{mb_label object=$rpu field="ccmu"}}</th>
				    <td>{{mb_field object=$rpu field="ccmu" canNull=false defaultOption="&mdash; Code CCMU" onchange="this.form.onsubmit();"}}</td>
				  </tr>
				  
				  
				  <tr> 
				    <th>{{mb_label object=$rpu field="mode_entree"}}</th>
				    <td>{{mb_field object=$rpu field="mode_entree" defaultOption="&mdash; Mode d'entrée" onchange="ContraintesRPU.updateProvenance(this.value, true); this.form.onsubmit();"}}</td>
			    </tr>
			    
			    {{if $dPconfig.dPurgences.old_rpu == "1"}}
				  <tr>
				    <th>{{mb_label object=$rpu field="urprov"}}</th>
				    <td>{{mb_field object=$rpu field="urprov" defaultOption="&mdash; Provenance" onchange="this.form.onsubmit();"}}</td>
				  </tr>
					{{else}}
				  <tr>
				    <th>{{mb_label object=$rpu field="provenance"}}</th>
				    <td>{{mb_field object=$rpu field="provenance" defaultOption="&mdash; Provenance" onchange="this.form.onsubmit();"}}</td>
				  </tr>
			    {{/if}}	  
				  
				  <tr>   
				    <th>{{mb_label object=$rpu field="transport"}}</th>
				    <td>{{mb_field object=$rpu field="transport" defaultOption="&mdash; Type de transport" onchange="this.form.onsubmit();"}}</td>
				  </tr>
				  
				  <tr>
				    <th>{{mb_label object=$rpu field="pec_transport"}}</th>
				    <td>{{mb_field object=$rpu field="pec_transport" defaultOption="&mdash; Prise en charge" onchange="this.form.onsubmit();"}}</td>
				  </tr>
				  
				  <tr>
				    <th>{{mb_label object=$rpu field="box_id"}}</th>
				    <td>
				      {{include file="../../dPhospi/templates/inc_select_lit.tpl field=box_id selected_id=$rpu->box_id ajaxSubmit=true listService=$listServicesUrgence}}
						</td>
				  </tr>

			  </table>
			</form>	      
    </td>
    <td>
      <form name="editRPUMotif" action="?" method="post" onsubmit="return onSubmitFormAjax(this);">
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
					      {{mb_field object=$rpu field="motif" onchange="this.form.onsubmit();"}}
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
				  
				  {{if $can->admin}}
				  <!-- Sortie réelle uniquement pour les administrateurs -->
				  <tr>
				    <th>{{mb_label object=$sejour field=sortie_reelle}}</th>
				    <td class="date">
				    	{{mb_field object=$sejour field=sortie_reelle form=editSejour onchange="submitSejour();" register=true}}
				    </td> 
				  </tr>
				  {{/if}}

				  <tr>
				    <th style="width: 120px;">{{mb_label object=$sejour field="mode_sortie"}}</th>
				    <td>
				      {{mb_field object=$sejour field="mode_sortie" defaultOption="&mdash; Mode de sortie" onchange="initFields(this.value);submitSejour();loadTransfert(this.value);"}}
				      {{if !$rpu->mutation_sejour_id}}
				      <div id="listEtabs">
				        {{if $sejour->mode_sortie == "transfert"}}
				          {{include file="../../dPurgences/templates/inc_vw_etab_externes.tpl" _transfert_id=$sejour->etablissement_transfert_id}}
				        {{/if}}
				      </div>
				      {{else}}
				      <strong>
				      <a href="?m=dPplanningOp&tab=vw_edit_sejour&sejour_id={{$rpu->mutation_sejour_id}}">
				      	Hospitalisation dossier [{{$rpu->_ref_sejour_mutation->_num_dossier}}]
				     	</a> 
				     	</strong>
				      {{/if}}
		 		    </td>
				   </tr>
				   
		     </table>
	     </form>
	     
	     <form name="editRPUDest" action="?" method="post" onsubmit="return onSubmitFormAjax(this);">
		     <input type="hidden" name="dosql" value="do_rpu_aed" />
		     <input type="hidden" name="m" value="dPurgences" />
		     <input type="hidden" name="del" value="0" />
		     <input type="hidden" name="rpu_id" value="{{$rpu->_id}}" />
			
	      <table class="form">
				  <tr>
				    <th style="width: 120px;">{{mb_label object=$rpu field="gemsa"}}</th>
				    <td>{{mb_field object=$rpu field="gemsa" canNull=false defaultOption="&mdash; Code GEMSA" onchange="this.form.onsubmit();"}}</td>
				  </tr>
				  
		      {{if $dPconfig.dPurgences.old_rpu == "1"}}
				  <tr>
				    <th>{{mb_label object=$rpu field="type_pathologie"}}</th>
				    <td>{{mb_field object=$rpu field="type_pathologie" canNull=false defaultOption="&mdash; Type de pathologie" onchange="this.form.onsubmit();"}}</td>
				  </tr>
				  
				  <tr>
				    <th>{{mb_label object=$rpu field="urtrau"}}</th>
				    <td>{{mb_field object=$rpu field="urtrau" canNull=false defaultOption="&mdash; Type de soins traumato" onchange="this.form.onsubmit();"}}</td>
				  </tr>
				  {{/if}}
		      
				  <tr>
				    <th class="category" colspan="2">Précisions sur la sortie</th>
				  </tr>

			    {{if $dPconfig.dPurgences.old_rpu == "1"}}
				  <tr>
				    <th>{{mb_label object=$rpu field="urmuta"}}</th>
				    <td>{{mb_field object=$rpu field="urmuta" canNull=false defaultOption="&mdash; Cause" onchange="this.form.onsubmit();"}}</td>
				  </tr>
				  {{else}}
				  <tr>
				    <th>{{mb_label object=$rpu field="orientation"}}</th>
				    <td>{{mb_field object=$rpu field="orientation" defaultOption="&mdash; Orientation" onchange="this.form.onsubmit();"}}</td>
				  </tr>
				  <tr> 
				    <th>{{mb_label object=$rpu field="destination"}}</th>
				    <td>{{mb_field object=$rpu field="destination" defaultOption="&mdash; Destination" onchange="this.form.onsubmit();"}}</td> 
				  </tr>
				  {{/if}}		  
				  
			  </table>
      </form>
    </td>
  </tr>
</table>

{{if !$rpu->mutation_sejour_id}}
<table class="form">    
  <tr>
	  <td class="button" colspan="4">
      <!-- ZSTCD --> 
		  {{if 0}}
      <form name="EditSejourZT" method="post" action="?m={{$m}}">
				<input type="hidden" name="dosql" value="do_sejour_aed" />
				<input type="hidden" name="m" value="dPplanningOp" />
				<input type="hidden" name="del" value="0" />
				<input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
				{{if $sejour->zt == "0"}}
				<input type="hidden" name="zt" value="1" />
        <button class="tick" type="submit">
          Passer en ZSTCD
        </button>
				{{/if}}
				
				{{if $sejour->zt == "1"}}
				<input type="hidden" name="zt" value="0" />
        <button class="cancel" type="submit">
          Revenir en ATU
        </button>
				{{/if}}
      </form>
      {{/if}}

	    <!-- Reconvocation => formulaire de creation de consultation avec champs pre-remplis -->
	    <button class="new" type="button" onclick="newConsultation({{$consult->_ref_plageconsult->chir_id}},{{$consult->patient_id}},{{$consult->_id}})">
        Reconvoquer
      </button>
       
      <!-- Hospitalisation immediate, creation d'un sejour et transfert des actes dans le nouveau sejour -->
      <form name="transfertHospi" method="post" action="?m={{$m}}" onsubmit="return confirm('{{tr}}confirm-RPU-Hospitalisation{{/tr}}');">
        <input type="hidden" name="dosql" value="do_transfert_aed" />
        <input type="hidden" name="m" value="dPurgences" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="rpu_id" value="{{$rpu->_id}}" />
        <button class="new" type="submit">Hospitaliser</button>
      </form>
      
      <!--  Sortie prévue du patient -->
      <form name="editSortiePrevu" method="post" action="?m={{$m}}">
        <input type="hidden" name="dosql" value="do_sejour_aed" />
        <input type="hidden" name="m" value="dPplanningOp" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
        Sortie prévue à {{mb_field object=$sejour field="sortie_prevue" register=true form="editSortiePrevu" onchange="submitFormAjax(this.form, 'systemMsg')"}}
      </form>
	  </td>
	</tr>
</table>
{{/if}}

<script type="text/javascript">

// Lancement des fonctions de contraintes entre les champs
{{if $rpu->mode_entree}}
ContraintesRPU.updateProvenance("{{$rpu->mode_entree}}");
{{/if}}

{{if $sejour->mode_sortie}}
ContraintesRPU.updateDestination("{{$sejour->mode_sortie}}");
ContraintesRPU.updateOrientation("{{$sejour->mode_sortie}}");
{{/if}}

</script>