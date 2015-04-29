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

  function searchMotif() {
    var form = getForm("editRPUtri");
    var url = new Url("urgences", "vw_search_motif");
    url.addParam('rpu_id'       , form.rpu_id.value);
    url.requestModal(600, 600);
  }
</script>
{{mb_script module=urgences script=motif ajax=true}}

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
              {{assign var=can_edit_pat value=false}}
              {{if $conf.dPurgences.allow_change_patient || !$sejour->_id || $app->user_type == 1}}
                {{assign var=can_edit_pat value=true}}
              {{/if}}
              <input type="text" name="_patient_view" style="width: 15em;" value="{{$patient->_view}}" 
                {{if $can_edit_pat}}
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
              <br/>
              <input type="text" name="_seek_patient" style="width: 13em; {{if !$can_edit_pat}}display:none;{{/if}}" placeholder="{{tr}}fast-search{{/tr}}" "autocomplete" onblur="$V(this, '')"  />

              <script>
                Main.add(function(){
                  {{if $can_edit_pat}}
                    var form = getForm("editRPUtri");
                    var url = new Url("system", "ajax_seek_autocomplete");
                    url.addParam("object_class", "CPatient");
                    url.addParam("field", "patient_id");
                    url.addParam("view_field", "_patient_view");
                    url.addParam("input_field", "_seek_patient");
                    url.autoComplete(form.elements._seek_patient, null, {
                      minChars: 3,
                      method: "get",
                      select: "view",
                      dropdown: false,
                      width: "300px",
                      afterUpdateElement: function(field,selected){
                        $V(field.form._patient_id, selected.getAttribute("id").split("-")[2]);
                        $V(field.form.elements._patient_view, selected.down('.view').innerHTML);
                        $V(field.form.elements._seek_patient, "");
                      }
                    });
                    Event.observe(form.elements._seek_patient, 'keydown', PatSelector.cancelFastSearch);
                  {{/if}}
                });
              </script>

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
            <th>{{mb_label object=$rpu field="box_id"}}</th>
            <td>
              {{mb_include module=dPhospi template="inc_select_lit" field=box_id selected_id=$rpu->box_id ajaxSubmit=0 listService=$services}}
              <button type="button" class="cancel opacity-60 notext" onclick="this.form.elements['box_id'].selectedIndex = 0"></button>
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
              <br/>
              <script type="text/javascript">
                Main.add(function(){
                  var form = getForm("editRPUtri");

                  if (form.elements._service_id) {
                    var box = form.elements.box_id;
                    box.observe("change", function(event){
                      var service_id = box.options[box.selectedIndex].up("optgroup").get("service_id");
                      $V(form.elements._service_id, service_id);
                    });
                  }
                });
              </script>
            </td>
          </tr>

          <tr>
            <th>{{mb_label object=$rpu field="diag_infirmier"}}</th>
            <td>
              {{mb_field object=$rpu field="diag_infirmier" class="autocomplete" form="editRPUtri"
              aidesaisie="validate: function() { form.onsubmit() },
                                        validateOnBlur: 0,
                                        resetSearchField: 0,
                                        resetDependFields: 0"}}
            </td>
          </tr>
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
      <fieldset style="width:48%;float:left;">
        {{mb_include module=urgences template=inc_tooltip_cte_ccmu}}
        <legend>Constantes</legend>
        <div id="constantes-tri" style="position: relative; height: 400px;"></div>
      </fieldset>

      <div style="float:left;width:48%;" id="form-echelle_tri">
        {{mb_include module=urgences template=vw_echelle_tri}}
      </div>
    </td>
    <td style="width:40%;">
      <div id="form-edit-complement">
        {{mb_include module=urgences template=inc_form_complement}}
      </div>
      <div id="form-question_motif" style="margin-bottom: 2px;">
        {{mb_include module=urgences template=inc_form_questions_motif}}
      </div>
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