{{mb_default var=show_target value=true}}
{{mb_default var=from_lock   value=false}}
{{mb_default var=force_new   value=false}}
{{mb_default var=show_link   value=true}}
{{mb_default var=show_type   value=true}}

{{assign var=trans_compact value="soins Transmissions trans_compact"|conf:"CGroups-$g"}}

{{if $_suivi instanceof CObservationMedicale}}
  {{if @$show_patient}}
    <td><strong>{{$_suivi->_ref_sejour->_ref_patient}}</strong></td>
    <td class="text">{{$_suivi->_ref_sejour->_ref_last_affectation->_ref_lit->_view}}</td>
  {{/if}}
  <td style="text-align: center;">
    <strong>
      Obs
      {{if $_suivi->type == "reevaluation"}}
        <br /><span class="compact"><label title="Réévaluation antibiothérapie">(rééval. antitio.)</label></span>
      {{/if}}
    </strong>
  </td>
  <td class="narrow text">
    {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_suivi->_ref_user}}
    <br />
    {{mb_value object=$_suivi field=date}}
  </td>
  <td class="narrow text">
    {{if $_suivi->object_id}}
      <span onmouseover="ObjectTooltip.createEx(this, '{{$_suivi->_ref_object->_guid}}');">
       {{if $_suivi->_ref_object instanceof CPrescriptionLineMedicament}}
         {{$_suivi->_ref_object->_ucd_view}}
       {{else}}
         {{$_suivi->_ref_object->_view}}
       {{/if}}
     </span>
    {{/if}}
  </td>
  <td colspan="3" class="text">
    <div>
      <strong>{{mb_value object=$_suivi field=text}}</strong>
    </div>
  </td>
  <td class="text">
    {{if !$readonly && $_suivi->_canEdit}}
      <form name="Del-{{$_suivi->_guid}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
        <input type="hidden" name="dosql" value="do_observation_aed" />
        <input type="hidden" name="del" value="1" />
        <input type="hidden" name="m" value="dPhospi" />
        <input type="hidden" name="observation_medicale_id" value="{{$_suivi->_id}}" />
        <input type="hidden" name="sejour_id" value="{{$_suivi->sejour_id}}" />
        <button type="button" class="trash notext" onclick="submitSuivi(this.form, 1)">{{tr}}Delete{{/tr}}</button>
      </form>
      <button type="button" class="edit notext" onclick="addObservation(null, null, '{{$_suivi->_id}}');"></button>
    {{/if}}
  </td>
{{/if}}

{{if $_suivi instanceof CConstantesMedicales}}
  <td style="text-align: center;">
    <label title="Constantes">Cst</label>
  </td>
  <td class="narrow text">
    {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_suivi->_ref_user}}
    <br />
    {{mb_value object=$_suivi field=datetime}}
  </td>
  <td colspan="4" class="text">
    {{foreach from=$params key=_key item=_field name="const"}}
      {{if $_suivi->$_key != null && $_key|substr:0:1 != "_"}}
        {{mb_title object=$_suivi field=$_key}} :
        {{if array_key_exists("formfields", $_field)}}
          {{mb_value object=$_suivi field=$_field.formfields.0 size="2"}}
          {{if array_key_exists(1, $_field.formfields)}}
            /
            {{mb_value object=$_suivi field=$_field.formfields.1 size="2"}}
          {{/if}}
        {{else}}
          {{mb_value object=$_suivi field=$_key}}
        {{/if}} {{$_field.unit}},
      {{/if}}
    {{/foreach}}
    {{if $_suivi->comment}}
      ({{$_suivi->comment}})
    {{/if}}
  </td>
  <td></td>
{{/if}}

