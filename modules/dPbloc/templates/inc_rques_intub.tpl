{{assign var=sejour value=$operation->_ref_sejour}}

{{if $operation->rques || ($sejour && $sejour->_id && $sejour->ATNC != "") || ($consult_anesth && $consult_anesth->_intub_difficile)}}
  <div class="small-warning">
    {{mb_value object=$operation field=rques}}
    {{if $consult_anesth->_id && $consult_anesth->_intub_difficile}}
      <div style="font-weight: bold; color:#f00;">
        {{tr}}CConsultAnesth-_intub_difficile{{/tr}}
      </div>
    {{/if}}
  </div>
  {{if $sejour && $sejour->_id && $sejour->ATNC != ""}}
    <div style="font-weight: bold; {{if $sejour->ATNC == 1}}color: #f00;{{/if}}"
         class="{{if $sejour->ATNC == 1}}small-warning{{else}}small-info{{/if}}">
      {{if $sejour->ATNC}}Risque ATNC{{else}}Aucun risque ATNC{{/if}}
    </div>
  {{/if}}
{{/if}}