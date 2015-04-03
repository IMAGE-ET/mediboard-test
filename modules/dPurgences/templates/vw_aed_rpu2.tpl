{{* Unique id pour le formulaire des constantes medicales *}}
{{assign var=unique_id value="-"|uniqid}}

<script>
  function requestInfoPatTri() {
    var oForm = getForm("editRPUtri");
    var iPatient_id = $V(oForm._patient_id);
    if(!iPatient_id){
      return false;
    }
    var url = new Url("dPpatients", "httpreq_get_last_refs");
    url.addParam("patient_id" , iPatient_id);
    url.addParam("is_anesth"  , 0);
    url.requestUpdate("infoPat");
    return true;
  }
  
  function refreshConstantesMedicalesTri(context_guid){
    if (context_guid) {
      var oForm = getForm("editRPUtri");
      var iPatient_id = $V(oForm._patient_id);
      var url = new Url('patients' , 'httpreq_vw_form_constantes_medicales');
      url.addParam("context_guid", context_guid);
      url.addParam("patient_id"   , iPatient_id);
      url.addParam('display_graph', 0);
      url.addParam('unique_id', '{{$unique_id}}');
      url.requestUpdate('constantes-tri');
      if (getForm("edit-constantes-medicales{{$unique_id}}")) {
        toggleAllGraphs();
      }
    }
  }
  
  function refreshAntecedentsPatient() {
    var oForm = getForm("editRPUtri");
    var iPatient_id = $V(oForm._patient_id);
    var url = new Url("dPcabinet", "httpreq_vw_list_antecedents");
    url.addParam("patient_id" , iPatient_id);
    url.addParam("_is_anesth" , "0");
    url.addParam("current_m"  , "dPurgences");
    url.addParam("sejour_id"  , "");
    url.addParam("chir_id"    , "{{$app->user_id}}");
    url.addParam("addform"    , "tri");
    url.requestUpdate('antecedentsanesth');
  }
  
  Main.add(function () {
    var sejour_id = '{{$rpu->sejour_id}}';
    if (sejour_id) {
      refreshConstantesMedicalesTri('CSejour-'+sejour_id);
      refreshAntecedentsPatient();
    }
    requestInfoPatTri();
  });
  function refreshComplement(colonne) {
    var form = getForm("editBox");
    var chapitre = form._chapitre_id.value;
    var motif = form._motif_id.value;
    if (colonne == 'chapitre') {
      motif = null;
    }
    else {
      chapitre = null;
    }
    var url = new Url("urgences", "ajax_form_complement");
    url.addParam('rpu_id'       , form.rpu_id.value);
    url.addParam('_chapitre_id' , chapitre);
    url.addParam('_motif_id'    , motif);
    url.requestUpdate('form-edit-complement');
  }
</script>

<table class="form">
  <tr>
    <td style="width:60%;">
      <form name="editRPUtri" action="?m={{$m}}{{if !$can->edit}}&amp;tab=vw_idx_rpu{{/if}}" method="post" onsubmit="return checkForm(this)">
        <input type="hidden" name="m" value="dPurgences" />
        <input type="hidden" name="dosql" value="do_rpu_aed" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="rpu_id" value="{{$rpu->_id}}" />
        <input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
        <input type="hidden" name="_annule" value="{{$rpu->_annule|default:"0"}}" />
        <input type="hidden" name="_bind_sejour" value="1" />
      
        <table class="form">
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
                {{mb_include module=mediusers template=inc_options_mediuser selected=$rpu->_responsable_id list=$listResponsables}}
              </select>
            </td>
          </tr>
          <tr>
            <th>{{mb_label object=$rpu field="_entree"}}</th>
            <td>{{mb_field object=$rpu field="_entree" form="editRPUtri" register=true}}</td>
          </tr>
          <tr>
            <th>
              <input type="hidden" name="_patient_id" class="{{$sejour->_props.patient_id}}" ondblclick="PatSelector.init()" value="{{$rpu->_patient_id}}"  onchange="requestInfoPatTri();refreshAntecedentsPatient();refreshConstantesMedicalesTri();" />
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
                  this.sForm = "editRPUtri";
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
          </tr>

          {{if "maternite"|module_active && @$modules.maternite->_can->read && (!$patient || $patient->sexe != "m")}}
            <tr>
              <th>{{tr}}CGrossesse{{/tr}}</th>
              <td>
                {{mb_include module=maternite template=inc_input_grossesse object=$sejour patient=$patient}}
              </td>
            </tr>
          {{/if}}

          <tr>
            <th>{{mb_label object=$rpu field="pec_douleur"}}</th>
            <td>
             {{mb_field object=$rpu field="pec_douleur" class="autocomplete" form="editRPUtri"
                            aidesaisie="validate: function() { form.onsubmit() },
                                        validateOnBlur: 0,
                                        resetSearchField: 0,
                                        resetDependFields: 0"}}
            </td>
          </tr>
          <tr>
            <td class="button" colspan="2">
              {{if $rpu->_id}}
                <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
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
                
                {{math assign=ecap_dhe equation="a * b" a='ecap'|module_active|strlen b=$current_group|idex:'ecap'|strlen}}
                {{if $ecap_dhe}}
                  {{mb_include module=ecap template=inc_button_dhe_urgence sejour_id=$sejour->_id}}
                {{/if}}
              {{else}}
                <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
              {{/if}}
            </td>
          </tr>
        </table> 
      </form>
      <div id="antecedentstri">
        {{assign var="current_m" value="dPurgences"}}
        {{assign var="_is_anesth" value="0"}}
        {{assign var=sejour_id value=""}}
        <table class="form">
          {{mb_include module=cabinet template=inc_ant_consult_trait addform="tri"}}
        </table>
      </div>
      <fieldset style="width:48%;float:left;">
        <legend>Constantes</legend>
        <div id="constantes-tri" style="position: relative; height: 400px;"></div>
      </fieldset>
      
      <div style="float:left;width:48%;" id="form-edit-complement">
        {{mb_include module=urgences template=inc_form_complement}}
      </div>
    </td>
    
    <td style="width:40%;">
      <table class="form">
        <tr>
          <th class="category" colspan="2">
            Dossier Patient
          </th>
        </tr>
        <tr>
          <td colspan="2" rowspan="3">
            <div id="antecedentsanesth">
              {{if !$rpu->_id}}
                <div class="empty">Aucun patient sélectionné</div>
              {{/if}}
            </div>
          </td>
        </tr>
      </table>
      
      <fieldSet>
        <legend>Infos patient</legend>
        <div class="text" id="infoPat">
          <div class="empty">Aucun patient sélectionné</div>
        </div>
      </fieldSet>
    </td>
  </tr>
</table>