{{if $_suivi instanceof CPrescriptionLineElement || $_suivi instanceof CPrescriptionLineComment}}
  <td style="text-align: center">
    <label title="Ligne de prescription">
      <strong>Presc</strong>
    </label>
  </td>
  <td class="narrow  text">
    {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_suivi->_ref_praticien}}
    <br />
    {{mb_value object=$_suivi field=debut}}
  </td>
  <td colspan="4" {{if $_suivi->_count.transmissions}} class="arretee" {{/if}}>
    {{if !$readonly}}
      <button type="button" class="tick" onclick="addTransmissionAdm('{{$_suivi->_id}}','{{$_suivi->_class}}');" style="float: right;">Réaliser ({{$_suivi->_count.transmissions}})</button>
    {{/if}}

    {{if $_suivi instanceof CPrescriptionLineElement}}
      <strong onmouseover="ObjectTooltip.createEx(this, '{{$_suivi->_ref_element_prescription->_guid}}');">{{$_suivi->_view}}</strong>
    {{/if}}
    {{mb_value object=$_suivi field="commentaire"}}
  </td>
  <td class="text {{if $_suivi->_count.transmissions}}arretee{{/if}}">
    {{if !$readonly && $_suivi->_canEdit && !$_suivi->_count.transmissions}}
      <form name="Del-{{$_suivi->_guid}}" action="?" method="post">
        <input type="hidden" name="m" value="dPprescription" />
        {{if $_suivi instanceof CPrescriptionLineElement}}
          <input type="hidden" name="dosql" value="do_prescription_line_element_aed" />
        {{else}}
          <input type="hidden" name="dosql" value="do_prescription_line_comment_aed" />
        {{/if}}
        <input type="hidden" name="del" value="1" />
        {{mb_key object=$_suivi}}
        <input type="hidden" name="sejour_id" value="{{$_suivi->_ref_prescription->object_id}}" />
        <button type="button" class="trash notext" onclick="submitSuivi(this.form, 1);"></button>
      </form>
      <button type="button" class="edit notext"
              onclick="addPrescription('{{$_suivi->_ref_prescription->object_id}}', '{{$app->user_id}}', '{{$_suivi->_id}}', '{{$_suivi->_class}}');"></button>
    {{/if}}
  </td>
{{/if}}

{{if $_suivi instanceof CConsultation}}
  <td class="narrow" style="text-align: center;">
    <strong onmouseover="ObjectTooltip.createEx(this, '{{$_suivi->_guid}}')">
      {{if $_suivi->type == "entree"}}
        Obs. entrée
      {{elseif $_suivi->_refs_dossiers_anesth|@count >= 1}}
        Cs anesth.
      {{else}}
        Cs
      {{/if}}
    </strong>
  </td>
  <td class="narrow text">
    {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_suivi->_ref_praticien}}
    <br />
    {{mb_value object=$_suivi field=_datetime}}
  </td>
  <td></td>
  <td class="text" colspan="3">
    {{if $_suivi->_refs_dossiers_anesth|@count}}
      {{foreach from=$_suivi->_refs_dossiers_anesth item=_dossier_anesth}}
        <strong>
          Dossier d'anesthésie
          {{if $_dossier_anesth->operation_id}}
            pour l'intervention du {{mb_value object=$_dossier_anesth->_ref_operation field=_datetime_best}}
          {{else}}
            {{$_dossier_anesth->_id}}
          {{/if}}
        </strong>
        <br />
        {{if $_dossier_anesth->operation_id}}
          {{if $_dossier_anesth->_ref_operation->ASA}}
            <u>ASA :</u> {{tr}}COperation.ASA.{{$_dossier_anesth->_ref_operation->ASA}}{{/tr}} <br />
          {{/if}}
          {{if $_dossier_anesth->_ref_operation->position}}
            <u>Position :</u> {{mb_value object=$_dossier_anesth->_ref_operation field=position}} <br />
          {{/if}}
        {{/if}}
        {{if $_dossier_anesth->prepa_preop}}
          <u>{{mb_label class=CConsultAnesth field=prepa_preop}} :</u> {{mb_value object=$_dossier_anesth field=prepa_preop}} <br />
        {{/if}}
        {{if $_dossier_anesth->_ref_techniques|@count}}
          <u>Techniques :</u>
          {{foreach from=$_dossier_anesth->_ref_techniques item=_technique name=foreach_techniques}}
            {{mb_value object=$_technique field=technique}} {{if !$smarty.foreach.foreach_techniques.last}}-{{/if}}
          {{/foreach}}
        {{/if}}
      {{/foreach}}
      {{if $_suivi->rques}}
        <u>Remarques :</u> {{mb_value object=$_suivi field=rques}} <br />
      {{/if}}
    {{else}}
      {{foreach from=$_suivi->_specs item=_spec}}
        {{assign var=field_name value=$_spec->fieldName}}
        {{if $_spec instanceof CTextSpec && $_suivi->$field_name}}
          <u>{{tr}}CConsultation-{{$field_name}}{{/tr}}</u> : {{mb_value object=$_suivi field=$field_name}} <br />
        {{/if}}
      {{/foreach}}
    {{/if}}
    {{if "forms"|module_active && $_suivi->_list_forms|@count}}
      <u>Formulaires :</u>
      <ul>
        {{foreach from=$_suivi->_list_forms item=_forms key=ex_class_id}}
          {{foreach from=$_forms item=ex_object key=ex_object_id}}
            <li>
              <a href="#1" onclick="ExObject.display('{{$ex_object_id}}', '{{$ex_class_id}}', '{{$ex_object->object_class}}-{{$ex_object->object_id}}')">
                {{$ex_object->_ref_ex_class->name}}
              </a>
            </li>
          {{/foreach}}
        {{/foreach}}
      </ul>
    {{/if}}
  </td>
  <td>
    {{if !$readonly}}
      {{if $_suivi->_canEdit}}
        <form name="Del-{{$_suivi->_guid}}" action="?m={{$m}}" method="post"
              onsubmit="return onSubmitFormAjax(this, function() { loadSuivi('{{$sejour->_id}}') })">
          <input type="hidden" name="dosql" value="do_consultation_aed" />
          <input type="hidden" name="m" value="cabinet" />
          <input type="hidden" name="consultation_id" value="{{$_suivi->_id}}" />
          <input type="hidden" name="annule" value="1" />
          <button type="button" class="trash notext"
                  onclick="if (confirm('Voulez-vous vraiment annuler cette consultation ?')) { this.form.onsubmit() } ">
            {{tr}}Delete{{/tr}}</button>
        </form>
      {{/if}}
      <button type="button" class="{{if $_suivi->_canEdit}}edit{{else}}search{{/if}} notext" onclick="modalConsult('{{$_suivi->_id}}')"></button>
    {{/if}}
  </td>
{{/if}}

