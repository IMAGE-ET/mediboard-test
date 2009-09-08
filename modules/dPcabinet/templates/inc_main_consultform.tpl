{{if !@$readonly}}
{{assign var=readonly value=0}}
{{/if}}

<!-- Fiches d'examens -->
{{mb_include_script module="dPcabinet" script="exam_dialog"}}
<script type="text/javascript">
  ExamDialog.register('{{$consult->_id}}','{{$consult->_class_name}}');
</script>

{{if $consult->_id}}
<form class="watch" name="editFrmExams" action="?m={{$m}}" method="post" onsubmit="return checkForm(this);">
<input type="hidden" name="m" value="dPcabinet" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="dosql" value="do_consultation_aed" />
{{mb_field object=$consult field="consultation_id" hidden=1 prop=""}}

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
        <button class="new notext" title="Ajouter une aide à la saisie" style="float: right;" type="button" onclick="addHelp('CConsultation', this.form.{{$field}})">
          Nouveau
        </button>
        <select name="_helpers_{{$field}}" size="1"  style="width: 130px; float: right;" onchange="pasteHelperContent(this); this.form.{{$field}}.onchange();">
          <option value="">&mdash; Choisir une aide</option>
          {{html_options options=$consult->_aides.$field.no_enum}}
        </select>
        {{/if}}
        {{mb_label object=$consult field=$field}}
      </th>
    </tr>
    <tr>
      <td>
        {{if $readonly}}
          {{mb_value object=$consult field=$field}}
        {{else}}
          {{mb_field object=$consult field=$field rows="5" onchange="submitFormAjax(this.form, 'systemMsg');"}}
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