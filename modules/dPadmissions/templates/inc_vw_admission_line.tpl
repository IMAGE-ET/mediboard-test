{{assign var=patient value=$_sejour->_ref_patient}}

<td>
  {{if $canAdmissions->edit}}
    {{if $conf.dPplanningOp.COperation.verif_cote}}
      {{foreach from=$_sejour->_ref_operations item=curr_op}}
        {{if $curr_op->cote == "droit" || $curr_op->cote == "gauche"}}
          <form name="editCoteOp{{$curr_op->_id}}" action="?" method="post" class="prepared">
            <input type="hidden" name="m" value="planningOp" />
            <input type="hidden" name="dosql" value="do_planning_aed" />
            {{mb_key object=$curr_op}}
            {{mb_label object=$curr_op field="cote_admission"}} :
            {{mb_field emptyLabel="Choose" object=$curr_op field="cote_admission" onchange="submitCote(this.form);"}}
          </form>
          <br />
        {{/if}}
      {{/foreach}}
    {{/if}}

    <button
        class="{{if !$_sejour->entree_reelle}}tick{{else}}edit notext{{/if}}"
            onclick="Admissions.validerEntree('{{$_sejour->_id}}',null, reloadAdmissionLine.curry('{{$_sejour->_id}}'))">
      {{if !$_sejour->entree_reelle}}{{tr}}CSejour-admit{{/tr}}{{else}}Modifier Admission{{/if}}
    </button>

    {{if $_sejour->entree_reelle}}
      Entrée réelle : {{mb_value object=$_sejour field=entree_reelle}}<br/>
      {{if $_sejour->mode_sortie}}
        {{tr}}CSejour.mode_entree.{{$_sejour->mode_entree}}{{/tr}}
      {{/if}}
    {{/if}}


  {{elseif $_sejour->entree_reelle}}
    {{if ($_sejour->entree_reelle < $date_min) || ($_sejour->entree_reelle > $date_max)}}
      {{$_sejour->entree_reelle|date_format:$conf.datetime}}
      <br>
    {{else}}
      {{$_sejour->entree_reelle|date_format:$conf.time}}
    {{/if}}
    {{if $_sejour->mode_sortie}}
      <br />
      {{tr}}CSejour.mode_entree.{{$_sejour->mode_entree}}{{/tr}}
    {{/if}}

    {{if $_sejour->etablissement_entree_id}}
      <br />{{$_sejour->_ref_etablissement_provenance}}
    {{/if}}
  {{else}}
    -
  {{/if}}
</td>

<td>
  <input type="checkbox" name="print_doc" value="{{$_sejour->_id}}"/>
</td>

<td colspan="2" class="text">
  {{if $canPlanningOp->read}}
    <div style="float: right;">
      {{if "web100T"|module_active}}
        {{mb_include module=web100T template=inc_button_iframe}}
      {{/if}}

      <button type="button" class="print notext" onclick="Admissions.showDocs('{{$_sejour->_id}}')"></button>

      {{if $conf.dPadmissions.show_deficience}}
        {{mb_include module=patients template=inc_vw_antecedents type=deficience callback="reloadAdmissionLine.curry(`$_sejour->_id`)"
        force_show=true handicap=$_sejour->handicap}}
      {{/if}}

      {{foreach from=$_sejour->_ref_operations item=_op}}
      <a class="action" title="Imprimer la DHE de l'intervention" href="#printDHE"
         onclick="Admissions.printDHE('operation_id', {{$_op->_id}}); return false;">
        <img src="images/icons/print.png" />
      </a>
      {{foreachelse}}
      <a class="action" title="Imprimer la DHE du séjour" href="#printDHE"
         onclick="Admissions.printDHE('sejour_id', {{$_sejour->_id}}); return false;">
        <img src="images/icons/print.png" />
      </a>
      {{/foreach}}

      <a class="action" title="Modifier le séjour" href="#editDHE"
        onclick="Sejour.editModal({{$_sejour->_id}}, reloadAdmissionLine.curry('{{$_sejour->_id}}')); return false;">
        <img src="images/icons/planning.png" />
      </a>

      {{mb_include module=system template=inc_object_notes object=$_sejour}}
    </div>
  {{/if}}

  {{if $patient->_ref_IPP}}
    <form name="editIPP{{$patient->_id}}" method="post" class="prepared">
      <input type="hidden" class="notNull" name="id400" value="{{$patient->_ref_IPP->id400}}" />
      <input type="hidden" class="notNull" name="object_id" value="{{$patient->_id}}" />
      <input type="hidden" class="notNull" name="object_class" value="CPatient" />
    </form>

    {{if $_sejour->_ref_NDA}}
      <form name="editNumdos{{$_sejour->_id}}" method="post" class="prepared">
        <input type="hidden" class="notNull" name="id400" value="{{$_sejour->_ref_NDA->id400}}" size="8" />
        <input type="hidden" class="notNull" name="object_id" value="{{$_sejour->_id}}" />
        <input type="hidden" class="notNull" name="object_class" value="CSejour" />
      </form>
    {{/if}}
  {{/if}}
  {{if "dPsante400"|module_active}}
    {{mb_include module=dPsante400 template=inc_manually_ipp_nda sejour=$_sejour patient=$patient
                    callback=reloadAdmissionLine.curry("`$_sejour->_id`")}}
  {{/if}}
  {{mb_include module=planningOp template=inc_vw_numdos nda_obj=$_sejour _show_numdoss_modal=1}}
  <span class="CPatient-view" onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}');">
    {{$patient}}
  </span>
  {{if "dPpatients CPatient nom_jeune_fille_mandatory"|conf:"CGroups-$g" && $patient->sexe == "f" && !$patient->nom_jeune_fille}}
    <br />
    <div class="small-warning"> Le nom de naissance est obligatoire mais n'a pas été renseigné pour cette patiente (circonstances particulières)</div>
  {{/if}}