{{if $_suivi instanceof CTransmissionMedicale}}
  {{if @$show_patient}}
    <td>{{$_suivi->_ref_sejour->_ref_patient}}</td>
    <td class="text">{{$_suivi->_ref_sejour->_ref_last_affectation->_ref_lit->_view}}</td>
  {{/if}}
  <td class="narrow" style="text-align: center;">
    <label title="Transmission">TC</label>
  </td>
  <td class="narrow text">{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_suivi->_ref_user initials=border}}</td>
  <td style="text-align: center;" class="narrow">
    {{assign var=sejour_id_ditto value=$_suivi->sejour_id}}
    {{mb_ditto name="date-$sejour_id_ditto" value=$_suivi->date|date_format:$conf.date}}
  </td>
  <td class="narrow">{{$_suivi->date|date_format:$conf.time}}</td>
  {{if $show_target}}
    <td class="text" style="height: 22px;">
      {{if $_suivi->object_id && $_suivi->object_class}}
        {{assign var=classes value=' '|explode:"CPrescriptionLineMedicament CPrescriptionLineElement CAdministration CPrescriptionLineMix"}}
        {{if in_array($_suivi->object_class, $classes)}}
          <span
            title="{{$_suivi->_ref_object->_view}} {{if $_suivi->_ref_object instanceof CPrescriptionLineElement && $_suivi->_ref_object->commentaire}}({{$_suivi->_ref_object->commentaire}}){{/if}}"
            style="float: left; border: 2px solid #800; width: 5px; height: 11px; margin-right: 3px;">
          </span>
        {{/if}}
        {{if (!$readonly && $_suivi->_canEdit) || $force_new}}
          <a href="#1" onclick="
        {{if $force_new}}
          Control.Modal.close();
        {{/if}}
          if (window.addTransmission) {
          addTransmission('{{$_suivi->sejour_id}}', '{{$app->user_id}}', null, '{{$_suivi->object_id}}', '{{$_suivi->object_class}}');
          }"
          >
        {{/if}}

        {{if !in_array($_suivi->object_class, $classes)}}
          {{$_suivi->_ref_object->_view}}
        {{/if}}
        {{if $_suivi->object_class == "CPrescriptionLineMedicament"}}
          [{{$_suivi->_ref_object->_ref_produit->_ref_ATC_2_libelle}}]
        {{/if}}

        {{if $_suivi->object_class == "CPrescriptionLineElement"}}
          [{{$_suivi->_ref_object->_ref_element_prescription->_ref_category_prescription->_view}}]
        {{/if}}

        {{if $_suivi->object_class == "CAdministration"}}
          {{if $_suivi->_ref_object->object_class == "CPrescriptionLineMedicament"}}
            [{{$_suivi->_ref_object->_ref_object->_ref_produit->_ref_ATC_2_libelle}}]
          {{/if}}

          {{if $_suivi->_ref_object->object_class == "CPrescriptionLineElement"}}
            [{{$_suivi->_ref_object->_ref_object->_ref_element_prescription->_ref_category_prescription->_view}}]
          {{/if}}
        {{/if}}

        {{if (!$readonly && $_suivi->_canEdit) || $force_new}}
          </a>
        {{/if}}
      {{/if}}
      {{if $_suivi->libelle_ATC}}
        <a href="#1" onclick="
        {{if $force_new}}
          Control.Modal.close();
        {{/if}}
          if (window.addTransmission) {
          addTransmission('{{$_suivi->sejour_id}}', '{{$_suivi->user_id}}', null, null, null, '{{$_suivi->libelle_ATC|smarty:nodefaults|JSAttribute}}');
          }"
          >{{$_suivi->libelle_ATC}}</a>
      {{/if}}
    </td>
  {{/if}}
  <td class="text {{if $_suivi->type}}trans-{{$_suivi->type}}{{/if}} libelle_trans" colspan="3">
    {{mb_value object=$_suivi field=text}}
  </td>

  <td class="text">
    {{if !$readonly && $_suivi->_canEdit}}
      <form name="Del-{{$_suivi->_guid}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
        <input type="hidden" name="dosql" value="do_transmission_aed" />
        <input type="hidden" name="del" value="1" />
        <input type="hidden" name="m" value="dPhospi" />
        <input type="hidden" name="transmission_medicale_id" value="{{$_suivi->_id}}" />
        <input type="hidden" name="sejour_id" value="{{$_suivi->sejour_id}}" />
        <button type="button" class="trash notext" onclick="submitSuivi(this.form, 1)">{{tr}}Delete{{/tr}}</button>
      </form>
      <button type="button" class="edit notext" onclick="addTransmission(null, null, '{{$_suivi->_id}}', null, null, null, 1)" ></button>
    {{/if}}
  </td>

