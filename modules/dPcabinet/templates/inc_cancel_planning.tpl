<!-- $Id: $ -->

<script type="text/javascript">

CancelAction = {
  action: null,
  form: null,

  checkAll: function(input) {
    $$('input.consult').each(function(element) {
      element.checked = input.checked;
    });
  },
  
  confirm: function(button, action) {
    $('cell_motif_annulation').hide();
    $$('div.confirm').invoke('hide');
    $$('div.'+action).invoke('show');

    if (action == 'cancel-1') {
      $('cell_motif_annulation').show();
      $V(button.form.motif_annulation, 'not_arrived');
    }

    Modal.open('following_consultations');
    this.action = action;
    this.form = button.form;
  },
  
  submit: function() {
    var consultation_ids = $$('input.consult:checked').pluck('value');
    consultation_ids.push($V(this.form.consultation_id));
    consultation_ids = consultation_ids.join('-');
    $V(this.form.consultation_ids, consultation_ids);
    
    switch (this.action) {
      case 'cancel-0': $V(this.form.annule, '0'); break;
      case 'cancel-1': $V(this.form.annule, '1'); break;
      case 'deletion': $V(this.form.del   , '1'); break;
    }

    if (checkForm(this.form)) {
      this.form.submit();
    }
  }
}

</script>

{{if $consult->annule}}
  <button class="change" type="button" onclick="CancelAction.confirm(this, 'cancel-0')">
  	{{tr}}Restore{{/tr}}
  </button>
{{else}}
  <button class="cancel" type="button" onclick="CancelAction.confirm(this, 'cancel-1')">
  	{{tr}}Cancel{{/tr}}
  </button>
{{/if}}

{{if $can->admin || !$consult->patient_id}}
<button class="trash" type="button" onclick="CancelAction.confirm(this, 'deletion');">
  {{tr}}Delete{{/tr}}
</button>
{{/if}}

<div id="following_consultations" style="display: none; width: 500px; max-height: 600px; overflow-y: auto;">
  <table class="tbl">
    {{if count($following_consultations)}}
      <tr>
        <td colspan="4" class="text">
          <div class="big-warning">
            <div class="confirm cancel-0"><strong>{{tr}}CConsultation-confirm-cancel-0{{/tr}}</strong></div>
            <div class="confirm cancel-1"><strong>{{tr}}CConsultation-confirm-cancel-1{{/tr}}</strong></div>
            <div class="confirm deletion"><strong>{{tr}}CConsultation-confirm-deletion{{/tr}}</strong></div>
            <div>{{tr}}CConsultation-propose-selection-1{{/tr}}</div>
            <div>{{tr}}CConsultation-propose-selection-2{{/tr}}</div>
          </div>
        </td>
      </tr>

      <tr>
        <th colspan="4" class="title">Rendez-vous suivants</th>
      </tr>
      <tr>
        <th class="narrow">
          <input type="checkbox" value="" onclick="CancelAction.checkAll(this);" />
        </th>
        <th style="text-align: center;">{{mb_label class=CConsultation field=_datetime}}</th>
        <th style="text-align: center;">{{mb_label class=CConsultation field=_praticien_id}}</th>
        <th style="text-align: center;">{{mb_label class=CConsultation field=_etat}}</th>
      </tr>


      {{foreach from=$following_consultations item=_consultation}}
      <tr>
        <td class="narrow">
          <input class="consult" type="checkbox" value="{{$_consultation->_id}}" />
        </td>
        <td>{{mb_value object=$_consultation field=_datetime}}</td>
        <td>{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_consultation->_ref_praticien}}</td>
        <td {{if $_consultation->annule}} class="cancelled" {{/if}}>
          {{mb_value object=$_consultation field=_etat}}
        </td>
      </tr>
      {{/foreach}}

    {{else}}
      <div class="small-warning">
        <div class="confirm cancel-0">{{tr}}CConsultation-confirm-cancel-0{{/tr}}</div>
        <div class="confirm cancel-1">{{tr}}CConsultation-confirm-cancel-1{{/tr}}</div>
        <div class="confirm deletion">{{tr}}CConsultation-confirm-deletion{{/tr}}</div>
      </div>
    {{/if}}

    <tr id="cell_motif_annulation" style="display: none;">
      <td style="text-align: right" colspan="2"><strong>{{mb_title object=$consult field=motif_annulation}}</strong></td>
      <td colspan="2">
        <input type="radio" name="_motif_annulation" value="not_arrived" checked="checked" onclick="CancelAction.form.motif_annulation.value = this.value;">
        <label for="editFrm__motif_annulation_not_arrived" id="labelFor_editFrm__motif_annulation_not_arrived" class="">{{tr}}CConsultation.motif_annulation.not_arrived{{/tr}}</label> <br/>

        <input type="radio" name="_motif_annulation" value="by_patient" onclick="CancelAction.form.motif_annulation.value = this.value;">
        <label for="editFrm__motif_annulation_by_patient" id="labelFor_editFrm__motif_annulation_by_patient" class="">{{tr}}CConsultation.motif_annulation.by_patient{{/tr}}</label>
      </td>
    </tr>

    <tr>
      <td colspan="4" class="button">
        <button type="button" class="cancel" onclick="Control.Modal.close();">{{tr}}Cancel{{/tr}}</button>
        <button type="button" class="tick"   onclick="CancelAction.submit();">{{tr}}Validate{{/tr}}</button>
      </td>
    </tr>

  </table>
</div>