</td>

<td class="text">
  {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_sejour->_ref_praticien}}
</td>

<td>
  <span onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_guid}}');">
    {{$_sejour->entree_prevue|date_format:$conf.time}}
    <br />
    {{$_sejour->type|upper|truncate:1:"":true}}
    {{$_sejour->_ref_operations|@count}} Int.
  </span>
</td>

<td>
  {{if !($_sejour->type == 'exte') && !($_sejour->type == 'consult') && $_sejour->annule != 1}}
    {{mb_include template=inc_form_prestations sejour=$_sejour edit=$canAdmissions->edit}}
    {{mb_include module=hospi template=inc_placement_sejour sejour=$_sejour which="last"}}
  {{/if}}
</td>

<td>
  {{if $canAdmissions->edit}}
    <form name="editSaisFrm{{$_sejour->_id}}" action="?" method="post" class="prepared">
      <input type="hidden" name="m" value="planningOp" />
      <input type="hidden" name="dosql" value="do_sejour_aed" />
      {{mb_key object=$_sejour}}
      <input type="hidden" name="patient_id" value="{{$_sejour->patient_id}}" />

      {{if !$_sejour->entree_preparee}}
        <input type="hidden" name="entree_preparee" value="1" />
        <button class="tick" type="button" onclick="submitAdmission(this.form, 1);">
          {{tr}}CSejour-entree_preparee{{/tr}}
        </button>
      {{else}}
        <input type="hidden" name="entree_preparee" value="0" />
        <button class="cancel" type="button" onclick="submitAdmission(this.form, 1);">
          {{tr}}Cancel{{/tr}}
        </button>
      {{/if}}

      {{if ($_sejour->entree_modifiee == 1) && ($conf.dPplanningOp.CSejour.entree_modifiee == 1)}}
        <img src="images/icons/warning.png" title="Le dossier a été modifié, il faut le préparer" />
      {{/if}}
    </form>
  {{else}}
    {{mb_value object=$_sejour field="entree_preparee"}}
  {{/if}}
</td>

<td class="text">
  {{foreach from=$_sejour->_ref_operations item=_op}}
    {{assign var=dossier_anesth value=$_op->_ref_consult_anesth}}
    {{if $dossier_anesth->_id}}
      {{assign var=consult_anesth value=$dossier_anesth->_ref_consultation}}
      <div class="{{if $consult_anesth->chrono == 64}}small-success{{else}}small-info{{/if}}" style="margin: 0;">
        <span onmouseover="ObjectTooltip.createEx(this, '{{$consult_anesth->_guid}}');">
          {{mb_value object=$consult_anesth field=_date}}
        </span>
          <br/>
        {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$consult_anesth->_ref_praticien}}
      </div>
    {{/if}}
  {{/foreach}}
</td>

<td class="button">
  {{if $_sejour->_couvert_cmu}}
    <div><strong>CMU</strong></div>
  {{/if}}
  {{if $_sejour->_couvert_ald}}
    <div><strong {{if $_sejour->ald}}style="color: red;"{{/if}}>ALD</strong></div>
  {{/if}}
</td>

{{if $conf.dPadmissions.show_dh}}
  <td>
    {{foreach from=$_sejour->_ref_operations item=_op}}
      {{if $_op->_ref_actes_ccam|@count}}
        <span style="color: #484;">
          {{foreach from=$_op->_ref_actes_ccam item=_acte}}
            {{if $_acte->montant_depassement}}
              {{if $_acte->code_activite == 1}}
              Chir :
              {{elseif $_acte->code_activite == 4}}
              Anesth :
              {{else}}
              Activité {{$_acte->code_activite}} :
              {{/if}}
              {{mb_value object=$_acte field=montant_depassement}}
              <br />
            {{/if}}
          {{/foreach}}
        </span>
      {{/if}}
      {{if $_op->depassement}}
        Prévu chir : {{mb_value object=$_op field="depassement"}}
        <br />
      {{/if}}
      {{if $_op->depassement_anesth}}
        Prévu anesth : {{mb_value object=$_op field="depassement_anesth"}}
        <br />
      {{/if}}
    {{foreachelse}}
      -
    {{/foreach}}
  </td>
{{/if}}