{{/if}}


{{* Tableau de transmissions *}}
{{* Affichage aggrégé dans le volet transmissions, de 1 à 3 objets (D-A-R) *}}

{{if $_suivi|is_array}}
  {{assign var=nb_trans value=0}}
  {{assign var=last_type value=""}}
  {{assign var=last_index value=0}}
  {{foreach from=$_suivi item=_trans_by_type key=type_trans}}
    {{if $type_trans != "0"}}
      {{if $_trans_by_type|@count}}
        {{assign var=last_type value=$type_trans}}
        {{math equation=x-1 x=$_trans_by_type|@count assign=last_index}}
      {{/if}}
      {{math equation=x+y x=$nb_trans y=$_trans_by_type|@count assign=nb_trans}}
    {{/if}}
  {{/foreach}}
  {{assign var=libelle_ATC value=$_suivi[0]->libelle_ATC}}
  {{assign var=key value="`$_suivi[0]->object_class` `$_suivi[0]->object_id`"}}
  {{assign var=locked value=""}}

  {{if isset($last_trans_cible|smarty:nodefaults)}}
    {{if $_suivi[0]->locked && ($libelle_ATC && in_array($last_trans_cible.$libelle_ATC, $_suivi) ||
    ($key != " " && in_array($last_trans_cible.$key, $_suivi)))}}
      {{assign var=locked value="hatching"}}
    {{/if}}
  {{/if}}
  {{if @$show_patient}}
    <td>{{$_suivi[0]->_ref_sejour->_ref_patient}}</td>
    <td class="text">{{$_suivi[0]->_ref_sejour->_ref_last_affectation->_ref_lit->_view}}</td>
  {{/if}}
  {{if $show_type}}
    <td class="narrow {{$locked}}" style="text-align: center;">
      <label title="Transmission">TC</label>
    </td>
  {{/if}}
  <td class="{{$locked}} text" style="width: 10%">
    {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_suivi[0]->_ref_user}}
    <br />
    {{mb_value object=$_suivi[0] field=date}}
  </td>
  {{if $show_target}}
    <td class="text libelle_trans {{$locked}}" style="height: 22px;">
      {{if $_suivi[0]->object_id && $_suivi[0]->object_class}}
        {{assign var=classes value=' '|explode:"CPrescriptionLineMedicament CPrescriptionLineElement CAdministration CPrescriptionLineMix"}}
        {{if in_array($_suivi[0]->object_class, $classes)}}
          <span
            title="{{$_suivi[0]->_ref_object->_view}} {{if $_suivi[0]->_ref_object instanceof CPrescriptionLineElement && $_suivi[0]->_ref_object->commentaire}}({{$_suivi[0]->_ref_object->commentaire}}){{/if}}"
            style="float: left; border: 2px solid #800; width: 5px; height: 11px; margin-right: 3px;">
          </span>
        {{/if}}
        {{if $locked || $trans_compact}}
          <strong>
        {{/if}}
        {{if $show_link}}
          <a href="#1"
          {{if $locked || $trans_compact}}
            onclick="showTrans('{{$_suivi[0]->_id}}' {{if !$locked}}, 1{{/if}})"
          {{else}}
            onclick="if (window.addTransmission) { addTransmission('{{$_suivi[0]->sejour_id}}', '{{$app->user_id}}', null, '{{$_suivi[0]->object_id}}', '{{$_suivi[0]->object_class}}'); }"
          {{/if}}>
        {{/if}}
        {{if !in_array($_suivi[0]->object_class, $classes)}}
          {{$_suivi[0]->_ref_object->_view}}
        {{/if}}
        {{if $_suivi[0]->object_class == "CPrescriptionLineMedicament"}}
          [{{$_suivi[0]->_ref_object->_ref_produit->_ref_ATC_2_libelle}}]
        {{/if}}

        {{if $_suivi[0]->object_class == "CPrescriptionLineElement"}}
          [{{$_suivi[0]->_ref_object->_ref_element_prescription->_ref_category_prescription->_view}}]
        {{/if}}

        {{if $_suivi[0]->object_class == "CAdministration"}}
          {{if $_suivi[0]->_ref_object->object_class == "CPrescriptionLineMedicament"}}
            [{{$_suivi[0]->_ref_object->_ref_object->_ref_produit->_ref_ATC_2_libelle}}]
          {{/if}}

          {{if $_suivi[0]->_ref_object->object_class == "CPrescriptionLineElement"}}
            [{{$_suivi[0]->_ref_object->_ref_object->_ref_element_prescription->_ref_category_prescription->_view}}]
          {{/if}}
        {{/if}}
        {{if $locked || $trans_compact}}
          </strong>
        {{/if}}
        {{if $show_link}}
          </a>
        {{/if}}
      {{/if}}
      {{if $libelle_ATC}}
        {{if $locked || $trans_compact}}
          <strong>
        {{/if}}
        {{if $show_link}}
          <a href="#1"
          {{if $locked || $trans_compact}}
            onclick="showTrans('{{$_suivi[0]->_id}}' {{if !$locked}}, 1{{/if}})"
          {{else}}
            onclick="if (window.addTransmission) { addTransmission('{{$_suivi[0]->sejour_id}}', '{{$_suivi[0]->user_id}}', null, null, null, '{{$_suivi[0]->libelle_ATC|smarty:nodefaults|JSAttribute}}'); }"
          {{/if}}
          >
        {{/if}}
        {{$_suivi[0]->libelle_ATC}}
        {{if $locked || $trans_compact}}
          </strong>
        {{/if}}
        {{if $show_link}}
          </a>
        {{/if}}
      {{/if}}
    </td>
  {{/if}}
  {{if $locked}}
    <td class="hatching" colspan="3" style="text-align: center"></td>
    <td class="hatching">
      <button type="button" class="unlock notext" title="Réouvrir la cible" onclick="toggleLockCible('{{$_suivi[0]->_id}}', 0)"></button>
    </td>
  {{else}}
    {{foreach from=$_suivi item=_trans_by_type key=type_trans}}
      {{if $type_trans != "0"}}
        <td style="width: 18%; page-break-inside: avoid;">
          {{if is_array($_trans_by_type)}}
            {{* Fusion de transmissions médicales *}}
            {{if $_trans_by_type|@count > 1}}
              {{assign var=transmissions_ids value=""}}
              {{foreach from=$_trans_by_type item=_trans name=_trans}}
                {{if $smarty.foreach._trans.first}}
                  {{assign var=transmissions_ids value=$_trans->_id}}
                {{else}}
                  {{assign var=transmissions_ids value="$transmissions_ids-`$_trans->_id`"}}
                {{/if}}
              {{/foreach}}
              <button type="button" class="merge notext" style="float: right;" onclick="mergeTrans('{{$transmissions_ids}}')"></button>
            {{/if}}
            {{foreach from=$_trans_by_type item=_trans}}
              {{mb_value object=$_trans field=text}}
              <br />
            {{/foreach}}
          {{/if}}
        </td>
      {{/if}}
    {{/foreach}}
    {{if !$readonly}}
      <td class="nowrap">
        {{if $_suivi.data|@count <= 1 && $_suivi.action|@count <= 1 && $_suivi.result|@count <= 1}}
          {{if $_suivi[0]->_canEdit}}
            <form name="Del-{{$_suivi[0]->_guid}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this);">
              <input type="hidden" name="m" value="hospi" />
              <input type="hidden" name="del" value="1" />
              {{if $_suivi|@count == 1}}
                <input type="hidden" name="dosql" value="do_transmission_aed" />
              {{else}}
                <input type="hidden" name="dosql" value="do_multi_transmission_aed" />
              {{/if}}
              {{if $nb_trans == 1}}
                <input type="hidden" name="transmission_medicale_id" value="{{$_suivi[0]->_id}}" />
              {{/if}}
              {{foreach from=$_suivi item=_trans_by_type key=type_trans}}
                {{if $type_trans != "0" && $_trans_by_type|@count}}
                  <input type="hidden" name="{{$_trans_by_type[0]->type}}_id" value="{{$_trans_by_type[0]->_id}}"/>
                {{/if}}
              {{/foreach}}
              <input type="hidden" name="sejour_id" value="{{$_suivi[0]->sejour_id}}" />
              <button type="button" class="trash notext"
                      onclick="confirmDeletion(this.form,
                        {typeName:'la/les transmission(s)',
                        ajax: true,
                        callback: function() { submitSuivi(getForm('Del-{{$_suivi[0]->_guid}}'), 1); } })"></button>
            </form>
            {{if $nb_trans == 1}}
              <button type="button" class="edit notext" onclick="addTransmission('{{$_suivi[0]->sejour_id}}', null, '{{$_suivi[0]->_id}}', null, null, null, 1)"></button>
            {{else}}
              <button type="button" class="edit notext" onclick="addTransmission('{{$_suivi[0]->sejour_id}}', null, {
              {{assign var=is_first_trans value=1}}
              {{foreach from=$_suivi item=_trans_by_type key=type_trans name=_trans}}
                {{if $type_trans != "0" && isset($_trans_by_type.0|smarty:nodefaults)}}
                  {{assign var=first_trans value=$_trans_by_type.0}}
                  {{if !$is_first_trans}},{{/if}}
                  {{$first_trans->type}}_id: '{{$first_trans->_id}}'
                  {{assign var=is_first_trans value=0}}
                {{/if}}
              {{/foreach}}
                })"></button>
            {{/if}}
            {{if isset($last_trans_cible|smarty:nodefaults)}}
              {{if ($libelle_ATC && in_array($last_trans_cible.$libelle_ATC, $_suivi)) ||
              ($key != " " && in_array($last_trans_cible.$key, $_suivi))}}
                {{assign var=last_trans value=$_suivi.$last_type.$last_index}}
                <button type="button" class="lock notext" title="Fermer la cible"
                        onclick="toggleLockCible('{{$last_trans->_id}}', 1)"></button>
              {{/if}}
            {{/if}}
          {{/if}}
        {{/if}}
      </td>
    {{/if}}
  {{/if}}
{{/if}}