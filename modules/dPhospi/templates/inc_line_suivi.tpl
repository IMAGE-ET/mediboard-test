{{mb_default var=show_target value=true}}
{{mb_default var=from_lock   value=false}}
{{mb_default var=force_new   value=false}}
{{mb_default var=show_link value=true}}

{{assign var=trans_compact value=$conf.soins.trans_compact}}

{{if $_suivi instanceof CObservationMedicale}}
  {{if @$show_patient}}
  <td><strong>{{$_suivi->_ref_sejour->_ref_patient}}</strong></td>
  <td class="text">{{$_suivi->_ref_sejour->_ref_last_affectation->_ref_lit->_view}}</td>
  {{/if}}
  <td><strong>Obs</strong></td>
  <td class="narrow">
    {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_suivi->_ref_user initials=border}}
  </td>
  <td  style="text-align: center">
    <strong>
      {{mb_ditto name=date value=$_suivi->date|date_format:$conf.date}}
    </strong>
  </td>
  <td>{{$_suivi->date|date_format:$conf.time}}</td>
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
  <td>Cst</td>
  <td class="narrow">
    {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_suivi->_ref_user initials=border}}
  </td>
  <td style="text-align: center">
     {{mb_ditto name=date value=$_suivi->datetime|date_format:$conf.date}}
  </td>
  <td>{{$_suivi->datetime|date_format:$conf.time}}</td>
  <td colspan="4" class="text">
    {{foreach from=$params key=_key item=_field name="const"}}
      {{if $_suivi->$_key != null && $_key|substr:0:1 != "_"}}
        {{mb_title object=$_suivi field=$_key}} :
        {{if array_key_exists("formfields", $_field)}}
          {{mb_value object=$_suivi field=$_field.formfields.0 size="2" }} / 
          {{mb_value object=$_suivi field=$_field.formfields.1 size="2" }}
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
  <td><strong>Presc</strong></td>
  <td class="narrow">
    {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_suivi->_ref_praticien initials=border}}
  </td>
  <td style="text-align: center">
    {{mb_ditto name=date value=$_suivi->debut|date_format:$conf.date}}
  </td>
  <td>{{mb_value object=$_suivi field="time_debut"}}</td>
  <td colspan="4" {{if $_suivi->_count.transmissions}} class="arretee" {{/if}}>
    {{if !$readonly}}
      <button type="button" class="tick" onclick="addTransmissionAdm('{{$_suivi->_id}}','{{$_suivi->_class}}');" style="float: right;">R�aliser ({{$_suivi->_count.transmissions}})</button>
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
  <td class="text">
    <strong onmouseover="ObjectTooltip.createEx(this, '{{$_suivi->_guid}}')">
      {{if $_suivi->type == "entree"}}
        Obs. entr�e
      {{elseif $_suivi->_refs_dossiers_anesth|@count >= 1}}
        Cs anesth.
      {{else}}
        Cs
      {{/if}}
    </strong>
  </td>
  <td class="narrow">
    {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_suivi->_ref_praticien initials=border}}
  </td>
  <td style="text-align: center">
    {{mb_ditto name=date value=$_suivi->_datetime|date_format:$conf.date}}
  </td>
  <td>{{$_suivi->_datetime|date_format:$conf.time}}</td>
  <td></td>
  <td class="text" colspan="3">
    {{if $_suivi->_refs_dossiers_anesth|@count}}
      {{foreach from=$_suivi->_refs_dossiers_anesth item=_dossier_anesth}}
        <strong>
          Dossier d'anesth�sie
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
      {{if $_suivi->rques}}
        <u>Remarques :</u> {{mb_value object=$_suivi field=rques}} <br />
      {{/if}}
      {{if $_suivi->examen}}
        <u>Examen clinique :</u> {{mb_value object=$_suivi field=examen}} <br />
      {{/if}}
      {{if $_suivi->traitement}}
        <u>Traitement :</u> {{mb_value object=$_suivi field=traitement}} <br />
      {{/if}}
      {{if $conf.dPcabinet.CConsultation.show_histoire_maladie && $_suivi->histoire_maladie}}
        <u>Histoire de la maladie :</u> {{mb_value object=$_suivi field=histoire_maladie}} <br />
      {{/if}}
      {{if $conf.dPcabinet.CConsultation.show_conclusion && $_suivi->conclusion}}
        <u>Au total :</u> {{mb_value object=$_suivi field=conclusion}}
      {{/if}}
    {{/if}}
  </td>
  <td>
    {{if !$readonly}}
      <button type="button" class="{{if $_suivi->_canEdit}}edit{{else}}search{{/if}} notext" onclick="modalConsult('{{$_suivi->_id}}')"></button>
    {{/if}}
  </td>
{{/if}}

