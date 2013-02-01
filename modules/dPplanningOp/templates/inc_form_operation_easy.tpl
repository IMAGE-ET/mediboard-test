<!-- $Id$ -->


<form name="editOpEasy" action="?m={{$m}}" method="post" onsubmit="return checkFormOperation()">
{{if $op->_id && $op->_ref_sejour->sortie_reelle && !$modules.dPbloc->_can->edit}}
<!-- <input type="hidden" name="_locked" value="1" /> -->
{{/if}}

<table class="form">
  {{if $op->annulee == 1}}
  <tr>
    <th class="category cancelled" colspan="3">
    {{tr}}COperation-annulee{{/tr}}
    </th>
  </tr>
  {{/if}}
  
  <!-- Selection du ou des chirurgiens -->
  <tr>
    <th class="narrow">{{mb_label object=$op field="chir_id"}}</th>
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
  {{if $conf.dPplanningOp.COperation.show_secondary_function && !$op->_id}}
    <tr>
      <th>
        {{mb_label class=CMediusers field=function_id}}
      </th>
      <td id="secondary_functions_easy" colspan="2">
        {{mb_include module=dPcabinet template=inc_refresh_secondary_functions chir=$chir change_active=0}}
      </td>
    </tr>
  {{/if}}
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
  
  {{if $conf.dPplanningOp.CSejour.easy_service}}
  <!-- Selection du service -->
  <tr>
    <th>{{mb_label object=$sejour field="service_id"}}</th>
    <td colspan="2">
      <select name="service_id" class="{{$sejour->_props.service_id}}" onchange="Value.synchronize(this, 'editSejour');" style="width: 15em;">
        <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
        {{foreach from=$listServices item=_service}}
        <option value="{{$_service->_id}}" {{if $sejour->service_id == $_service->_id}} selected="selected" {{/if}}>
          {{$_service->_view}}
        </option>
        {{/foreach}}
      </select>
    </td>
  </tr>
  {{/if}}
  
  
  <!-- Affichage du libelle -->
  <tr>
    <th>{{mb_label object=$op field="libelle"}}</th>
    <td colspan="2">
      {{mb_field object=$op field="libelle" style="width: 15em; padding-right: 1px;" onfocus="ProtocoleSelector.init()" readonly="readonly"}}
      <button class="search notext" type="button" onclick="ProtocoleSelector.init()">
        Choisir un protocole
      </button>
    </td>
  </tr>

  <!-- Diagnostic principal -->
  {{if $conf.dPplanningOp.CSejour.easy_cim10}}
  <tr>
    <th>{{mb_label object=$sejour field="DP"}}</th>
    <td colspan="2">
      <script type="text/javascript">
      Main.add(function(){
        var url = new Url("dPcim10", "ajax_code_cim10_autocomplete");
        url.autoComplete(getForm("editOpEasy").keywords_code, '', {
          minChars: 1,
          dropdown: true,
          width: "250px",
          select: "code",
          afterUpdateElement: function(oHidden) {
            $V(getForm("editOpEasy").DP, oHidden.value);
          }
        });
      });
      </script>
      
      <input type="text" name="keywords_code" class="autocomplete str code cim10" value="{{$sejour->DP}}" onchange="Value.synchronize(this, 'editSejour');" style="width: 12em" />
      <button type="button" class="cancel notext" onclick="$V(this.form.DP, '');" />
      <button type="button" class="search notext" onclick="CIM10Selector.init()">{{tr}}button-CCodeCIM10-choix{{/tr}}</button>
      <input type="hidden" name="DP" value="{{$sejour->DP}}" onchange="$V(this.form.keywords_code, this.value); Value.synchronize(this, 'editSejour');"/>
    </td>
  </tr>
  {{/if}}
  
  
  <!-- Liste des codes ccam -->
  <tr {{if !$conf.dPplanningOp.COperation.use_ccam}}style="display: none;"{{/if}}>
    <th>
      Liste des codes CCAM
      {{mb_field object=$op field="codes_ccam" onchange="refreshListCCAM('easy');" hidden=1}}
    </th>
    <td colspan="2" class="text" id="listCodesCcamEasy"></td>
  </tr>
    
  <!-- Selection du coté --> 
  <tr>
    <th>{{mb_label object=$op field="cote"}}</th>
    <td colspan="2">
      {{mb_field object=$op field="cote" style="width: 15em" emptyLabel="Choose" onchange="Value.synchronize(this); modifOp();"}}
    </td>
  </tr> 

  <!-- Choix du type d'anesthésie -->
  {{if $conf.dPplanningOp.COperation.easy_type_anesth}}
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
  {{/if}}
  <!-- Selection de la date -->
  {{if $modurgence}}
  <tr>
    <th>
      {{mb_label object=$op field="date"}}
    </th>
    <td colspan="2">
      <input type="hidden" name="plageop_id" value="" />
      <input type="hidden" name="_date" value="{{if $op->_datetime}}{{$op->_datetime|iso_date}}{{else}}{{$date_min}}{{/if}}" />
      {{assign var="operation_id" value=$op->operation_id}}
      {{mb_ternary var=update_entree_prevue test=$op->operation_id value="" other="updateEntreePrevue();"}}
      <input type="text" name="date_da" readonly value="{{$op->date|date_format:"%d/%m/%Y"}}" />
      <input type="hidden" name="date" value="{{$op->date}}" class="date notNull"
        onchange="{{$update_entree_prevue}}
        Value.synchronize(this.form.date_da);
        Value.synchronize(this);
        document.editSejour._curr_op_date.value = this.value;
        modifSejour();
        $V(this.form._date, this.value);"/>
      <script type="text/javascript">
        Main.add(function() {
          var dates = {
            limit: {
              start: "{{$date_min}}",
              stop:  "{{$date_max}}"
            }
          };
          Calendar.regField(getForm("editOpEasy").date{{if !$can->admin}}, dates{{/if}});
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
      </select> mn
    </td>
  </tr>
  
  {{else}}
  <tr>
    <th>
      <input type="hidden" name="plageop_id" class="notNull {{$op->_props.plageop_id}}" onchange="Value.synchronize(this);" ondblclick="PlageOpSelector.init()" value="{{$plage->plageop_id}}" />
      {{mb_label object=$op field="plageop_id"}}
      <input type="hidden" name="date" value="" />
      <input type="hidden" name="_date" value="{{$plage->date}}" 
      onchange="Value.synchronize(this);
                Sejour.preselectSejour(this.value);" />
    </th>
    <td colspan="2">
      <input type="text" name="_locale_date" readonly="readonly"
        style="width: 15em;"
        onfocus="this.blur(); PlageOpSelector.init()"
        value="{{$op->_datetime|date_format:$conf.datetime}}" />
      <button type="button" class="search notext" onclick="PlageOpSelector.init()">Choisir une date</button>
    </td>
  </tr>
  {{/if}}
  
  <tr>
    <th>Taux d'occupation</th>
    <td colspan="2" id="occupationeasy">
    </td>
  </tr>

  <!-- Selection du patient -->
  <tr>
    <th>
      <input type="hidden" name="patient_id" class="notNull {{$sejour->_props.patient_id}}" ondblclick="PatSelector.init()" value="{{$patient->patient_id}}" onchange="changePat(); $('button-edit-patient-easy').setVisible(this.value);" />
      {{mb_label object=$sejour field="patient_id"}}
    </th>
    <td colspan="2">
      <input type="text" name="_patient_view" style="width: 15em" value="{{$patient->_view}}" readonly="readonly"
        {{if $conf.dPplanningOp.CSejour.patient_id || !$sejour->_id || $app->user_type == 1}}
          onfocus="PatSelector.init()"
        {{/if}}
      />
      {{if $conf.dPplanningOp.CSejour.patient_id || !$sejour->_id || $app->user_type == 1}}
      <button type="button" class="search notext" onclick="PatSelector.init()">Choisir un patient</button>
      <button id="button-edit-patient-easy" type="button" 
            onclick="location.href='?m=dPpatients&amp;tab=vw_edit_patients&amp;patient_id='+this.form.patient_id.value" 
            class="edit notext" {{if !$patient->_id}}style="display: none;"{{/if}}>
        {{tr}}Edit{{/tr}}
      </button>
      {{/if}}
    </td>
  </tr>

  {{if $conf.dPplanningOp.CSejour.show_atnc && $conf.dPplanningOp.CSejour.easy_atnc}}
    <th>{{mb_label object=$sejour field="ATNC"}}</th>
    <td colspan="3">{{mb_field object=$sejour field="ATNC" typeEnum="select" emptyLabel="Non renseigné" onchange="checkATNCEasy()"}}</td>
  {{/if}}

  <!-- ALD et CMU -->
  <tbody id="ald_patient_easy" {{if !$conf.dPplanningOp.CSejour.easy_ald_cmu}} style="display: none;"{{/if}}>
    {{mb_include module=planningOp template=inc_check_ald patient=$sejour->_ref_patient onchange="Value.synchronize(this, 'editSejour');"}}
  </tbody>
  
  
  <!-- Selection du type de chambre et du régime alimentaire-->
  {{if $conf.dPplanningOp.CSejour.easy_chambre_simple && $conf.dPhospi.systeme_prestations == "standard"}}
    <tr>
      <th>{{mb_label object=$sejour field="chambre_seule"}}</th>
      <td colspan="2">{{mb_field object=$sejour field="chambre_seule" onchange="checkChambreSejourEasy()"}}</td>
    </tr>
  {{/if}}
  
  {{if $conf.dPplanningOp.CSejour.easy_chambre_simple || $conf.dPplanningOp.COperation.easy_regime || $conf.dPbloc.CPlageOp.systeme_materiel == "expert"}}
    <tr>
      <td></td>
      <td colspan="2">
        {{if $conf.dPplanningOp.COperation.easy_materiel && $conf.dPbloc.CPlageOp.systeme_materiel == "expert"}}
          {{mb_include module=dPbloc template=inc_button_besoins_ressources object_id=$op->_id type=operation_id}}
        {{/if}}
      {{if $conf.dPplanningOp.CSejour.easy_chambre_simple && $conf.dPhospi.systeme_prestations == "expert" && $sejour->_id}}
        <button type="button" class="search" onclick="Prestations.edit('{{$sejour->_id}}', 'sejour')">Prestations</button>
      {{/if}}
      {{if $conf.dPplanningOp.COperation.easy_regime}}
        {{mb_include template=regimes_alimentaires prefix=easy}}
      {{/if}}
    </tr>
  {{/if}}

  <!-- Consultation d'accompagnement -->
  {{if $conf.dPplanningOp.CSejour.consult_accomp}}
  <tr>
    <th>{{mb_label object=$sejour field=consult_accomp}}</th>
    <td colspan="3">{{mb_field object=$sejour field=consult_accomp typeEnum=radio onchange="checkConsultAccompSejourEasy()"}}</td>
  </tr>
  {{/if}}
  
  {{if ($conf.dPplanningOp.COperation.easy_materiel || $conf.dPplanningOp.COperation.easy_remarques) && $conf.dPplanningOp.COperation.show_remarques}}
  <tr>
    <td></td>
    {{if $conf.dPplanningOp.COperation.easy_materiel}}
    <td class="text" {{if !$conf.dPplanningOp.COperation.easy_remarques}}colspan="2"{{/if}}>
      {{mb_label object=$op field="materiel"}}
    </td>
    {{/if}}
    {{if $conf.dPplanningOp.COperation.easy_remarques}}
      <td class="text" {{if !$conf.dPplanningOp.COperation.easy_materiel}}colspan="2"{{/if}}>{{mb_label object=$op field="rques"}}</td>
    {{/if}}
  </tr>
  <tr>
  <td></td>
    {{if $conf.dPplanningOp.COperation.easy_materiel}}
    <td style="width: 33%;" {{if !$conf.dPplanningOp.COperation.easy_remarques}}colspan="2"{{/if}}>
        {{mb_field object=$op field="materiel" onchange="Value.synchronize(this);" form="editOpEasy"
        aidesaisie="validateOnBlur: 0"}}
    </td>
    {{/if}}
    {{if $conf.dPplanningOp.COperation.easy_remarques}}
    <td style="width: 33%;" {{if !$conf.dPplanningOp.COperation.easy_materiel}}colspan="2"{{/if}}>
      {{mb_field object=$op field="rques" onchange="Value.synchronize(this);" form="editOpEasy"
        aidesaisie="validateOnBlur: 0"}}
    </td>
    {{/if}}
  </tr>
  {{/if}}
  {{if $conf.dPplanningOp.CSejour.accident && $conf.dPplanningOp.COperation.easy_accident}}
  <tr>
    <th>{{mb_label object=$sejour field="date_accident"}}</th>
    <td colspan="3">{{mb_field object=$sejour form="editOpEasy" field="date_accident" register=true onchange="checkAccidentEasy();"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$sejour field="nature_accident"}}</th>
    <td colspan="3">{{mb_field object=$sejour field="nature_accident" emptyLabel="Choose" style="width: 15em;" onchange="checkAccidentEasy();"}}</td>
  </tr>
  {{/if}}

  {{if $conf.dPplanningOp.CSejour.assurances && $conf.dPplanningOp.COperation.easy_assurances}}
    <tbody id="assurances_patient_easy">
      {{mb_include module=planningOp template="inc_vw_assurances"}}
    </tbody>
  {{/if}}
</table>
</form>