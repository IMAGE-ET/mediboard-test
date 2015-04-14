<!-- $Id$ -->

{{mb_script module="dPplanningOp" script="ccam_selector"}}
{{mb_script module="dPplanningOp" script="plage_selector"}}
{{mb_script module="dPpatients"   script="pat_selector"}}
{{mb_script module="dPplanningOp"   script="operation"}}
{{assign var=multi_label value="dPplanningOp COperation multiple_label"|conf:"CGroups-$g"}}

<script>
Main.add(function(){
   Document.refreshList('{{$op->_id}}');
});

PlageOpSelector.init = function(){
  if(!(checkChir() && checkDuree())) return;

  var oOpForm     = document.editOp;
  var oSejourForm = document.editSejour;
  
  this.sPlage_id         = "plageop_id";
  this.sSalle_id         = "salle_id";
  this.sDate             = "_date";
  this.sType             = "type";
  this.sPlaceAfterInterv = "_place_after_interv_id";
  this.sHoraireVoulu     = "_horaire_voulu";
  
  
  this.s_hour_entree_prevue = "_hour_entree_prevue";
  this.s_min_entree_prevue  = "_min_entree_prevue";
  this.s_date_entree_prevue = "_date_entree_prevue";
  
  this.pop(oOpForm.chir_id.value, oOpForm._time_op.value, oSejourForm.group_id.value,
           oOpForm.operation_id.value);
};

CCAMSelector.init = function(){
  this.sForm  = "editOp";
  this.sView  = "_codes_ccam";
  this.sChir  = "chir_id";
  this.sClass = "_class";
  this.pop();
};

addBesoins = function(types_ressources_ids) {
  var form = getForm("addBesoinOp");
  types_ressources_ids = types_ressources_ids.split(",");

  types_ressources_ids.each(function(type_ressource_id) {
    $V(form.type_ressource_id, type_ressource_id);
    onSubmitFormAjax(form, function() {
    // C'est après l'ajout du dernier besoin que l'on actualise la couleur du bouton Matériel
      if (types_ressources_ids.indexOf(type_ressource_id) == (types_ressources_ids.length - 1)) {
       checkRessources('{{$op->_id}}');
      }
    });
  });
};

refreshFunction = function(chir_id) {
  var url = new Url("dPcabinet", "ajax_refresh_secondary_functions");
  url.addParam("chir_id"   , chir_id);
  url.addParam("field_name", "secondary_function_id");
  url.addParam("type_onchange", "sejour");
  url.requestUpdate("secondary_functions", {onSuccess:
  function(request) {
    $("secondary_functions_easy").update(request.responseText);
  }});
}

</script>

<form name="addBesoinOp" method="post">
  <input type="hidden" name="m" value="dPbloc" />
  <input type="hidden" name="dosql" value="do_besoin_ressource_aed" />
  <input type="hidden" name="besoin_ressource_id" />
  <input type="hidden" name="operation_id" value="{{$op->_id}}" />
  <input type="hidden" name="type_ressource_id" />
</form>

<form name="editOp" action="?m={{$m}}" method="post" onsubmit="return checkFormOperation()">

<input type="hidden" name="dosql" value="do_planning_aed" />
<input type="hidden" name="m" value="dPplanningOp" />
<input type="hidden" name="del" value="0" />
{{mb_key object=$op}}

{{if $dialog}}
  {{if $op->plageop_id || !$modurgence}}
    <input type="hidden" name="postRedirect" value="m=planningOp&a=vw_edit_planning&dialog=1" />
  {{else}}
    <input type="hidden" name="postRedirect" value="m=planningOp&a=vw_edit_urgence&dialog=1" />
  {{/if}}
{{/if}}

{{if $op->_id && $op->_ref_sejour->sortie_reelle && !$modules.dPbloc->_can->edit}}
<!-- <input type="hidden" name="_locked" value="1" /> -->
{{/if}}
{{mb_field object=$op field="sejour_id" hidden=1 canNull=true}}
{{mb_field object=$op field="commande_mat" hidden=1}}
{{mb_field object=$op field="rank" hidden=1}}
{{mb_field object=$op field="consult_related_id" hidden=1}}
{{*mb_field object=$op field="_horaire_voulu" hidden=0*}}
<input type="hidden" name="_horaire_voulu" value="{{$op->_horaire_voulu}}" />
<input type="hidden" name="annulee" value="{{$op->annulee|default:"0"}}" />
<input type="hidden" name="salle_id" value="{{$op->salle_id}}" />
<input type="hidden" name="examen_operation_id" value="{{$op->examen_operation_id}}" />

