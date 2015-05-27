{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module=urgences script=contraintes_rpu ajax=1}}
{{mb_script module=dPurgences script=urgences ajax=true}}

<script type="text/javascript">

ContraintesRPU.contraintesProvenance  = {{$contrainteProvenance|@json}};
ContraintesRPU.contraintesDestination = {{$contrainteDestination|@json}};
ContraintesRPU.contraintesOrientation = {{$contrainteOrientation|@json}};

function redirect() {
  //document.location.href="?m=dPurgences&tab=vw_idx_rpu";
}

submitSejourWithSortieReelle = function (callback){
  submitFormAjax(getForm('editSortieReelle'), 'systemMsg', { onComplete : 
    callback 
  });
};

submitRPU = function (callback) {
  var oForm = document.editSortieAutorise;
  submitFormAjax(oForm, 'systemMsg', { onComplete : function(){ 
    reloadSortieReelle(); 
    if (callback) callback(); 
  }});
};

submitConsultWithChrono = function (chrono, callback) {
  var oForm = document.editFrmFinish;
  oForm.chrono.value = chrono;
  submitFormAjax(oForm, 'systemMsg', { onComplete : function(){ 
    reloadFinishBanner(); 
    if (callback) callback(); 
  }});
};

submitSejour = function (sejour_id) {
  var oForm = document.editSejour;
  return onSubmitFormAjax(oForm, { onComplete: function() {
    if (sejour_id != null) {
      reloadDiagnostic(sejour_id, 1);
    }
  }});
};

reloadSortieReelle = function () {
  var url = new Url("dPurgences", "ajax_sortie_reelle");
  url.addParam("sejour_id", getForm('editSortieReelle').elements.sejour_id.value);
  url.addParam("consult_id", getForm('ValidCotation').elements.consultation_id.value);
  url.requestUpdate('div_sortie_reelle');
};

submitSejRpuConsult = function () {
  if (checkForm(getForm("editRPU")) && checkForm(getForm("editRPUDest"))) {
    submitSejourWithSortieReelle(
      submitRPU.curry(
        submitConsultWithChrono.curry({{$consult|const:'TERMINE'}}, redirect)
      )
    ); 
  }
};

Fields = {
  init: function(mode_sortie) {
    ContraintesRPU.updateDestination(mode_sortie, true);
    ContraintesRPU.updateOrientation(mode_sortie, true); 
    $('etablissement_sortie_transfert').setVisible(mode_sortie == "transfert");
    $('service_sortie_transfert'      ).setVisible(mode_sortie == "mutation");
    $('commentaires_sortie'           ).setVisible(mode_sortie && mode_sortie != "normal");
    $('date_deces'                    ).setVisible(mode_sortie == "deces");
    var date_deces = getForm("editSejour")._date_deces;
    if (mode_sortie != "deces") {
      $V(date_deces, "", false);
      $V(date_deces.previous().down("input"), "", false);
      date_deces.removeClassName("notNull");
    }
  }
};

printDossier = function (id) {
  var url = new Url("dPurgences", "print_dossier");
  url.addParam("rpu_id", id);
  url.popup(700, 550, "RPU");
};

showEtabEntreeTransfert = function (mode) {
  // mode de transfert = transfert (7)
  $('service_entree_transfert').hide();
  $('etablissement_entree_transfert').hide();
  
  if (mode == 7) {
    $('etablissement_entree_transfert').show();
    $V(getForm('editRPU')._service_entree_transfert_id, '');
  } 
  else if (mode == 6) {
    $('service_entree_transfert').show();
    $V(getForm('editRPU')._etablissement_entree_id, '');
  }
};

</script>

