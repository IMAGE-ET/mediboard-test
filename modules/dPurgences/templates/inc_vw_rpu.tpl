{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module=dPurgences script=contraintes_rpu}}

<script type="text/javascript">

ContraintesRPU.contraintesProvenance  = {{$contrainteProvenance|@json}};
ContraintesRPU.contraintesDestination = {{$contrainteDestination|@json}};
ContraintesRPU.contraintesOrientation = {{$contrainteOrientation|@json}};

function redirect() {
  //document.location.href="?m=dPurgences&tab=vw_idx_rpu";
}

function submitSejourWithSortieReelle(callback){
  submitFormAjax(getForm('editSortieReelle'), 'systemMsg', { onComplete : 
    callback 
  });
}

function submitRPU(callback) {
  var oForm = document.editSortieAutorise;
  submitFormAjax(oForm, 'systemMsg', { onComplete : function(){ 
    reloadSortieReelle(); 
    if (callback) callback(); 
  }});
}

function submitConsultWithChrono(chrono, callback) {
  var oForm = document.editFrmFinish;
  oForm.chrono.value = chrono;
  submitFormAjax(oForm, 'systemMsg', { onComplete : function(){ 
    reloadFinishBanner(); 
    if (callback) callback(); 
  }});
}

function submitSejour(sejour_id) {
  var oForm = document.editSejour;
  return onSubmitFormAjax(oForm, { onComplete: function() {
    if (sejour_id != null) {
      reloadDiagnostic(sejour_id, 1);
    }
  }});
} 

