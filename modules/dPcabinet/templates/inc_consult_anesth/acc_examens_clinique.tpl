<table class="main form">
  <tr>
    <td>
      <!-- Fiches d'examens -->
      {{mb_include_script module="dPcabinet" script="exam_dialog"}}
      <script type="text/javascript">
        ExamDialog.register('{{$consult->_id}}','{{$consult->_class_name}}');
      </script>
    </td>
  </tr>
  <tr>
    <td>
      <form name="editAnesthExamenCardio" action="?m={{$m}}" method="post" onsubmit="return onSubmitFormAjax(this);">
        <input type="hidden" name="m" value="dPcabinet" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="dosql" value="do_consult_anesth_aed" />
        {{mb_key object=$consult_anesth}}
				
        {{mb_label object=$consult_anesth field="examenCardio"}}
        <select name="_helpers_examenCardio" onchange="pasteHelperContent(this); this.form.examenCardio.onchange();" class="helper">
          <option value="">&mdash; Aide</option>
          {{html_options options=$consult_anesth->_aides.examenCardio.no_enum}}
        </select>
        <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('CConsultAnesth', this.form.examenCardio, '', '', '', '', {{$userSel->_id}})">{{tr}}New{{/tr}}</button>
        <br />
        {{mb_field object=$consult_anesth field="examenCardio" onchange="this.form.onsubmit()"}}

        <br />

        {{mb_label object=$consult_anesth field="examenPulmo"}}
        <select name="_helpers_examenPulmo" onchange="pasteHelperContent(this); this.form.examenPulmo.onchange();" class="helper">
          <option value="">&mdash; Aide</option>
          {{html_options options=$consult_anesth->_aides.examenPulmo.no_enum}}
        </select>
        <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('CConsultAnesth', this.form.examenPulmo, '', '', '', '', {{$userSel->_id}})">{{tr}}New{{/tr}}</button>
        <br />
        {{mb_field object=$consult_anesth field="examenPulmo" onchange="this.form.onsubmit()"}}
      </form>
    </td>
  </tr>
  <tr>
    <td>
      <form name="editFrmExams" action="?m={{$m}}" method="post" onsubmit="return onSubmitFormAjax(this);"">
        <input type="hidden" name="m" value="dPcabinet" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="dosql" value="do_consultation_aed" />
        {{mb_key object=$consult}}
				
        {{mb_label object=$consult field="examen"}}
        <select name="_helpers_examen" onchange="pasteHelperContent(this); this.form.examen.onchange();" class="helper">
          <option value="">&mdash; Aide</option>
          {{html_options options=$consult->_aides.examen.no_enum}}
        </select>
        <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('CConsultation', this.form.examen, '', '', '', '', {{$userSel->_id}})">{{tr}}New{{/tr}}</button>
        <br />
        {{mb_field object=$consult field="examen" onchange="this.form.onsubmit()"}}
      </form>
    </td>
  </tr>
</table>