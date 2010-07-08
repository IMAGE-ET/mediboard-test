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

function redirect() {
	//document.location.href="?m=dPurgences&tab=vw_idx_rpu";
}

function submitSejourWithSortieReelle(callback){
  submitFormAjax(getForm('editSortieReelle'), 'systemMsg', { onComplete : callback });
}

function reloadSortieReelle() {
	  var url = new Url;
	  url.setModuleAction("dPurgences", "ajax_sortie_reelle");
	  url.addParam("sejour_id", getForm('editSortieReelle').elements.sejour_id.value);
	  url.addParam("consult_id", getForm('ValidCotation').elements.consultation_id.value);
	  url.requestUpdate('div_sortie_reelle');
	}

function submitConsultWithChrono(chrono, callback) {
  var oForm = document.editFrmFinish;
  oForm.chrono.value = chrono;
  submitFormAjax(oForm, 'systemMsg', { onComplete : function(){ reloadFinishBanner(); if (callback) callback(); }});
}

function submitRPU(callback) {
	var oForm = document.editSortieAutorise;
	submitFormAjax(oForm, 'systemMsg', { onComplete : function(){ reloadSortieReelle(); if (callback) callback(); }});
}

function submitSejRpuConsult() {
	if (checkForm(getForm("editRPU")) && checkForm(getForm("editRPUDest"))) {
		submitSejourWithSortieReelle(
		  submitRPU.curry(
				submitConsultWithChrono.curry({{$consult|const:'TERMINE'}}, redirect)
			)
	  ); 
	}
}

function loadTransfert(mode_sortie){
  $('etablissement_sortie_transfert').setVisible(mode_sortie == "transfert");
}

function loadServiceMutation(mode_sortie){
  $('service_sortie_transfert').setVisible(mode_sortie == "mutation");
}

function initFields(mode_sortie){
  ContraintesRPU.updateDestination(mode_sortie, true);
  ContraintesRPU.updateOrientation(mode_sortie, true); 
	loadTransfert(mode_sortie);
  loadServiceMutation(mode_sortie);
}

function printDossier(id) {
  var url = new Url("dPurgences", "print_dossier");
  url.addParam("rpu_id", id);
  url.popup(700, 550, "RPU");
}