function reloadSortieReelle() {
  var url = new Url("dPurgences", "ajax_sortie_reelle");
  url.addParam("sejour_id", getForm('editSortieReelle').elements.sejour_id.value);
  url.addParam("consult_id", getForm('ValidCotation').elements.consultation_id.value);
  url.requestUpdate('div_sortie_reelle');
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

Fields = {
  init: function(mode_sortie) {
	  ContraintesRPU.updateDestination(mode_sortie, true);
	  ContraintesRPU.updateOrientation(mode_sortie, true); 
	  $('etablissement_sortie_transfert').setVisible(mode_sortie == "transfert");
	  $('service_sortie_transfert'      ).setVisible(mode_sortie == "mutation");
	  $('commentaires_sortie'           ).setVisible(mode_sortie && mode_sortie != "normal");
	}
};

function printDossier(id) {
  var url = new Url("dPurgences", "print_dossier");
  url.addParam("rpu_id", id);
  url.popup(700, 550, "RPU");
}

function showEtabEntreeTransfert(mode) {
  // mode de transfert = transfert (7)
  $('service_entree_transfert').hide();
  $('etablissement_entree_transfert').hide();
  
  if (mode == 7) {
    $('etablissement_entree_transfert').show();
    $V(getForm('editRPU')._service_entree_transfert_id, '');
  } 
  else if (mode == 6) {
    $('service_entree_transfert').show();
    $V(getForm('editRPU')._etablissement_entree_transfert_id, '');
  }
}

</script>

<table class="form">
  <tr>
    <td class="halfPane">
      <fieldset>
        <legend>Prise en charge infirmier</legend>
        <form name="editRPU" action="?" method="post" onsubmit="return onSubmitFormAjax(this);">
          <input type="hidden" name="dosql" value="do_rpu_aed" />
          <input type="hidden" name="m" value="dPurgences" />
          <input type="hidden" name="del" value="0" />
          <input type="hidden" name="rpu_id" value="{{$rpu->_id}}" />
          <input type="hidden" name="sejour_id" value="{{$rpu->sejour_id}}" />
          <input type="hidden" name="_bind_sejour" value="1" />
          
          <table class="layout" style="width: 100%">
            <col class="narrow" />
            <tr>
              <td colspan="2">
                <script type="text/javascript">
                  Main.add(function() {
                    var form = getForm("editRPU");
                    var options = {
                      objectClass: "{{$rpu->_class_name}}",
                      contextUserId: "{{$userSel->_id}}",
                      contextUserView: "{{$userSel->_view}}",
                      timestamp: "{{$conf.dPcompteRendu.CCompteRendu.timestamp}}",
                      validate: function() { form.onsubmit(); },
                      resetSearchField: false,
                      resetDependFields: false,
                      validateOnBlur: false
                    }
                    
                    new AideSaisie.AutoComplete(form.elements.diag_infirmier, options);
                    new AideSaisie.AutoComplete(form.elements.pec_douleur   , options);
                    });
                </script>

              	<table class="layout" style="width: 100%">
              		<tr>
                    <td style="width: 50%">{{mb_label object=$rpu field="diag_infirmier"}}</td>
                    <td style="width: 50%">{{mb_label object=$rpu field="pec_douleur"}}</td>
              		</tr>
									<tr>
			              <td>{{mb_field object=$rpu field="diag_infirmier" onchange="this.form.onsubmit();" class="autocomplete"}}</td>
			              <td>{{mb_field object=$rpu field="pec_douleur"    onchange="this.form.onsubmit();" class="autocomplete"}}</td>
									</tr>
              	</table>
								
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
            
            <tr id="etablissement_entree_transfert" {{if $rpu->mode_entree != '7'}}style="display:none"{{/if}}>
              <th>{{mb_label object=$rpu field="_etablissement_entree_transfert_id"}}</th>
              <td>{{mb_field object=$rpu field="_etablissement_entree_transfert_id" form="editRPU" autocomplete="true,1,50,true,true" onchange="this.form.onsubmit();"}}</td> 
            </tr>
            
            <tr id="service_entree_transfert" {{if $rpu->mode_entree != '6'}}style="display:none"{{/if}}>
              <th>{{mb_label object=$rpu field="_service_entree_mutation_id"}}</th>
              <td>{{mb_field object=$rpu field="_service_entree_mutation_id" form="editRPU" autocomplete="true,1,50,true,true" onchange="this.form.onsubmit();"}}</td> 
            </tr>
            
            {{if $conf.dPurgences.old_rpu == "1"}}
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
      </fieldset>
    </td>
		
    <td>
      <fieldset>
        <legend>Prise en charge praticien</legend>
				
        <form name="editRPUMotif" action="?" method="post" onsubmit="return onSubmitFormAjax(this);">
          <input type="hidden" name="dosql" value="do_rpu_aed" />
          <input type="hidden" name="m" value="dPurgences" />
          <input type="hidden" name="del" value="0" />
          <input type="hidden" name="rpu_id" value="{{$rpu->_id}}" />
            <table class="layout" style="width: 100%">
              <tr>
                <td colspan="2">
                  {{mb_label object=$rpu field="motif"}}
                </td>
              </tr>
              <tr>
                <td colspan="2">
                {{main}}
                  var form = getForm("editRPUMotif");
                  var options = {
                    objectClass: "{{$rpu->_class_name}}",
                    contextUserId: "{{$userSel->_id}}",
                    contextUserView: "{{$userSel->_view}}",
                    timestamp: "{{$conf.dPcompteRendu.CCompteRendu.timestamp}}",
                    validate: function() { form.onsubmit(); },
                    resetSearchField: false,
                    resetDependFields: false,
                    validateOnBlur: false
                  }
									
                  new AideSaisie.AutoComplete(form.elements.motif, options);
                {{/main}}
								
                {{mb_field object=$rpu field="motif" onchange="this.form.onsubmit();" class="autocomplete"}}
                </td>
             </tr>
           </table>
         </form>
     
		    {{assign var=sejour value=$rpu->_ref_sejour}}
        {{mb_include module=dPurgences template=inc_form_sortie}}

      </fieldset>

      <fieldset>
        <legend>Précisions sur la sortie</legend>
        <form name="editRPUDest" action="?" method="post" onsubmit="return onSubmitFormAjax(this);">
          <input type="hidden" name="dosql" value="do_rpu_aed" />
          <input type="hidden" name="m" value="dPurgences" />
          <input type="hidden" name="del" value="0" />
          <input type="hidden" name="rpu_id" value="{{$rpu->_id}}" />
          <table class="layout" style="width: 100%">
            <tr>
              <th style="width: 120px;">{{mb_label object=$rpu field="gemsa"}}</th>
              <td>{{mb_field object=$rpu field="gemsa" canNull=false defaultOption="&mdash; Code GEMSA" onchange="this.form.onsubmit();"}}</td>
            </tr>          
            
            {{if $conf.dPurgences.old_rpu == "1"}}
            <tr>
              <th>{{mb_label object=$rpu field="type_pathologie"}}</th>
              <td>{{mb_field object=$rpu field="type_pathologie" canNull=true defaultOption="&mdash; Type de pathologie" onchange="this.form.onsubmit();"}}</td>
            </tr>
            
            <tr>
              <th>{{mb_label object=$rpu field="urtrau"}}</th>
              <td>{{mb_field object=$rpu field="urtrau" canNull=true defaultOption="&mdash; Type de soins traumato" onchange="this.form.onsubmit();"}}</td>
            </tr>
            {{/if}}
            {{if $conf.dPurgences.old_rpu == "1"}}
            <tr>
              <th>{{mb_label object=$rpu field="urmuta"}}</th>
              <td>{{mb_field object=$rpu field="urmuta" canNull=true defaultOption="&mdash; Cause" onchange="this.form.onsubmit();"}}</td>
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
      </fieldset>
    </td>
  </tr>
  <tr>
    <td class="button" colspan="4">
      <fieldset>
        <legend>Actions</legend>
        {{if !$rpu->mutation_sejour_id}}
          {{if $conf.dPurgences.gerer_reconvoc == "1"}}
            <!-- Reconvocation => formulaire de creation de consultation avec champs pre-remplis -->
            <button id="button_reconvoc" class="new" {{if ($conf.dPurgences.hide_reconvoc_sans_sortie == "1") && !$sejour->sortie_reelle}}disabled="disabled"{{/if}} type="button" onclick="newConsultation({{$consult->_ref_plageconsult->chir_id}},{{$consult->patient_id}},{{$consult->_id}})">
              Reconvoquer
            </button>
          {{/if}}
          
          {{if $conf.dPurgences.gerer_hospi == "1"}} 
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
      </fieldset>
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