{{if $_suivi instanceof CTransmissionMedicale}}
  {{if @$show_patient}}
    <td>{{$_suivi->_ref_sejour->_ref_patient}}</td>
    <td class="text">{{$_suivi->_ref_sejour->_ref_last_affectation->_ref_lit->_view}}</td>
  {{/if}}
  <td class="narrow">TC</td>
  <td class="narrow">{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_suivi->_ref_user initials=border}}</td>
  <td style="text-align: center;" class="narrow">
    {{mb_ditto name=date value=$_suivi->date|date_format:$conf.date}}
  </td>
  <td class="narrow">{{$_suivi->date|date_format:$conf.time}}</td>
  <td class="text" style="height: 22px;">
   {{if $show_target}}
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
  {{/if}} 

  </td>
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
{{* Affichage aggr�g� dans le volet transmissions, de 1 � 3 objets (D-A-R) *}}

{{if $_suivi|is_array}}
  {{assign var=nb_trans value=$_suivi|@count}}
  {{assign var=libelle_ATC value=$_suivi[0]->libelle_ATC}}
  {{assign var=key value="`$_suivi[0]->object_class` `$_suivi[0]->object_id`"}}
  {{assign var=locked value=""}}

  {{if isset($last_trans_cible|smarty:nodefaults)}}
    {{if $_suivi[0]->locked && ($libelle_ATC && in_array($last_trans_cible.$libelle_ATC, $_suivi) ||
        ($key != " " && in_array($last_trans_cible.$key, $_suivi)))}}
      {{assign var=locked value="hatching"}}
      {{assign var=log value=$_suivi[0]->_log_lock}}
    {{/if}}
  {{/if}}
  {{if @$show_patient}}
    <td>{{$_suivi[0]->_ref_sejour->_ref_patient}}</td>
    <td class="text">{{$_suivi[0]->_ref_sejour->_ref_last_affectation->_ref_lit->_view}}</td>
  {{/if}}

  <td class="narrow {{$locked}}">
    TC
  </td>
  <td class="narrow {{$locked}}">
    {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_suivi[0]->_ref_user initials=border}}
  </td>
  <td style="text-align: center;" class="narrow {{$locked}}">
    {{if $locked}}
      {{mb_ditto name=date value=$log->date|date_format:$conf.date}}
    {{else}}
      {{mb_ditto name=date value=$_suivi[0]->date|date_format:$conf.date}}
    {{/if}}
  </td>
  <td class="narrow {{$locked}}">
    {{if $locked}}
      {{$log->date|date_format:$conf.time}}
    {{else}}
      {{$_suivi[0]->date|date_format:$conf.time}}
    {{/if}}
  </td>
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
  {{if $locked}}
    <td class="hatching" colspan="3" style="text-align: center"></td>
    <td class="hatching">
      <button type="button" class="unlock notext" title="R�ouvrir la cible" onclick="toggleLockCible('{{$_suivi[0]->_id}}', 0)"></button>
    </td>
  {{else}}
    {{if $_suivi|@count}}
      {{if $_suivi[0]->type != "data"}}
        <td></td>
        {{if $_suivi[0]->type == "result"}}
          <td></td>
        {{/if}}
      {{/if}}
    {{/if}}
    {{foreach from=$_suivi item=_trans name=foreach_trans}}
      {{if $smarty.foreach.foreach_trans.index == 1 && $_suivi[0]->type == "data" && $_trans->type == "result"}}
        <td></td>
      {{/if}}
      <td>
        {{mb_value object=$_trans field=text}}
      </td>
    {{/foreach}}
    {{if $nb_trans == 1}}
      {{if $_suivi[0]->type != "result"}}
        <td></td>
        {{if $_suivi[0]->type != "action"}}
          <td></td>
        {{/if}}
      {{/if}}
    {{elseif $nb_trans == 2 && $_suivi[1]->type == "action"}}
      <td></td>
    {{/if}}
    <td class="nowrap">
      {{if !$readonly && $_suivi[0]->_canEdit}}
        <form name="Del-{{$_suivi[0]->_guid}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this);">
          {{if $_suivi|@count == 1}}
            <input type="hidden" name="dosql" value="do_transmission_aed" />
          {{else}}
            <input type="hidden" name="dosql" value="do_multi_transmission_aed" />
          {{/if}}
          <input type="hidden" name="del" value="1" />
          <input type="hidden" name="m" value="dPhospi" />
          {{if $nb_trans == 1}}
            <input type="hidden" name="transmission_medicale_id" value="{{$_suivi[0]->_id}}" />
          {{/if}}
          {{if $nb_trans >= 2}}
            <input type="hidden" name="{{$_suivi[0]->type}}_id" value="{{$_suivi[0]->_id}}" />
            <input type="hidden" name="{{$_suivi[1]->type}}_id" value="{{$_suivi[1]->_id}}" />
          {{/if}}
          {{if $nb_trans == 3}}
            <input type="hidden" name="{{$_suivi[2]->type}}_id" value="{{$_suivi[2]->_id}}" />
          {{/if}}
          <input type="hidden" name="sejour_id" value="{{$_suivi[0]->sejour_id}}" />
          <button type="button" class="trash notext"
           onclick="confirmDeletion(this.form,
            {typeName:'la/les transmission(s)',
              ajax: true,
              callback: function() { submitSuivi(getForm('Del-{{$_suivi[0]->_guid}}'), 1); } })"></button>
        </form>
        {{if $nb_trans == 1}}
          <button type="button" class="edit notext" onclick="addTransmission('{{$_suivi[0]->sejour_id}}', null, '{{$_suivi[0]->_id}}', null, null, null, 1)"></button>
        {{elseif $nb_trans == 2}}
          <button type="button" class="edit notext" onclick="addTransmission('{{$_suivi[0]->sejour_id}}', null, { {{$_suivi[0]->type}}_id: '{{$_suivi[0]->_id}}', {{$_suivi[1]->type}}_id: '{{$_suivi[1]->_id}}' }, null, null, null, 1)"></button>
        {{else}}
          <button type="button" class="edit notext" onclick="addTransmission('{{$_suivi[0]->sejour_id}}', null, { {{$_suivi[0]->type}}_id: '{{$_suivi[0]->_id}}', {{$_suivi[1]->type}}_id: '{{$_suivi[1]->_id}}', {{$_suivi[2]->type}}_id: '{{$_suivi[2]->_id}}' }, null, null, null, 1)"></button>
        {{/if}}
        {{if isset($last_trans_cible|smarty:nodefaults)}}
          {{if ($libelle_ATC && in_array($last_trans_cible.$libelle_ATC, $_suivi)) ||
               ($key != " " && in_array($last_trans_cible.$key, $_suivi))}}
            {{math equation=x-1 x=$nb_trans assign=last_index}}
            <button type="button" class="lock notext" title="Fermer la cible"
              onclick="toggleLockCible('{{$_suivi[$last_index]->_id}}', 1)"></button>
          {{/if}}
        {{/if}}
      {{/if}}
    </td>
  {{/if}}
{{/if}}