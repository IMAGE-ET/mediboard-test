<!-- $Id$ -->

{{mb_script module="dPplanningOp" script="ccam_selector"}}
{{mb_script module="dPplanningOp" script="plage_selector"}}
{{mb_script module="dPpatients"   script="pat_selector"}}

<script type="text/javascript">
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
  
  this.pop(oOpForm.chir_id.value, oOpForm._hour_op.value,
           oOpForm._min_op.value, oSejourForm.group_id.value,
           oOpForm.operation_id.value);
}

CCAMSelector.init = function(){
  this.sForm  = "editOp";
  this.sView  = "_codes_ccam";
  this.sChir  = "chir_id";
  this.sClass = "_class";
  this.pop();
}

addBesoins = function(types_ressources_ids) {
  var form = getForm("addBesoinOp");
  types_ressources_ids = types_ressources_ids.split(",");
  
  types_ressources_ids.each(function(type_ressource_id) {
    $V(form.type_ressource_id, type_ressource_id);
    onSubmitFormAjax(form, function() {
    // C'est après l'ajout du dernier besoin que l'on actualise la couleur du bouton Matériel
      if (types_ressources_ids.indexOf(type_ressource_id) == (types_ressources_ids.length - 1)) {
       checkRessources.curry('{{$op->_id}}');
      }
    });
  });
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
  <input type="hidden" name="postRedirect" value="m=planningOp&a=vw_edit_urgence&dialog=1" />
{{/if}}

{{if $op->_id && $op->_ref_sejour->sortie_reelle && !$modules.dPbloc->_can->edit}}
<!-- <input type="hidden" name="_locked" value="1" /> -->
{{/if}}
{{mb_field object=$op field="sejour_id" hidden=1 canNull=true}}
{{mb_field object=$op field="commande_mat" hidden=1}}
{{mb_field object=$op field="rank" hidden=1}}
{{*mb_field object=$op field="_horaire_voulu" hidden=0*}}
<input type="hidden" name="_horaire_voulu" value="{{$op->_horaire_voulu}}" />
<input type="hidden" name="annulee" value="{{$op->annulee|default:"0"}}" />
<input type="hidden" name="salle_id" value="{{$op->salle_id}}" />

<!-- Form Fields -->
<input type="hidden" name="_group_id" value="{{$sejour->group_id}}" />
<input type="hidden" name="_class" value="COperation" />
<input type="hidden" name="_protocole_prescription_anesth_id" value="" />
<input type="hidden" name="_protocole_prescription_chir_id" value="" />
{{mb_field object=$op field="_count_actes" hidden=1}}
<input type="hidden" name="_place_after_interv_id" value="" />
{{mb_field object=$op field=duree_preop form=editOp hidden=1}}
<input type="hidden" name="_types_ressources_ids" {{if $op->_id}}onchange="addBesoins(this.value)"{{/if}}/>

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
      <select name="chir_id" class="{{$op->_props.chir_id}}"
        onchange="synchroPrat(); Value.synchronize(this); removePlageOp(true);"
        style="width: 15em">
        <option value="">&mdash; Choisir un chirurgien</option>
        {{mb_include module=mediusers template=inc_options_mediuser list=$listPraticiens selected=$chir->_id}}
      </select>
      <button type="button"onclick="toggleOtherPrats()" title="{{tr}}Add{{/tr}}"
        class="notext {{if $op->chir_2_id || $op->chir_3_id || $op->chir_4_id}}up{{else}}down{{/if}}"></button>
    </td>
  </tr>
  <tr class="other_prats" {{if !$op->chir_2_id && !$op->chir_3_id && !$op->chir_4_id}}style="display: none"{{/if}}>
    <th>
      {{mb_label object=$op field="chir_2_id"}}
    </th>
    <td colspan="2">
      <select name="chir_2_id" onchange="Value.synchronize(this)">
        <option value="">&mdash; Choisir un chirurgien</option>
        {{mb_include module=mediusers template=inc_options_mediuser list=$listPraticiens selected=$op->chir_2_id}}
      </select>
    </td>
  </tr>
  <tr class="other_prats" {{if !$op->chir_2_id && !$op->chir_3_id && !$op->chir_4_id}}style="display: none"{{/if}}>
    <th>
      {{mb_label object=$op field="chir_3_id"}}
    </th>
    <td colspan="2">
      <select name="chir_3_id" onchange="Value.synchronize(this)">
        <option value="">&mdash; Choisir un chirurgien</option>
        {{mb_include module=mediusers template=inc_options_mediuser list=$listPraticiens selected=$op->chir_3_id}}
      </select>
    </td>
  </tr>
  <tr class="other_prats" {{if !$op->chir_2_id && !$op->chir_3_id && !$op->chir_4_id}}style="display: none"{{/if}}>
    <th>
      {{mb_label object=$op field="chir_4_id"}}
    </th>
    <td colspan="2">
      <select name="chir_4_id" onchange="Value.synchronize(this)">
        <option value="">&mdash; Choisir un chirurgien</option>
        {{mb_include module=mediusers template=inc_options_mediuser list=$listPraticiens selected=$op->chir_4_id}}
      </select>
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$op field="libelle"}}</th>
    <td colspan="2">
      {{mb_field object=$op field="libelle" form="editOp"
        autocomplete="true,1,50,true,true"
        onblur="\$V(getForm('editOpEasy').libelle, \$V(getForm('editOp').libelle));"
        style="width: 12em"}}
      <button class="search notext" type="button" onclick="ProtocoleSelector.init()">
        Choisir un protocole
      </button>
    </td>
  </tr>
  
  <tr {{if !$conf.dPplanningOp.COperation.use_ccam}}style="display: none;"{{/if}}>
    <th>{{mb_label object=$op field="codes_ccam" defaultFor="_codes_ccam"}}</th>
    <td colspan="2">
      <input type="text" name="_codes_ccam" ondblclick="CCAMSelector.init()" style="width: 12em" value="" class="autocomplete"/>
      <div style="display: none; width: 200px !important" class="autocomplete" id="_codes_ccam_auto_complete"></div>
      <script type="text/javascript">
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
      <select name="type_anesth" style="width: 15em;" onchange="submitAnesth(this.form);">
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
    <th>{{mb_label object=$op field="_hour_op"}}</th>
    <td>
      <select name="_hour_op" class="notNull num">
      {{foreach from=$hours_duree|smarty:nodefaults item=hour}}
        <option value="{{$hour}}" {{if (!$op && $hour == 1) || $op->_hour_op == $hour}} selected="selected" {{/if}}>{{$hour}}</option>
      {{/foreach}}
      </select> h
      <select name="_min_op">
      {{foreach from=$mins_duree|smarty:nodefaults item=min}}
        <option value="{{$min}}" {{if (!$op && $min == 0) || $op->_min_op == $min}} selected="selected" {{/if}}>{{$min}}</option>
      {{/foreach}}
      </select> min
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
        
        <input type="hidden" name="date" value="{{$op->date}}" class="date notNull"
          onchange="
          {{$update_entree_prevue}}
          Value.synchronize(this.form.date_da);
          Value.synchronize(this);
          document.editSejour._curr_op_date.value = this.value;
          modifSejour();  $V(this.form._date, this.value);" />
        
        <script type="text/javascript">
          Main.add(function() {
            var dates = {
              limit: {
                start: "{{$date_min}}",
                stop:  "{{$date_max}}"
              }
            };
            Calendar.regField(getForm("editOp").date{{if !$can->admin}}, dates{{/if}});
          });
        </script>
        
        à
        <select name="_hour_urgence" onchange="Value.synchronize(this)">
        {{foreach from=$hours_urgence|smarty:nodefaults item=hour}}
          <option value="{{$hour}}" {{if $op->_hour_urgence == $hour}} selected="selected" {{/if}}>{{$hour}}</option>
        {{/foreach}}
        </select> h
        <select name="_min_urgence" onchange="Value.synchronize(this);">
        {{foreach from=$mins_duree|smarty:nodefaults item=min}}
          <option value="{{$min}}" {{if $op->_min_urgence == $min}}selected="selected"{{/if}}>{{$min}}</option>
        {{/foreach}}
        </select> min
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
                  }; 
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
  
  {{if $conf.dPplanningOp.COperation.show_duree_uscpo >= 1}}
    <tr>
      <th>{{mb_label object=$op field=duree_uscpo}}</th>
      <td colspan="3">{{mb_field object=$op field=duree_uscpo increment=true form=editOp size=2}} {{tr}}night{{/tr}}(s)</td>
    </tr>
  {{/if}}
  
  <tr>
    <th>{{mb_label object=$op field=presence_preop}}</th>
    <td colspan="2">{{mb_field object=$op field=presence_preop form=editOp }}</td>
  </tr>

  <tr>
    <th>{{mb_label object=$op field=presence_postop}}</th>
    <td colspan="2">{{mb_field object=$op field=presence_postop form=editOp }}</td>
  </tr>
  
  {{if $conf.dPbloc.CPlageOp.systeme_materiel == "expert"}}
    <tr>
      <td></td>
      <td>
        {{mb_include module=dPbloc template=inc_button_besoins_ressources object_id=$op->_id type=operation_id}}
      </td>
      <td></td>
    </tr>
  {{/if}}
  <tr>
    <td class="text">{{mb_label object=$op field="examen"}}</td>
    <td class="text">{{mb_label object=$op field="materiel"}}</td>
    <td class="text">{{mb_label object=$op field="rques"}}</td>
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
      {{mb_field object=$op field="rques" onchange="Value.synchronize(this);" form="editOp"
        aidesaisie="validateOnBlur: 0"}}
    </td>
  </tr>
  
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
      {{mb_field object=$op field="info"}}
    </td>
  </tr>

</table>

</form>