{{assign var=sejour value=$rpu->_ref_sejour}}

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
                <table class="layout" style="width: 100%">
                  <tr>
                    <td style="width: 50%">{{mb_label object=$rpu field="diag_infirmier"}}</td>
                    <td style="width: 50%">{{mb_label object=$rpu field="pec_douleur"}}</td>
                  </tr>
                  <tr>
                    <td>
                      {{mb_field object=$rpu field="diag_infirmier" onchange="this.form.onsubmit();" class="autocomplete" form="editRPU"
                        aidesaisie="validate: function() { form.onsubmit() },
                                    validateOnBlur: 0,
                                    resetSearchField: 0,
                                    resetDependFields: 0"}}
                    </td>
                    <td>{{mb_field object=$rpu field="pec_douleur"    onchange="this.form.onsubmit();" class="autocomplete" form="editRPU"
                        aidesaisie="validate: function() { form.onsubmit() },
                                    validateOnBlur: 0,
                                    resetSearchField: 0,
                                    resetDependFields: 0"}}</td>
                  </tr>
                </table>
                
              </td>

            </tr>

            <tr>
              <th>{{mb_label object=$rpu field="_entree"}}</th>
              <td>{{mb_value object=$rpu field="_entree"}}</td>
            </tr>
            
            <tr>
              <th>{{mb_label object=$rpu field="ccmu"}}</th>
              <td>{{mb_field object=$rpu field="ccmu" canNull=false emptyLabel="Choose" onchange="this.form.onsubmit();"}}</td>
            </tr>
            
            <tr> 
              <th>{{mb_label object=$rpu field="_mode_entree"}}</th>
              <td>{{mb_field object=$rpu field="_mode_entree" emptyLabel="Choose" onchange="ContraintesRPU.updateProvenance(this.value, true); showEtabEntreeTransfert(this.value); this.form.onsubmit();"}}</td>
            </tr>
            
            <tr id="etablissement_entree_transfert" {{if $sejour->mode_entree != '7'}}style="display:none"{{/if}}>
              <th>{{mb_label object=$rpu field="_etablissement_entree_id"}}</th>
              <td>{{mb_field object=$rpu field="_etablissement_entree_id" form="editRPU" autocomplete="true,1,50,true,true" onchange="this.form.onsubmit();"}}</td> 
            </tr>
            
            <tr id="service_entree_transfert" {{if $sejour->mode_entree != '6'}}style="display:none"{{/if}}>
              <th>{{mb_label object=$rpu field="_service_entree_id"}}</th>
              <td>
                {{mb_field object=$rpu field="_service_entree_id" form="editRPU" autocomplete="true,1,50,true,true" onchange="this.form.onsubmit();"}}
                <input type="hidden" name="cancelled" value="0" />
              </td> 
            </tr>
            
            {{if $conf.dPurgences.old_rpu == "1"}}
            <tr>
              <th>{{mb_label object=$rpu field="urprov"}}</th>
              <td>{{mb_field object=$rpu field="urprov" emptyLabel="Choose" onchange="this.form.onsubmit();"}}</td>
            </tr>
            {{else}}
            <tr>
              <th>{{mb_label object=$rpu field="_provenance"}}</th>
              <td>{{mb_field object=$rpu field="_provenance" emptyLabel="Choose" onchange="this.form.onsubmit();"}}</td>
            </tr>
            {{/if}}    
            
            <tr>   
              <th>{{mb_label object=$rpu field="_transport"}}</th>
              <td>{{mb_field object=$rpu field="_transport" emptyLabel="Choose" onchange="this.form.onsubmit();"}}</td>
            </tr>
            
            <tr>
              <th>{{mb_label object=$rpu field="pec_transport"}}</th>
              <td>{{mb_field object=$rpu field="pec_transport" emptyLabel="Choose" onchange="this.form.onsubmit();"}}</td>
            </tr>
            
            <script type="text/javascript">
              Main.add(function(){
                var form = getForm("editRPU");
                
                if (form.elements._service_id) {
                  var box = form.elements.box_id;
                  box.observe("change", function(event){
                    var service_id = box.options[box.selectedIndex].up("optgroup").get("service_id");
                    $V(form.elements._service_id, service_id);
                  });
                }
              });
            </script>
    
            <tr>
              <th>{{mb_label object=$rpu field="box_id"}}</th>
              <td>
                <input type="hidden" name="_service_id" value="{{$rpu->_service_id}}" />
                {{mb_include module=dPhospi template="inc_select_lit" field=box_id selected_id=$rpu->box_id ajaxSubmit=true listService=$services}}
              </td>
            </tr>
  
          </table>
        </form>
      </fieldset>
      {{if "CAppUI::conf"|static_call:"dPurgences CRPU display_motif_sfmu":"CGroups-$g"}}
        <fieldset>
          <legend>Précisions sur l'entrée</legend>
          <form name="editRPUMotifsfmu" action="?" method="post" onsubmit="return onSubmitFormAjax(this);">
            <input type="hidden" name="dosql" value="do_rpu_aed" />
            <input type="hidden" name="m" value="dPurgences" />
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="rpu_id" value="{{$rpu->_id}}" />
            <table class="layout" style="width: 100%">
              <tr>
                {{assign var=notnull value=""}}
                {{if "CAppUI::conf"|static_call:"dPurgences CRPU gestion_motif_sfmu":"CGroups-$g" == "2"}}
                  {{assign var=notnull value="notNull"}}
                {{/if}}
                <th>{{mb_label object=$rpu field="motif_sfmu" class=$notnull}}</th>
                <td>{{mb_field object=$rpu field="motif_sfmu" class=$notnull
                  autocomplete="true,1,10,true,true" form="editRPUMotifsfmu" size=50 onchange="this.form.onsubmit();"}}
                  {{mb_script module=dPurgences script=CCirconstance ajax=true}}
                  <button type="button" class="search notext" onclick="CCirconstance.searchMotifSFMU(this.form)">
                    {{tr}}Search{{/tr}}
                  </button>
                </td>
              </tr>
            </table>
          </form>
        </fieldset>
      {{/if}}
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
                {{mb_field object=$rpu field="motif" onchange="this.form.onsubmit();" class="autocomplete" form="editRPUMotif"
                        aidesaisie="validate: function() { form.onsubmit() },
                                    validateOnBlur: 0,
                                    resetSearchField: 0,
                                    resetDependFields: 0"}}
                </td>
             </tr>
           </table>
         </form>
     
        {{mb_include module=urgences template=inc_form_sortie}}

      </fieldset>

      <fieldset>
        <legend>Précisions sur la sortie</legend>
        <form name="editRPUDest" action="?" method="post" onsubmit="return onSubmitFormAjax(this);">
          <input type="hidden" name="dosql" value="do_rpu_aed" />
          <input type="hidden" name="m" value="dPurgences" />
          <input type="hidden" name="del" value="0" />
          <input type="hidden" name="rpu_id" value="{{$rpu->_id}}" />
          <input type="hidden" name="sejour_id" value="{{$rpu->sejour_id}}" />
          <input type="hidden" name="_bind_sejour" value="1" />
          <table class="layout" style="width: 100%">
            <tr>
              <th style="width: 120px;">{{mb_label object=$rpu field="gemsa"}}</th>
              <td>{{mb_field object=$rpu field="gemsa" canNull=false emptyLabel="Choose" onchange="this.form.onsubmit();"}}</td>
            </tr>          
            
            {{if $conf.dPurgences.old_rpu == "1"}}
            <tr>
              <th>{{mb_label object=$rpu field="type_pathologie"}}</th>
              <td>{{mb_field object=$rpu field="type_pathologie" canNull=true emptyLabel="Choose" onchange="this.form.onsubmit();"}}</td>
            </tr>
            
            <tr>
              <th>{{mb_label object=$rpu field="urtrau"}}</th>
              <td>{{mb_field object=$rpu field="urtrau" canNull=true emptyLabel="Choose" onchange="this.form.onsubmit();"}}</td>
            </tr>
            {{/if}}
            {{if $conf.dPurgences.old_rpu == "1"}}
            <tr>
              <th>{{mb_label object=$rpu field="urmuta"}}</th>
              <td>{{mb_field object=$rpu field="urmuta" canNull=true emptyLabel="Choose" onchange="this.form.onsubmit();"}}</td>
            </tr>
            {{else}}
            <tr>
              <th>{{mb_label object=$rpu field="orientation"}}</th>
              <td>{{mb_field object=$rpu field="orientation" emptyLabel="Choose" onchange="this.form.onsubmit();"}}</td>
            </tr>
            <tr> 
              <th>{{mb_label object=$rpu field="_destination"}}</th>
              <td>{{mb_field object=$rpu field="_destination" emptyLabel="Choose" onchange="this.form.onsubmit();"}}</td> 
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
            <button id="button_reconvoc"
                    class="new singleclick" {{if ($conf.dPurgences.hide_reconvoc_sans_sortie == "1") && !$sejour->sortie_reelle}}disabled="disabled"{{/if}}
                    type="button" onclick="ContraintesRPU.checkObligatory('{{$rpu->_id}}', newConsultation.curry({{$consult->_ref_plageconsult->chir_id}},{{$consult->patient_id}},{{$consult->_id}}));">
              Reconvoquer
            </button>
          {{/if}}
          {{if @$modules.ecap->mod_active && ($conf.ecap.dhe.dhe_mode_choice == "new")}}
            {{mb_include module=ecap template=inc_button_non_prevue patient_id=$rpu->_patient_id}}
          {{else}}
            {{if $conf.dPurgences.gerer_hospi == "1" && ($conf.dPurgences.create_sejour_hospit == "0" || !$sejour->sortie_reelle)}} 
              {{assign var=label value=$conf.dPurgences.create_sejour_hospit|ternary:"simple":"transfert"}}
              <!-- Hospitalisation immediate, creation d'un sejour et transfert des actes dans le nouveau sejour -->
                <button class="new singleclick" type="button" onclick="ContraintesRPU.checkObligatory('{{$rpu->_id}}', Urgences.hospitalize.curry('{{$rpu->_id}}'))">Hospitaliser</button>
            {{/if}}
          {{/if}}
          
          <div id="uhcd_button" style="display: inline-block">
            {{mb_include module=dPurgences template=inc_uhcd}}
          </div>          
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
          {{mb_include module=urgences template=inc_sortie_reelle}}
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
{{if $sejour->mode_entree}}
  ContraintesRPU.updateProvenance("{{$rpu->_mode_entree}}");
{{/if}}

{{if $sejour->mode_sortie}}
ContraintesRPU.updateDestination("{{$sejour->mode_sortie}}");
ContraintesRPU.updateOrientation("{{$sejour->mode_sortie}}");
{{/if}}

</script>