<!-- Form Fields -->
<input type="hidden" name="_group_id" value="{{$sejour->group_id}}" />
<input type="hidden" name="_class" value="COperation" />
<input type="hidden" name="_protocole_prescription_anesth_id" value="" />
<input type="hidden" name="_protocole_prescription_chir_id" value="" />
{{mb_field object=$op field="_count_actes" hidden=1}}
<input type="hidden" name="_place_after_interv_id" value="" />
{{mb_field object=$op field=duree_preop form=editOp hidden=1}}
{{if $conf.dPbloc.CPlageOp.systeme_materiel == "expert"}}
  <input type="hidden" name="_types_ressources_ids"
    onchange="{{if $op->_id}}addBesoins(this.value);{{else}}synchronizeTypes($V(this));{{/if}}"/>
{{/if}}

<table class="form">
  <tr>
    <th class="category" colspan="3">
      {{if $op->operation_id}}
        {{mb_include module=system template=inc_object_idsante400 object=$op}}
        {{mb_include module=system template=inc_object_history    object=$op}}
        {{mb_include module=system template=inc_object_notes      object=$op}}
      {{/if}}
      {{tr}}COperation-msg-informations{{/tr}}
    </th>
  </tr>
  
  {{if $op->annulee == 1}}
  <tr>
    <th class="category cancelled" colspan="3">{{tr}}COperation-annulee{{/tr}}</th>
  </tr>
  {{/if}}

  <tr>
    <th>
      {{mb_label object=$op field="chir_id"}}
    </th>
    <td colspan="2">
      <script>
        Main.add(function () {
          var form = getForm("editOp");
          selectPraticien(form.chir_id, form.chir_id_view);
          selectPraticien(form.chir_2_id, form.chir_2_id_view);
          selectPraticien(form.chir_3_id, form.chir_3_id_view);
          selectPraticien(form.chir_4_id, form.chir_4_id_view);
        });
      </script>
      {{mb_field object=$op field="chir_id" hidden=hidden value=$chir->_id onchange="synchroPrat(); Value.synchronize(this); removePlageOp(true); refreshFunction(this.value)"}}
      <input type="text" name="chir_id_view" class="autocomplete" style="width:15em;" onchange="Value.synchronize(this);"
             value="{{if $chir->_id}}{{$chir->_view}}{{/if}}"  placeholder="&mdash; Choisir un praticien"/>
      <button type="button" onclick="toggleOtherPrats()" title="{{tr}}Add{{/tr}}"
        class="notext {{if $op->chir_2_id || $op->chir_3_id || $op->chir_4_id}}up{{else}}down{{/if}}"></button>
      <input name="_limit_search_op" class="changePrefListUsers" type="checkbox"
             {{if $app->user_prefs.useEditAutocompleteUsers}}checked{{/if}}
             onchange="changePrefListUsers(this);"
             title="Limiter la recherche des praticiens" />
    </td>
  </tr>
  {{if $conf.dPplanningOp.COperation.show_secondary_function && !$op->_id}}
    <tr>
      <th>
        {{mb_label class=CMediusers field=function_id}}
      </th>
      <td id="secondary_functions" colspan="2">
        {{mb_include module=dPcabinet template=inc_refresh_secondary_functions chir=$chir type_onchange="sejour"}}
      </td>
    </tr>
  {{/if}}
  <tr class="other_prats" {{if !$op->chir_2_id && !$op->chir_3_id && !$op->chir_4_id}}style="display: none"{{/if}}>
    <th>
      {{mb_label object=$op field="chir_2_id"}}
    </th>
    <td colspan="2">
      {{mb_field object=$op field="chir_2_id" hidden=hidden value=$op->chir_2_id onchange="Value.synchronize(this);"}}
      <input type="text" name="chir_2_id_view" class="autocomplete" style="width:15em;"
             value="{{if $op->chir_2_id}}{{$op->_ref_chir_2->_view}}{{/if}}" placeholder="&mdash; Choisir un chirurgien"/>
      <button type="button" class="cancel notext" onclick="$V(this.form.chir_2_id, '');$V(this.form.chir_2_id_view, '');"></button>
    </td>
  </tr>
  <tr class="other_prats" {{if !$op->chir_2_id && !$op->chir_3_id && !$op->chir_4_id}}style="display: none"{{/if}}>
    <th>
      {{mb_label object=$op field="chir_3_id"}}
    </th>
    <td colspan="2">
      {{mb_field object=$op field="chir_3_id" hidden=hidden value=$op->chir_3_id onchange="Value.synchronize(this);"}}
      <input type="text" name="chir_3_id_view" class="autocomplete" style="width:15em;"
             value="{{if $op->chir_3_id}}{{$op->_ref_chir_3->_view}}{{/if}}" placeholder="&mdash; Choisir un chirurgien"/>
      <button type="button" class="cancel notext" onclick="$V(this.form.chir_3_id, '');$V(this.form.chir_3_id_view, '');"></button>
    </td>
  </tr>
  <tr class="other_prats" {{if !$op->chir_2_id && !$op->chir_3_id && !$op->chir_4_id}}style="display: none"{{/if}}>
    <th>
      {{mb_label object=$op field="chir_4_id"}}
    </th>
    <td colspan="2">
      {{mb_field object=$op field="chir_4_id" hidden=hidden value=$op->chir_4_id onchange="Value.synchronize(this);"}}
      <input type="text" name="chir_4_id_view" class="autocomplete" style="width:15em;"
             value="{{if $op->chir_4_id}}{{$op->_ref_chir_4->_view}}{{/if}}" placeholder="&mdash; Choisir un chirurgien"/>
      <button type="button" class="cancel notext" onclick="$V(this.form.chir_4_id, '');$V(this.form.chir_4_id_view, '');"></button>
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$op field="libelle"}}</th>
    <td colspan="2">
      <table class="main layout">
        <tr>
          <td style="padding: 0;">
            {{mb_field object=$op field="libelle" form="editOp"
                autocomplete="true,1,50,true,true" inputWidth="100%"
                onblur="\$V(getForm('editOpEasy').libelle, \$V(getForm('editOp').libelle));"}}
          </td>
          <td style="padding: 0;" class="narrow">
            <button class="search notext" type="button" onclick="ProtocoleSelector.init()">
              Choisir un protocole
            </button>

            {{if $multi_label}}
              <button class="edit notext" type="button" onclick="LiaisonOp.edit('{{$op->_id}}');"></button>
            {{/if}}
          </td>
        </tr>
      </table>

      {{mb_include module=planningOp template=inc_search_protocole}}
    </td>
  </tr>
  
  <tr {{if !$conf.dPplanningOp.COperation.use_ccam}}style="display: none;"{{/if}}>
    <th>{{mb_label object=$op field="codes_ccam" defaultFor="_codes_ccam"}}</th>
    <td colspan="2">
      <input type="text" name="_codes_ccam" ondblclick="CCAMSelector.init()" style="width: 12em" value="" class="autocomplete"/>
      <div style="display: none; width: 200px !important" class="autocomplete" id="_codes_ccam_auto_complete"></div>
      <script>
        Main.add(function(){
          var oForm = getForm('editOp');
          var url = new Url("dPccam", "httpreq_do_ccam_autocomplete");
          url.autoComplete(oForm._codes_ccam, '', {
            minChars: 1,
            dropdown: true,
            width: "250px",
            updateElement: function(selected) {
              $V(oForm._codes_ccam, selected.down("strong").innerHTML);
              oCcamField.add($V(oForm._codes_ccam), true);
            }
          });
        })
      </script>
      <button class="add notext" type="button" onclick="oCcamField.add(this.form._codes_ccam.value,true)">{{tr}}Add{{/tr}}</button>
      <button type="button" class="search notext" onclick="CCAMSelector.init()">{{tr}}button-CCodeCCAM-choix{{/tr}}</button>
    </td>
  </tr>
  <tr {{if !$conf.dPplanningOp.COperation.use_ccam}}style="display: none;"{{/if}}>
    <th>
      Liste des codes CCAM
      {{mb_field object=$op field="codes_ccam" onchange="refreshListCCAM('expert');" hidden=1}}
    </th>
    <td colspan="2" class="text" id="listCodesCcam">
    </td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$op field=exam_extempo}}</th>
    <td colspan="2">{{mb_field object=$op field=exam_extempo}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$op field="cote"}}</th>
    <td colspan="2">
      {{mb_field object=$op field="cote" style="width: 15em" emptyLabel="Choose" onchange="Value.synchronize(this);"}}
    </td>
  </tr> 
  
  <tr>
    <th>{{mb_label object=$op field="type_anesth"}}</th>
    <td colspan="2">
      <select name="type_anesth" style="width: 15em;" onchange="Value.synchronize(this)">
        <option value="">&mdash; Anesthésie</option>
        {{foreach from=$listAnesthType item=curr_anesth}}
          {{if $curr_anesth->actif || $op->type_anesth == $curr_anesth->type_anesth_id}}
            <option value="{{$curr_anesth->type_anesth_id}}" {{if $op->type_anesth == $curr_anesth->type_anesth_id}} selected="selected" {{/if}}>
              {{$curr_anesth->name}} {{if !$curr_anesth->actif && $op->type_anesth == $curr_anesth->type_anesth_id}}(Obsolète){{/if}}
            </option>
          {{/if}}
        {{/foreach}}
      </select>
    </td>
  </tr> 

  {{if $can->admin}}
  <tr>
    <th>{{mb_label object=$op field="anesth_id"}}</th>
    <td colspan="2">
      {{mb_field object=$op field="anesth_id" style="width: 15em" options=$anesthesistes}}
    </td>
  </tr> 
  {{/if}}

  <tr>
    <th>{{mb_label object=$op field="_time_op"}}</th>
    <td>
      <input type="text" class="time" name="_time_op_da" readonly value="{{$op->_time_op|date_format:"%H:%M"}}" />
      <input name="_time_op" class="notNull time" type="hidden"
             onchange="$V(this.form.elements._time_op_da, this.value.substr(0, 5))" value="{{$op->_time_op}}"/>
      <script>
        Main.add(function() {
          Calendar.regField(getForm("editOp")._time_op, null, {datePicker:false, timePicker:true,
            minHours: parseInt({{$hours_duree.deb}}),
            maxHours: parseInt({{$hours_duree.fin}}),
            minInterval: parseInt({{$mins_duree}})});
        });
      </script>
    </td>
    <td id="timeEst">
    </td>
  </tr>
  
  <tr>
    {{if $modurgence}}
      <th>{{mb_label object=$op field="date"}}</th>
      <td colspan="2">
        <input type="hidden" name="plageop_id" value="" />
        <input type="hidden" name="_date" value="{{if $op->_datetime}}{{$op->_datetime|iso_date}}{{else}}{{$date_min}}{{/if}}" />
       
        
        {{assign var="operation_id" value=$op->operation_id}}
        {{mb_ternary var=update_entree_prevue test=$op->operation_id value="" other="updateEntreePrevue();"}}
        
        <input type="text" name="date_da" readonly value="{{$op->date|date_format:"%d/%m/%Y"}}" />
        <input type="hidden" name="date" value="{{$op->date}}" class="date notNull"
          onchange="
          {{$update_entree_prevue}}
          Value.synchronize(this.form.date_da);
          Value.synchronize(this);
          document.editSejour._curr_op_date.value = this.value;
          modifSejour();  $V(this.form._date, this.value);" />
        
        <script>
          Main.add(function() {
            var dates = {
              limit: {
                start: "{{$date_min}}",
                stop:  "{{$date_max}}"
              }
            };
            Calendar.regField(getForm("editOp").date{{if !$can->admin && !@$modules.dPbloc->_can->edit}}, dates{{/if}});
          });
        </script>
        
        à
        <input type="text" class="time" name="_time_urgence_da" readonly value="{{$op->_time_urgence|date_format:"%H:%M"}}" />
        <input name="_time_urgence" class="notNull time" type="hidden" value="{{$op->_time_urgence}}"
               onchange="Value.synchronize($(this.form._time_urgence_da));Value.synchronize(this);"/>

        <script>
          Main.add(function() {
            Calendar.regField(getForm("editOp")._time_urgence, null, {datePicker:false, timePicker:true,
              minHours: parseInt({{$hours_urgence.deb}}),
              maxHours: parseInt({{$hours_urgence.fin}}),
              minInterval: parseInt({{$mins_duree}})});
          });
        </script>
      </td>
    {{else}}
      <th>
        <input type="hidden" name="plageop_id" class="notNull {{$op->_props.plageop_id}}"
          value="{{$plage->plageop_id}}"
          onchange="Value.synchronize(this);"
          ondblclick="PlageOpSelector.init()" />
        {{mb_label object=$op field="plageop_id"}}
        <input type="hidden" name="date" value="" />
        <input type="hidden" name="_date" value="{{$plage->date}}" 
        onchange="Value.synchronize(this); 
                  if(this.value){ 
                    $V(this.form._locale_date, Date.fromDATE(this.value).toLocaleDate());
                  } else { 
                    $V(this.form._locale_date, '');
                  }
                  Sejour.preselectSejour(this.value);" />
      </th>
      <td colspan="2">
        <input type="text" name="_locale_date" readonly="readonly"
          onfocus="this.blur(); PlageOpSelector.init()"
          value="{{$op->_datetime|date_format:$conf.datetime}}"
          onchange="Value.synchronize(this);"
          style="width: 15em" />
        <button type="button" class="search notext" onclick="PlageOpSelector.init()">Choisir une date</button>
        {{if $op->_ref_salle && $op->_ref_salle->_id}}
        <br />
        en {{$op->_ref_salle->_view}}
        {{/if}}
      </td>
    {{/if}}
  </tr>
  
  {{if $modurgence}} 
    <tr>
      <th>{{mb_label object=$op field=salle_id}}</th>
      <td colspan="3">
        <select  style="width: 15em;" name="salle_id">
          <option value="">&mdash; {{tr}}CSalle.select{{/tr}}</option>
          {{foreach from=$listBlocs item=_bloc}}
          <optgroup label="{{$_bloc}}">
            {{foreach from=$_bloc->_ref_salles item=_salle}}
            <option value="{{$_salle->_id}}" {{if $_salle->_id == $op->salle_id}}selected="selected"{{/if}}>
              {{$_salle}}
            </option>
            {{foreachelse}}
            <option value="" disabled="disabled">{{tr}}CSalle.none{{/tr}}</option>
            {{/foreach}}
          </optgroup>
          {{/foreach}}
        </select>
      </td>
    </tr>
  {{/if}}
  {{if $conf.dPplanningOp.COperation.show_asa_position}}
    <tr>
      <th>{{mb_label object=$op field="ASA"}}</th>
      <td colspan="2">{{mb_field object=$op field="ASA" emptyLabel="Choose" style="width: 15em;"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$op field="position"}}</th>
      <td colspan="2">{{mb_field object=$op field="position" emptyLabel="Choose" style="width: 15em;"}}</td>
    </tr>
  {{/if}}
  {{if $conf.dPplanningOp.COperation.use_poste}}
    <tr>
      <th>
        {{mb_label object=$op field=poste_sspi_id}}
      </th>
      <td colspan="3">
        <input type="hidden" name="poste_sspi_id" value="{{$op->poste_sspi_id}}"/>
        <input type="text" name="_poste_sspi_id_autocomplete" value="{{$op->_ref_poste}}"/>
        <script>
          Main.add(function() {
            var form=getForm("editOp");
            var url = new Url("system", "ajax_seek_autocomplete");
            url.addParam("object_class", "CPosteSSPI");
            url.addParam('show_view', true);
            url.addParam("input_field", "_poste_sspi_id_autocomplete");
            url.addParam("where[type]", "sspi");
            url.addParam("whereComplex[bloc_id]", "IS NOT NULL");
            url.autoComplete(form.elements._poste_sspi_id_autocomplete, null, {
              minChars: 2,
              method: "get",
              select: "view",
              dropdown: true,
              afterUpdateElement: function(field,selected) {
                var guid = selected.getAttribute('id');
                if (guid) {
                  $V(field.form['poste_sspi_id'], guid.split('-')[2]);
                }
              }
            });
          });
        </script>
        <button type="button" class="cancel notext"
          onclick="$V(this.form.poste_sspi_id, ''); $V(this.form._poste_sspi_id_autocomplete, '')"></button>
      </td>
    </tr>
    <tr>
      <th>
        {{mb_label object=$op field=poste_preop_id}}
      </th>
      <td colspan="3">
        <input type="hidden" name="poste_preop_id" value="{{$op->poste_preop_id}}"/>
        <input type="text" name="_poste_preop_id_autocomplete" value="{{$op->_ref_poste_preop}}"/>
        <script>
          Main.add(function() {
            var form=getForm("editOp");
            var url = new Url("system", "ajax_seek_autocomplete");
            url.addParam("object_class", "CPosteSSPI");
            url.addParam('show_view', true);
            url.addParam("input_field", "_poste_preop_id_autocomplete");
            url.addParam("where[type]", "preop");
            url.addParam("whereComplex[bloc_id]", "IS NOT NULL");
            url.autoComplete(form.elements._poste_preop_id_autocomplete, null, {
              minChars: 2,
              method: "get",
              select: "view",
              dropdown: true,
              afterUpdateElement: function(field,selected) {
                var guid = selected.getAttribute('id');
                if (guid) {
                  $V(field.form['poste_preop_id'], guid.split('-')[2]);
                }
              }
            });
          });
        </script>
        <button type="button" class="cancel notext"
                onclick="$V(this.form.poste_preop_id, ''); $V(this.form._poste_preop_id_autocomplete, '')"></button>
      </td>
    </tr>
  {{/if}}
  
  {{if $conf.dPplanningOp.COperation.show_duree_uscpo >= 1}}
    <tr>
      <th>{{mb_label object=$op field=duree_uscpo}}</th>
      <td colspan="3">{{mb_field object=$op field=duree_uscpo increment=true form=editOp size=2}} {{tr}}night{{/tr}}(s)</td>
    </tr>
  {{/if}}
  {{if $conf.dPplanningOp.COperation.show_presence_op}}
    <tr>
      <th>{{mb_label object=$op field=presence_preop}}</th>
      <td colspan="2">{{mb_field object=$op field=presence_preop form=editOp }}</td>
    </tr>
  
    <tr>
      <th>{{mb_label object=$op field=presence_postop}}</th>
      <td colspan="2">{{mb_field object=$op field=presence_postop form=editOp }}</td>
    </tr>
  {{/if}}
  {{if $conf.dPbloc.CPlageOp.systeme_materiel == "expert"}}
    <tr>
      <td></td>
      <td>
        {{mb_include module=dPbloc template=inc_button_besoins_ressources object_id=$op->_id type=operation_id from_dhe=1}}
      </td>
      <td></td>
    </tr>
  {{/if}}
  {{if $conf.dPplanningOp.COperation.show_remarques}}
    <tr>
      <td class="text">{{mb_label object=$op field="examen"}}</td>
      <td class="text">{{mb_label object=$op field="materiel"}}</td>
      <td class="text">{{mb_label object=$op field="exam_per_op"}}</td>
    </tr>
    <tr>
      <td style="width: 33%;">
        {{mb_field object=$op field="examen" form="editOp"
          aidesaisie="validateOnBlur: 0"}}
      </td>
      <td style="width: 33%;">
        {{mb_field object=$op field="materiel" onchange="Value.synchronize(this);" form="editOp"
        aidesaisie="validateOnBlur: 0"}}
      </td>
      <td style="width: 33%;">
        {{mb_field object=$op field="exam_per_op" onchange="Value.synchronize(this);" form="editOp"
        aidesaisie="validateOnBlur: 0"}}
      </td>
    </tr>
    <tr>
      <td colspan="3" class="text">{{mb_label object=$op field="rques"}}</td>
    </tr>
    <tr>
      <td colspan="3">
        {{mb_field object=$op field="rques" onchange="Value.synchronize(this);" form="editOp"
        aidesaisie="validateOnBlur: 0"}}
      </td>
    </tr>
  {{/if}}
  {{if $op->_count_actes}}
  <tr>
    <td colspan="3">
      <div class="small-info">
        L'intervention a déjà été codée.<br/>
        Il est impossible de modifier les champs ci-dessous.
      </div>
    </td>
  </tr>
  {{/if}}
  
  {{if $conf.dPplanningOp.COperation.show_montant_dp}}  
    <tr>
      <th>{{mb_label object=$op field="conventionne"}}</th>
      <td colspan="2">
        {{mb_field object=$op field="conventionne" typeEnum="checkbox"}}
      </td>
    </tr>
    <tr>
      <td class="text">{{mb_label object=$op field="depassement"}}</td>
      <td class="text">{{mb_label object=$op field="forfait"}}</td>
      <td class="text">{{mb_label object=$op field="fournitures"}}</td>
    </tr>
  
    <tr>
      {{if $op->_ref_actes_ccam|@count}}
      <td>{{mb_value object=$op field="depassement"}}</td>
      <td>{{mb_value object=$op field="forfait"}}</td>
      <td>{{mb_value object=$op field="fournitures"}}</td>
      {{else}}
      <td>{{mb_field object=$op field="depassement" size="4"}}</td>
      <td>{{mb_field object=$op field="forfait" size="4"}}</td>
      <td>{{mb_field object=$op field="fournitures" size="4"}}</td>
      {{/if}}
    </tr>
    <tr>
      <th>{{mb_label object=$op field="info"}}</th>
      <td colspan="2">
        {{mb_field object=$op field="info" typeEnum="checkbox"}}
      </td>
    </tr>
  {{/if}}
  {{if "reservation"|module_active}}
    <tr>
      <th></th>
      <td>
        {{mb_include module="reservation" template="inc_button_examen" form="editOp"}}
      </td>
    </tr>
  {{/if}}
</table>

</form>
