{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module=cabinet script=file}}
{{mb_include module=files template=yoplet_uploader object=$sejour}}
{{assign var=gerer_circonstance value=$conf.dPurgences.gerer_circonstance}}

{{if !$group->service_urgences_id}}
  <div class="small-warning">{{tr}}dPurgences-no-service_urgences_id{{/tr}}</div>
{{else}}
  {{mb_script module="dPpatients" script="pat_selector"}}
  {{mb_script module="dPurgences" script="contraintes_rpu"}}
  
  {{if "dPprescription"|module_active}}
    {{mb_script module="dPprescription" script="prescription"}}
    {{mb_script module="dPprescription" script="element_selector"}}
    {{mb_script module="soins" script="plan_soins"}}
  {{/if}}
  
  {{if "dPmedicament"|module_active}}
    {{mb_script module="dPmedicament" script="medicament_selector"}}
    {{mb_script module="dPmedicament" script="equivalent_selector"}}
  {{/if}}
  
  {{mb_script module=compteRendu script=modele_selector}}
  {{mb_script module=compteRendu script=document}}

  <script type="text/javascript">
  
  ContraintesRPU.contraintesProvenance = {{$contrainteProvenance|@json}};
  
  function loadSuivi(sejour_id, user_id, cible, show_obs, show_trans, show_const) {
    if (!sejour_id) {
      return;
    }
    
    var urlSuivi = new Url("dPhospi", "httpreq_vw_dossier_suivi");
    urlSuivi.addParam("sejour_id", sejour_id);
    urlSuivi.addParam("user_id", user_id);
    urlSuivi.addParam("cible", cible);
    if (!Object.isUndefined(show_obs)) {
      urlSuivi.addParam("_show_obs", show_obs);
    }
    if (!Object.isUndefined(show_trans)) {
      urlSuivi.addParam("_show_trans", show_trans);
    }
    if (!Object.isUndefined(show_const)) {
      urlSuivi.addParam("_show_const", show_const);
    }
    urlSuivi.requestUpdate("dossier_suivi");
  }
  
  function submitSuivi(oForm) {
    sejour_id = oForm.sejour_id.value;
    submitFormAjax(oForm, 'systemMsg', { onComplete: function() { loadSuivi(sejour_id); } });
  }
  
  function refreshConstantesMedicales(context_guid) {
    if (context_guid) {
      var url = new Url("dPhospi", "httpreq_vw_constantes_medicales");
      url.addParam("context_guid", context_guid);
      url.requestUpdate("constantes");
    }
  }
  
  var constantesMedicalesDrawn = false;
  function refreshConstantesHack(sejour_id) {
    (function(){
      if (constantesMedicalesDrawn == false && $('constantes').visible() && sejour_id) {
        refreshConstantesMedicales('CSejour-'+sejour_id);
        constantesMedicalesDrawn = true;
      }
    }).delay(0.5);
  }

  function refreshConstantesHack(sejour_id) {
    (function(){
      if (constantesMedicalesDrawn == false && $('constantes').visible() && sejour_id) {
        refreshConstantesMedicales('CSejour-'+sejour_id);
        constantesMedicalesDrawn = true;
      }
    }).delay(0.5);
  }

  function showExamens(consult_id) {
    if (!consult_id) {
      return;
    }

    var url = new Url("dPurgences", "ajax_show_examens");
    url.addParam("consult_id", consult_id);
    url.requestUpdate("examens");
  }

  function loadDocItems(sejour_id, consult_id) {
    if (!sejour_id) {
      return;
    }

    var url = new Url("dPurgences", "ajax_show_doc_items");
    url.addParam("sejour_id" , sejour_id);
    url.addParam("consult_id", consult_id);
    url.requestUpdate("doc-items");
  }

  function loadActes(sejour_id) {
    if (!sejour_id) {
      return;
    }

    var url = new Url("dPurgences", "ajax_show_actes");
    url.addParam("sejour_id" , sejour_id);
    url.requestUpdate("actes");
  }  
  
  function cancelRPU() {
    var oForm = document.editRPU;
    var oElement = oForm._annule;
    
    if (oElement.value == "0") {
      if (confirm("Voulez-vous vraiment annuler le dossier ?")) {
        oElement.value = "1";
        oForm.submit();
        return;
      }
    }
        
    if (oElement.value == "1") {
      if (confirm("Voulez-vous vraiment rétablir le dossier ?")) {
        oElement.value = "0";
        oForm.submit();
        return;
      }
    }
  }
  
  {{if $isPrescriptionInstalled}}
    function reloadPrescription(prescription_id){
      Prescription.reloadPrescSejour(prescription_id, '','', '1', null, null, null,'');
    }
  {{/if}}

  function changeModeEntree(mode_entree) {
    loadTransfert(mode_entree); 
    loadServiceMutation(mode_entree);
  }
  
  function loadTransfert(mode_entree){
    $('etablissement_entree_transfert').setVisible(mode_entree == 7);
  }
  
  function loadServiceMutation(mode_entree){
    $('service_entree_mutation').setVisible(mode_entree == 6);
  }
  
  function printDossier(id) {
    var url = new Url("dPurgences", "print_dossier");
    url.addParam("rpu_id", id);
    url.popup(700, 550, "RPU");
  }
 
  function loadResultLabo(sejour_id) {
    var url = new Url("dPImeds", "httpreq_vw_sejour_results");
    url.addParam("sejour_id", sejour_id);
    url.requestUpdate('Imeds');
  }

  function requestInfoPat() {
    var oForm = getForm("editRPU");
    var iPatient_id = $V(oForm._patient_id);
    if(!iPatient_id){
      return false;
    }
    var url = new Url("dPpatients", "httpreq_get_last_refs");
    url.addParam("patient_id", iPatient_id);
    url.addParam("is_anesth", 0);
    url.requestUpdate("infoPat");
  }
  
  function printEtiquettes() {
    var nb_printers = {{$nb_printers|@json}};
    if (nb_printers > 0) {
      var url = new Url('dPcompteRendu', 'ajax_choose_printer');
      url.addParam('mode_etiquette', 1);
      url.addParam('object_class', '{{$rpu->_class}}');
      url.addParam('object_id', '{{$rpu->_id}}');
      url.requestModal(400);
    }
    else {
      getForm('download_etiq').submit();
    }
  }
  
  showDossierSoins = function(sejour_id, date, default_tab){
    $('dossier_sejour').update("");
    var url = new Url("soins", "ajax_vw_dossier_sejour");
    url.addParam("sejour_id", sejour_id);
    if(default_tab){
      url.addParam("default_tab", default_tab);
    }
    url.requestUpdate($('dossier_sejour'));
    modalWindow = modal($('dossier_sejour'));
  }
  
  Main.add(function () {
    {{if $rpu->_id && $can->edit}}
      if (window.DossierMedical){
        DossierMedical.reloadDossierPatient();
      }
      var tab_sejour = Control.Tabs.create('tab-dossier');
      loadDocItems('{{$rpu->sejour_id}}', '{{$rpu->_ref_consult->_id}}');
    {{/if}}
    
    {{if "forms"|module_active}}
      if ($("ex-forms-rpu")) {
        ExObject.loadExObjects("{{$rpu->_class}}", "{{$rpu->_id}}", "ex-forms-rpu", 0.5);
      }
    {{/if}}
    
    if (document.editAntFrm){
      document.editAntFrm.type.onchange();
    }
  });
  
  </script>

  <form name="download_etiq" style="display: none;" action="?" target="_blank" method="get" class="prepared">
    <input type="hidden" name="m" value="dPhospi" />
    <input type="hidden" name="a" value="print_etiquettes" />
    <input type="hidden" name="object_id" value="{{$rpu->_id}}" />
    <input type="hidden" name="object_class" value="{{$rpu->_class}}" />
    <input type="hidden" name="suppressHeaders" value="1" />
    <input type="hidden" name="dialog" value="1" />
  </form>

  <form name="editRPU" action="?m={{$m}}{{if !$can->edit}}&amp;tab=vw_idx_rpu{{/if}}" method="post" onsubmit="return checkForm(this)">
  
  <input type="hidden" name="m" value="dPurgences" />
  <input type="hidden" name="dosql" value="do_rpu_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="rpu_id" value="{{$rpu->_id}}" />
  <input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
  <input type="hidden" name="_annule" value="{{$rpu->_annule|default:"0"}}" />
  
  <input type="hidden" name="_bind_sejour" value="1" />
  <table class="form">
    <colgroup>
      <col class="narrow" />
      <col style="width: 50%" />
      <col class="narrow" />
      <col style="width: 50%" />
    </colgroup>
    
    <tr>
      {{if $rpu->_id}}
      <th class="title modify" colspan="4">
        
        {{mb_include module=system template=inc_object_notes      object=$sejour}}
        {{mb_include module=system template=inc_object_idsante400 object=$rpu}}
        {{mb_include module=system template=inc_object_history    object=$rpu}}
  
        <a class="action" style="float: right;" title="Modifier uniquement le sejour" href="?m=dPplanningOp&amp;tab=vw_edit_sejour&amp;sejour_id={{$sejour->_id}}">
          <img src="images/icons/edit.png" alt="modifier" />
        </a>
  
        {{tr}}CRPU-title-modify{{/tr}}
        '{{$rpu}}'
        {{mb_include module=planningOp template=inc_vw_numdos nda_obj=$sejour}}
      </th>
      {{else}}
      <th class="title" colspan="4">
        {{tr}}CRPU-title-create{{/tr}}
        {{if $sejour->_NDA}}
          pour le dossier
          {{mb_include module=planningOp template=inc_vw_numdos nda_obj=$sejour}}
        {{/if}}
      </th>
      {{/if}}
    </tr>
  
    {{if $rpu->_annule}}
    <tr>
      <th class="category cancelled" colspan="4">
      {{tr}}CRPU-_annule{{/tr}}
      </th>
    </tr>
    {{/if}}
    
    <tr>
      <th>{{mb_label object=$rpu field="_responsable_id"}}</th>
      <td>
        <select name="_responsable_id" style="width: 15em;" class="{{$rpu->_props._responsable_id}}">
          <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
          {{foreach from=$listResponsables item=curr_user}}
          <option value="{{$curr_user->_id}}" class="mediuser" style="border-color: #{{$curr_user->_ref_function->color}}" {{if $curr_user->_id == $rpu->_responsable_id}}selected="selected"{{/if}}>
            {{$curr_user->_view}}
          </option>
          {{/foreach}}
        </select>
      </td>
      
      <th>{{mb_label object=$rpu field="_mode_entree"}}</th>
      <td>{{mb_field object=$rpu field="_mode_entree" style="width: 15em;" emptyLabel="Choose" onchange="ContraintesRPU.updateProvenance(this.value, true); changeModeEntree(this.value)"}}</td>
    </tr>
    
    <tr>
      <th>{{mb_label object=$rpu field="_entree"}}</th>
      <td>{{mb_field object=$rpu field="_entree" form="editRPU" register=true}}</td>
      
      <th></th>
      <td>
        <input type="hidden" name="group_id" value="{{$g}}" />
        <div id="etablissement_entree_transfert" {{if !$rpu->_etablissement_entree_id}}style="display:none"{{/if}}>
          {{mb_field object=$rpu field="_etablissement_entree_id" form="editRPU" style="width: 12em;" autocomplete="true,1,50,true,true"}}
        </div>
        <div id="service_entree_mutation" {{if !$rpu->_service_entree_id}}style="display:none"{{/if}}>
          {{mb_field object=$rpu field="_service_entree_id" form="editRPU" autocomplete="true,1,50,true,true"}}
          <input type="hidden" name="cancelled" value="0" />
        </div>
      </td>  
    </tr>
  
    <tr>
      <th>
        <input type="hidden" name="_patient_id" class="{{$sejour->_props.patient_id}}" ondblclick="PatSelector.init()" value="{{$rpu->_patient_id}}"  onchange="requestInfoPat();" />
        {{mb_label object=$rpu field="_patient_id"}}
      </th>
      <td>
        <input type="text" name="_patient_view" style="width: 15em;" value="{{$patient->_view}}" 
          {{if $conf.dPurgences.allow_change_patient || !$sejour->_id || $app->user_type == 1}} 
            onfocus="PatSelector.init()" 
          {{/if}}
        readonly="readonly" />
        
        {{if $conf.dPurgences.allow_change_patient || !$sejour->_id || $app->user_type == 1}} 
          <button type="button" class="search notext" onclick="PatSelector.init()">{{tr}}Search{{/tr}}</button>
        {{/if}}
        <script type="text/javascript">
          PatSelector.init = function(){
            this.sForm = "editRPU";
            this.sId   = "_patient_id";
            this.sView = "_patient_view";
            this.pop();
          }
        </script>
        {{if $patient->_id}}
        <button id="button-edit-patient" type="button" class="edit notext"
          onclick="location.href='?m=dPpatients&amp;tab=vw_edit_patients&amp;patient_id='+this.form._patient_id.value" 
        >
          {{tr}}Edit{{/tr}}
        </button>
        {{/if}}
        
      </td>
      
      {{if $conf.dPurgences.old_rpu == "1"}}
        <th>{{mb_label object=$rpu field="urprov"}}</th>
        <td>{{mb_field object=$rpu field="urprov" emptyLabel="Choose" style="width: 15em;"}}</td>
      {{else}}
        <th>{{mb_label object=$rpu field="_provenance"}}</th>
        <td>{{mb_field object=$rpu field="_provenance" emptyLabel="Choose" style="width: 15em;"}}</td>
      {{/if}}
    </tr>
    
    <tr>
      {{if $can->edit}}
        <th>{{mb_label object=$rpu field="ccmu"}}</th>
        <td>{{mb_field object=$rpu field="ccmu" emptyLabel="Choose" style="width: 15em;"}}</td>
      {{else}}
        <th></th>
        <td></td>
      {{/if}}
      
      <th>{{mb_label object=$rpu field="_transport"}}</th>
      <td>{{mb_field object=$rpu field="_transport" emptyLabel="Choose" style="width: 15em;"}}</td>
    </tr>
    
     <!-- Selection du service -->
    <tr>
      <th></th>
      <td></td>
      <th>{{mb_label object=$rpu field="pec_transport"}}</th>
      <td>{{mb_field object=$rpu field="pec_transport" emptyLabel="Choose" style="width: 15em;"}}</td>
    </tr>
    
    {{if $conf.dPurgences.display_regule_par}}
      <tr>
        <th></th>
        <td></td>
        <th>{{mb_label object=$rpu field="regule_par"}}</th>
        <td>{{mb_field object=$rpu field="regule_par" emptyLabel="Choose" typeEnum=radio}}</td>
      </tr>
    {{/if}}
    
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
      <td style="vertical-align: middle;">
        {{mb_include module=dPhospi template="inc_select_lit" field=box_id selected_id=$rpu->box_id ajaxSubmit=0 listService=$services}}
        <button type="button" class="cancel opacity-60 notext" onclick="this.form.elements['box_id'].selectedIndex=0"></button>
        &mdash; {{tr}}CRPU-_service_id{{/tr}} :
        {{if $services|@count == 1}}
          {{assign var=first_service value=$services|@reset}}
          {{$first_service->_view}}
        {{else}}
        <select name="_service_id" class="{{$sejour->_props.service_id}}">
          <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
          {{foreach from=$services item=_service}}
          <option value="{{$_service->_id}}" {{if "Urgences" == $_service->nom}} selected="selected" {{/if}}>
            {{$_service->_view}}
          </option>
          {{/foreach}}
        </select>
        {{/if}}
      </td>
      <th>{{mb_label object=$rpu field="date_at"}}</th>
      <td>{{mb_field object=$rpu field="date_at" form="editRPU" register=true}}</td>
    </tr>
  
    {{if $can->edit}}
      {{if $gerer_circonstance}}
        <tr>
          <th>{{mb_label object=$rpu field="circonstance"}}</th>
          <td>
            <input type="hidden" name="circonstance" value="{{$rpu->circonstance}}" />
            <input type="text" name="_keywords_circonstance" value="{{if $orumip_active}}{{$rpu->_libelle_circonstance}}{{else}}{{$rpu->circonstance}}{{/if}}" class="autocomplete"/>
            <br/>
            <span id="libelle_circonstance" onclick="emptyCirconstance()" style="width: 150px;">{{$rpu->_libelle_circonstance}}</span>
            <script type="text/javascript">
              function emptyCirconstance() {
                var oForm = getForm("editRPU");
                $V(oForm.circonstance, "");
                $V(oForm._keywords_circonstance, "");
                $("libelle_circonstance").update();
              }
              Main.add(function(){
                var url = new Url("dPurgences", "ajax_circonstance_autocomplete");
                url.autoComplete(getForm("editRPU")._keywords_circonstance, '', {
                  minChars: 1,
                  dropdown: true,
                  width: "250px",
                  select: "view",
                  afterUpdateElement: function(input, selected) {
                    $V(getForm("editRPU").circonstance, selected.select(".code")[0].innerHTML);
                    $("libelle_circonstance").innerHTML = selected.select(".libelle_circonstance")[0].innerHTML;
                  }
                });
              });
            </script>
          </td>
          <th></th>
          <td></td>
        </tr>
      {{/if}}
      
      {{if $rpu->motif_entree}}
      <tr>
        <th>{{mb_label object=$rpu field="motif_entree"}}</th>
        <td>{{mb_value object=$rpu field="motif_entree"}}</td>
        <th></th>
        <td></td>
      </tr>  
      {{/if}}
      
      <tr>
        <th>{{mb_label object=$rpu field="diag_infirmier"}}</th> 
        <td>
          {{mb_field object=$rpu field="diag_infirmier" class="autocomplete" form="editRPU"
                        aidesaisie="validate: function() { form.onsubmit() },
                                    validateOnBlur: 0,
                                    resetSearchField: 0,
                                    resetDependFields: 0"}}
        </td>
        <th>{{mb_label object=$rpu field="pec_douleur"}}</th>
        <td>
         {{mb_field object=$rpu field="pec_douleur" class="autocomplete" form="editRPU"
                        aidesaisie="validate: function() { form.onsubmit() },
                                    validateOnBlur: 0,
                                    resetSearchField: 0,
                                    resetDependFields: 0"}}
        </td>
      </tr>
    {{else}}
      <th>{{mb_label object=$rpu field="motif_entree"}}</th>
      <td>
         {{mb_field object=$rpu field="motif_entree" class="autocomplete" form="editRPU"
                        aidesaisie="validate: function() { form.onsubmit() },
                                    validateOnBlur: 0,
                                    resetSearchField: 0,
                                    resetDependFields: 0"}}
      </td>
      <th></th>
      <td></td>
    {{/if}}
    
    <tr>
      <td class="button" colspan="4">
        {{if $rpu->_id}}
          <button class="modify" type="submit">Valider</button>
          {{mb_ternary var=annule_text test=$sejour->annule value="Rétablir" other="Annuler"}}
          {{mb_ternary var=annule_class test=$sejour->annule value="change" other="cancel"}}
          
          <button class="{{$annule_class}}" type="button" onclick="cancelRPU();">
            {{$annule_text}}
          </button>
          
          {{if $can->admin}}
            <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'l\'urgence ',objName:'{{$rpu->_view|smarty:nodefaults|JSAttribute}}'})">
              {{tr}}Delete{{/tr}}
            </button>
          {{/if}}
          
          <button type="button" class="print" onclick="printDossier({{$rpu->_id}})">
            {{tr}}Print{{/tr}} dossier
          </button>
          
          <button type="button" class="print" onclick="printEtiquettes();">
            {{tr}}CModeleEtiquette.print_labels{{/tr}}
          </button>
              
          <a class="button new" href="?m=dPurgences&amp;tab=vw_aed_rpu&amp;rpu_id=0">
            {{tr}}CRPU-title-create{{/tr}}
          </a>
          
          {{assign var=ecap_active value='ecap'|module_active}} 
          {{assign var=ecap_idex   value=$current_group|idex:'ecap'}} 
          {{math assign=ecap_dhe equation="a * b" a=$ecap_active|strlen b=$ecap_idex|strlen}}
          {{if $ecap_dhe}}
            {{mb_include module=ecap template=inc_button_dhe_urgence sejour_id=$sejour->_id}}
          {{/if}}
        {{else}}
          <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
        {{/if}}
      </td>
    </tr>
    {{if !$rpu->_id}}
    <tr>
      <td colspan="4">
        <fieldSet>
          <legend>Infos patient</legend>
          <div class="text" id="infoPat">
            <div class="empty">Aucun patient sélectionné</div>
          </div>
        </fieldSet>
      </td>
    </tr>
    {{/if}}
    
  </table>
  
  </form>
  
  <!-- Dossier Médical du patient -->
  {{if $rpu->_id && $can->edit}}
    <table width="100%" class="tbl">
      <tr>
        <th class="category">Attentes</th>
        <th class="category">Prise en charge médicale</th>
      </tr>
    
      <tr>
        <td style="width: 60%">
          {{include file="inc_vw_rpu_attente.tpl"}}
        </td>
        <td class="button {{if $sejour->type != "urg"}}arretee{{/if}}">
          {{include file="inc_pec_praticien.tpl"}}
        </td>
      </tr>
    </table>
    
    {{assign var=consult value=$rpu->_ref_consult}}
  
    {{if $rpu->mutation_sejour_id}}
      <div class="small-info">
        Une mutation du séjour a été effectuée, il est possible de visualiser le dossier de soins en cliquant sur le bouton suivant
        <button type="button" class="search" onclick="showDossierSoins('{{$rpu->mutation_sejour_id}}');">Dossier de soins</button>
      </div>
    {{else}}
      <ul id="tab-dossier" class="control_tabs">
        <li><a href="#antecedents">Antécédents &amp; Traitements</a></li>
        
        {{if $isPrescriptionInstalled && $modules.dPprescription->_can->read && !$conf.dPprescription.CPrescription.prescription_suivi_soins}}
          <li {{if $rpu->sejour_id}} onmouseup="Prescription.reloadPrescSejour('', '{{$rpu->sejour_id}}','', '', null, null, null,'');" {{/if}}><a href="#prescription_sejour">Prescription</a></li>
          <li {{if $rpu->sejour_id}} onmouseup="PlanSoins.loadTraitement('{{$rpu->sejour_id}}',null,'','administration');"{{/if}}><a href="#dossier_traitement">Suivi de soins</a></li>
        {{else}}
          <li onmouseup="loadSuivi({{$rpu->sejour_id}});"><a href="#dossier_suivi">Suivi de soins</a></li>
        {{/if}}
        
        
        <li onmouseup="refreshConstantesHack('{{$rpu->sejour_id}}')"><a href="#constantes">{{tr}}CPatient.surveillance{{/tr}}</a></li>
        
        {{if "forms"|module_active}}
          <li><a href="#ex-forms-rpu">Formulaires</a></li>
        {{/if}}
        
        <li onmouseup="showExamens('{{$consult->_id}}')"><a href="#examens">Dossier médical</a></li>
        {{if $app->user_prefs.ccam_sejour == 1 }}
          <li onmouseup="loadActes('{{$rpu->sejour_id}}')"><a href="#actes">Cotation infirmière</a></li>
        {{/if}}
        {{if @$modules.dPImeds->mod_active}}
          <li onmouseup="loadResultLabo('{{$rpu->sejour_id}}')"><a href="#Imeds">Labo</a></li>
        {{/if}}
        <li onmouseup="loadDocItems('{{$rpu->sejour_id}}', '{{$consult->_id}}')"><a href="#doc-items">Documents</a></li>
      </ul>
      
      <hr class="control_tabs" />
      
      <div id="antecedents">
        {{assign var="current_m" value="dPurgences"}}
        {{assign var="_is_anesth" value="0"}}
        {{assign var=sejour_id value=""}}
        
        {{mb_include module=cabinet template=inc_ant_consult chir_id=$app->user_id}}
      </div>
      
      <div id="constantes" style="display:none"></div>
      <div id="ex-forms-rpu" style="display: none;"></div>
      
      <div id="examens"    style="display:none">
        <div class="small-info">
          Aucune prise en charge médicale
        </div>
      </div>
      
      {{if $app->user_prefs.ccam_sejour == 1 }}
      <div id="actes" style="display: none;"> </div>    
      {{/if}}
      
      {{if $isPrescriptionInstalled && $modules.dPprescription->_can->read && !$conf.dPprescription.CPrescription.prescription_suivi_soins}}
      <div id="prescription_sejour" style="display: none;">
        <div class="small-info">
          Aucune prescription
        </div>
      </div>
      <div id="dossier_traitement">
        <div class="small-info">
          Aucun plan de soins
        </div>
      </div>  
      {{else}}
        <div id="dossier_suivi" style="display:none"></div>
      {{/if}}
      
      {{if @$modules.dPImeds->mod_active}}
      <div id="Imeds" style="display: none;">
        <div class="small-info">
          Veuillez sélectionner un séjour dans la liste de gauche pour pouvoir
          consulter les résultats de laboratoire disponibles pour le patient concerné.
        </div>
      </div>
      {{/if}}
        
      <div id="doc-items" style="display: none;"></div>
    {{/if}}
  {{/if}}
  
  
  {{if $sejour->mode_entree}}
  <script type="text/javascript">
    // Lancement des fonctions de contraintes entre les champs
    ContraintesRPU.updateProvenance("{{$sejour->mode_entree}}");
  </script>
  {{/if}}
{{/if}}

<div id="dossier_sejour" style="width: 95%; height: 90%; overflow: auto; display: none;"></div>  