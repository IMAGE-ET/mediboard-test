{{assign var=aide_autocomplete value=$dPconfig.dPcabinet.CConsultation.aide_autocomplete}}
{{if !@$readonly}}
  {{assign var=readonly value=0}}
{{/if}}

<!-- Fiches d'examens -->
{{mb_include_script module="dPcabinet" script="exam_dialog"}}

<script type="text/javascript">
  {{if !$readonly}}
    ExamDialog.register('{{$consult->_id}}','{{$consult->_class_name}}');
  {{/if}}

  onExamComplete = function(){
    FormObserver.changes = 0;
  }
</script>

{{if $consult->_id}}
<form class="watched" name="editFrmExams" action="?m={{$m}}" method="post" onsubmit="return onSubmitFormAjax(this, {onComplete: onExamComplete})">
<input type="hidden" name="m" value="dPcabinet" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="dosql" value="do_consultation_aed" />
{{mb_key object=$consult}}

<table class="main">
  {{foreach name=exam_fields from=$consult->_exam_fields key=current item=field}}
  {{assign var=last value=$smarty.foreach.exam_fields.last}}
  
  {{if !$last && $current mod 2 == 0}}
  <tr>
    <td class="halfPane">
  {{elseif $current mod 2 == 1}}
    <td class="halfPane">
  {{else}}
  <tr>
    <td colspan="2">
  {{/if}}
  {{* Beginning *}}
  
  <table class="form">
    <tr>
      <th class="category">
        {{if !$readonly}}
          {{if $aide_autocomplete == 1}}
            <script type="text/javascript">
              Main.add(function() {
                new AideSaisie.AutoComplete(getForm("editFrmExams").elements.{{$field}}, {
                  objectClass: "{{$consult->_class_name}}",
                  userId: "{{$consult->_ref_chir->_id}}",
                  userView: "{{$consult->_ref_chir->_view}}",
                  contextUserId: "{{$consult->_ref_chir->_id}}",
                  timestamp: "{{$dPconfig.dPcompteRendu.CCompteRendu.timestamp}}",
                  validate: function(){ getForm("editFrmExams").onsubmit(); },
                  resetSearchField: false,
                  resetDependFields: false,
                  validateOnBlur: false
                });
              });
            </script>
          {{else}}
            <button class="submit notext" style="float: left;" type="submit" tabindex="1000">
              {{tr}}Save{{/tr}}
            </button>
            <button class="new notext" title="Ajouter une aide à la saisie" style="float: right;" 
                    type="button" onclick="addHelp('CConsultation', this.form.{{$field}})">
              {{tr}}New{{/tr}}
            </button>
            <select name="_helpers_{{$field}}" style="width: 130px; float: right;" 
                    onchange="pasteHelperContent(this); this.form.onsubmit()">
              <option value="">&mdash; Aide</option>
              {{html_options options=$consult->_aides.$field.no_enum}}
            </select>
          {{/if}}
        {{/if}}
        {{mb_label object=$consult field=$field}}
      </th>
    </tr>
    <tr>
      <td>
        {{if $readonly}}
          {{mb_value object=$consult field=$field}}
        {{else}}
          {{mb_field object=$consult field=$field rows="4" onchange="this.form.onsubmit()"}}
        {{/if}}
      </td>
    </tr>
  </table>

  {{* End *}}
  {{if !$last && $current mod 2 == 0}}
    </td>
  {{elseif $current mod 2 == 1}}
    </td>
  </tr>
  {{else}}
    </td>
  </tr>
  {{/if}}
{{/foreach}}
</table>
</form>

{{else}}
<div class="small-info">Consultation non réalisée</div>
{{/if}}