function showEtabEntreeTransfert(mode) {
  // mode de transfert = transfert (7)
  if (mode == 7) {
    $('etablissement_entree_transfert').show();
  } else {
    $('etablissement_entree_transfert').hide();
    $V(getForm('editRPU')._etablissement_entree_transfert_id, '');
  }
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
				<input type="hidden" name="sejour_id" value="{{$rpu->sejour_id}}" />
				<input type="hidden" name="_bind_sejour" value="1" />
				
				<table class="form">
				  <tr>
				    <th class="category" colspan="2">Prise en charge infirmier</th>
				  </tr> 
				  
				  <tr>
				    <td colspan="2">{{mb_label object=$rpu field="diag_infirmier"}}
				    <!-- Aide a la saisie -->
			        <select name="_helpers_diag_infirmier" style="width: 80px;" size="1" onchange="pasteHelperContent(this); this.form.diag_infirmier.onchange();" class="helper">
			          <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
			          {{html_options options=$rpu->_aides.diag_infirmier.no_enum}}
			        </select>
			        <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('CRPU', this.form.diag_infirmier, null, null, null, null, {{$user_id}})">
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
				    <td>{{mb_field object=$rpu field="mode_entree" defaultOption="&mdash; Mode d'entrée" onchange="ContraintesRPU.updateProvenance(this.value, true); showEtabEntreeTransfert(this.value); this.form.onsubmit();"}}</td>
			    </tr>
			    
					<tr>
						<th>{{mb_label object=$rpu field="_etablissement_entree_transfert_id"}}</th>
			      <td>
			        <div id="etablissement_entree_transfert" {{if $rpu->mode_entree != '7'}}style="display:none"{{/if}}>
			          {{mb_field object=$rpu field="_etablissement_entree_transfert_id" form="editRPU" autocomplete="true,1,50,true,true" onchange="this.form.onsubmit();"}}
			        </div>
           </td> 
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
				      {{include file="../../dPhospi/templates/inc_select_lit.tpl" field=box_id selected_id=$rpu->box_id ajaxSubmit=true listService=$listServicesUrgence}}
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
					      <select name="_helpers_motif" size="1" onchange="pasteHelperContent(this); this.form.motif.onchange();" class="helper">
				          <option value="">&mdash; Aide</option>
				          {{html_options options=$rpu->_aides.motif.no_enum}}
				        </select>
				        <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('CRPU', this.form.motif, null, null, null, null, {{$user_id}})">{{tr}}New{{/tr}}</button><br />      
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
				    <td>
				    	{{mb_field object=$sejour field=sortie_reelle form=editSejour onchange="submitSejour();" register=true}}
				    </td> 
				  </tr>
				  {{/if}}

				  <tr>
				    <th style="width: 120px;">{{mb_label object=$sejour field="mode_sortie"}}</th>
				    <td>
				      {{mb_field object=$sejour field="mode_sortie" defaultOption="&mdash; Mode de sortie" onchange="initFields(this.value); submitSejour();"}}
				      {{if !$rpu->mutation_sejour_id}}
							  <input type="hidden" name="group_id" value="{{if $sejour->group_id}}{{$sejour->group_id}}{{else}}{{$g}}{{/if}}" />
							{{else}}
					      <strong>
						      <a href="?m=dPplanningOp&tab=vw_edit_sejour&sejour_id={{$rpu->mutation_sejour_id}}">
						      	Hospitalisation dossier {{mb_include module=dPplanningOp template=inc_vw_numdos num_dossier=$rpu->_ref_sejour_mutation->_num_dossier}}
						     	</a> 
					     	</strong>
				      {{/if}}
              <div id="etablissement_sortie_transfert" {{if !$sejour->etablissement_transfert_id}}style="display:none;"{{/if}}>
                  {{mb_field object=$sejour field="etablissement_transfert_id" form="editSejour" autocomplete="true,1,50,true,true" onchange="submitSejour();"}}
                </div>
                
							<div id="service_sortie_transfert" {{if !$sejour->service_mutation_id}}style="display:none;"{{/if}}>
                {{mb_field object=$sejour field="service_mutation_id" form="editSejour" autocomplete="true,1,50,true,true" onchange="submitSejour();"}}
              </div>
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

<table class="form">    
  <tr>
	  <td class="button" colspan="4">
      {{if !$rpu->mutation_sejour_id}}
				{{if $dPconfig.dPurgences.gerer_reconvoc == "1"}}
			    <!-- Reconvocation => formulaire de creation de consultation avec champs pre-remplis -->
			    <button class="new" type="button" onclick="newConsultation({{$consult->_ref_plageconsult->chir_id}},{{$consult->patient_id}},{{$consult->_id}})">
		        Reconvoquer
		      </button>
	      {{/if}}
				
				{{if $dPconfig.dPurgences.gerer_hospi == "1"}} 
		      <!-- Hospitalisation immediate, creation d'un sejour et transfert des actes dans le nouveau sejour -->
		      <form name="transfertHospi" method="post" action="?m={{$m}}" onsubmit="return confirm($T('confirm-RPU-Hospitalisation'));">
		        <input type="hidden" name="dosql" value="do_transfert_aed" />
		        <input type="hidden" name="m" value="dPurgences" />
		        <input type="hidden" name="del" value="0" />
		        <input type="hidden" name="rpu_id" value="{{$rpu->_id}}" />
		        <button class="new" type="submit">Hospitaliser</button>
	        </form>
				{{/if}}
      {{/if}}
      <!--  Autoriser sortie du patient --> <!--  Autoriser sortie du patient et valider la sortie -->
      <form name="editSortieAutorise" method="post" action="?m={{$m}}">
        <input type="hidden" name="dosql" value="do_rpu_aed" />
        <input type="hidden" name="m" value="dPurgences" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="rpu_id" value="{{$rpu->_id}}" />
        <input type="hidden" name="sortie_autorisee" value="1" />
      </form>
      <div id="div_sortie_reelle">
        {{mb_include module=dPurgences template=inc_sortie_reelle}}
      </div>
      
      <button type="button" class="print" onclick="printDossier({{$rpu->_id}})">
        {{tr}}Print{{/tr}} dossier
      </button>
	  </td>
	</tr>
</table>

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