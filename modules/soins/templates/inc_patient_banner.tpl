{{mb_default var=mode_pharma value=0}}
{{mb_default var=mode_protocole value=0}}
{{mb_default var=operation_id value=0}}
{{mb_default var=rpu value=null}}

{{assign var=prescription value=$sejour->_ref_prescription_sejour}}
{{assign var=dossier_medical value=$patient->_ref_dossier_medical}}
{{assign var=dossier_medical_sejour value=$sejour->_ref_dossier_medical}}
{{assign var=antecedents value=$dossier_medical->_ref_antecedents_by_type}}
{{if $dossier_medical_sejour}}
  {{assign var=antecedents_sejour value=$dossier_medical_sejour->_ref_antecedents_by_type}}
{{else}}
  {{assign var=antecedents_sejour value=0}}
{{/if}}
{{assign var=sejour_id value=$sejour->_id}}
{{assign var=conf_preselect_prat value="dPprescription general preselection_praticien_auto"|conf:"CGroups-$g"}}
{{assign var=is_executant_prescription value=CAppUI::$user->isExecutantPrescription()}}

{{if 'oncomip'|module_active}}
  <script>
    Main.add(function() {
      var url = new Url('oncomip', 'ajax_search_patient');
      url.addParam('patient_id', {{$patient->_id}});
      url.requestUpdate('oncomip_search_patient', {
        onComplete: function() {
          $('oncomip_search_patient').show();
        }
      });
    });
  </script>
{{/if}}

<table class="tbl">
  <tr>
    <th class="title text" style="text-align: left; border: none; width: 80px;">
      {{mb_include module=system template=inc_object_notes object=$patient}}
      <a href="?m=dPpatients&tab=vw_full_patients&patient_id={{$patient->_id}}">
        {{mb_include module="patients" template=inc_vw_photo_identite mode="read" size=52}}
      </a>
    </th>
    <th class="title text" style="border: none;">
      <form name="actionPat" action="?" method="get">
        <input type="hidden" name="m" value="patients" />
        <input type="hidden" name="tab" value="vw_idx_patients" />
        <input type="hidden" name="patient_id" value="{{$patient->_id}}" />
        <h2 class="title">
          <span style="font-size: 0.8em;" onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}')">
            {{$patient->_view}}
          </span>
          <span id="atcd_allergies">
            {{mb_include module=soins template=inc_antecedents_allergies patient_guid=$patient->_guid}}
          </span>
          {{if "maternite"|module_active}}
            {{mb_include module=maternite template=inc_input_grossesse object=$sejour modify_grossesse=0}}
          {{/if}}
          {{if $sejour->isolement}}
            <span class="texticon texticon-isolement" title="Isolement">Isol</span>
          {{/if}}
          <br/>
          <span style="font-size: 0.7em;" onmouseover="ObjectTooltip.createEx(this, '{{$sejour->_guid}}')">
            {{if $rpu}}
              Admission du {{$rpu->_entree|date_format:"%d/%m/%Y"}}
            {{else}}
              {{$sejour->_shortview|replace:"Du":"Séjour du"}}
            {{/if}}
          </span>
          -
          <span style="font-size: 0.6em;">
            {{$sejour->_motif_complet|spancate:30:"...":false}}
            {{* N'afficher le libellé d'intervention que lorsqu'il y en a qu'une *}}
            {{if $sejour->libelle && $sejour->_ref_operations|@count == 1}}
              &mdash;
              {{foreach from=$sejour->_ref_operations item=_op name=op}}
                {{$_op->libelle|spancate:30:"...":false}} {{if !$smarty.foreach.op.last}};{{/if}}
              {{/foreach}}
            {{/if}}
          </span>
          {{if $sejour->_jour_op}}
            {{foreach from=$sejour->_jour_op item=_info_jour_op}}
              <span style="font-size: 0.8em;" onmouseover="ObjectTooltip.createEx(this, '{{$_info_jour_op.operation_guid}}');">(J{{$_info_jour_op.jour_op}})</span>
            {{/foreach}}
          {{/if}}
          {{if $sejour->_ref_curr_affectation->_id}}
            <span style="font-size: 0.6em;">
              {{$sejour->_ref_curr_affectation->_ref_lit}}
            </span>
          {{/if}}

          {{if $prescription && $prescription->_ref_lines_important|@count}}
            <br/>
            {{mb_include module=prescription template=vw_line_important lines=$prescription->_ref_lines_important}}
          {{/if}}
        </h2>
      </form>
    </th>
    <th class="title text" style="text-align: right; border: none; width: 10%;">
      {{mb_include module=system template=inc_object_idsante400 object=$patient}}
      {{mb_include module=system template=inc_object_history object=$patient}}

      <a href="#print-{{$patient->_guid}}" onclick="Patient.print('{{$patient->_id}}')">
        <img src="images/icons/print.png" alt="imprimer" title="Imprimer la fiche patient" />
      </a>

      {{if $can->edit}}
        <a href="#edit-{{$patient->_guid}}" onclick="Patient.edit('{{$patient->_id}}')">
          <img src="images/icons/edit.png" alt="modifier" title="Modifier le patient" />
        </a>
      {{/if}}

      {{if $app->user_prefs.vCardExport}}
        <a href="#export-{{$patient->_guid}}" onclick="Patient.exportVcard('{{$patient->_id}}')">
          <img src="images/icons/vcard.png" alt="export" title="Exporter le patient" />
        </a>
      {{/if}}

      {{mb_include module=dPpatients template=inc_view_ins_patient patient=$patient}}

      {{if 'oncomip'|module_active}}
        <span id="oncomip_search_patient" style="display: none;"></span>
      {{/if}}
    </th>
  </tr>
  {{if $conf.ref_pays == "2" && $sejour->technique_reanimation}}
    <tr>
      <td colspan="3">
        <div title="{{tr}}CSejour-technique_reanimation-desc{{/tr}}" class="small-warning">{{$sejour->technique_reanimation}}</div>
      </td>
    </tr>
  {{/if}}
</table>

<table class="tbl">
  {{mb_include module=soins template=inc_infos_patients_soins add_class=1}}
</table>
