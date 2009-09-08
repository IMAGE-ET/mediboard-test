<script type="text/javascript">
  function refreshConstantesMedicales () {
    var url = new Url("dPpatients", "httpreq_vw_constantes_medicales");
    url.addParam("patient_id", {{$consult->_ref_patient->_id}});
    url.addParam("context_class", "{{$consult->_class_name}}");
    url.addParam("context_id", {{$consult->consultation_id}});
    url.requestUpdate("constantes-medicales", { waitingText: null } );
  };
  Main.add(refreshConstantesMedicales);
</script>
<table class="form">
  <tr>
    <td rowspan="2" id="constantes-medicales"></td>
    <td>
      <!-- Fiches d'examens -->
      {{mb_include_script module="dPcabinet" script="exam_dialog"}}
      <script type="text/javascript">
        ExamDialog.register('{{$consult->_id}}','{{$consult->_class_name}}');
      </script>
    </td>
  </tr>
  <tr>
    <td class="greedyPane">
      <table class="form">
        <tr>
          <td>
            <form name="editAnesthExamenCardio" action="?m={{$m}}" method="post" onsubmit="return checkForm(this);">
              {{mb_label object=$consult_anesth field="examenCardio"}}
              <input type="hidden" name="m" value="dPcabinet" />
              <input type="hidden" name="del" value="0" />
              <input type="hidden" name="dosql" value="do_consult_anesth_aed" />
              {{mb_field object=$consult_anesth field="consultation_anesth_id" hidden=1 prop=""}}
              <select name="_helpers_examenCardio" onchange="pasteHelperContent(this); this.form.examenCardio.onchange();">
                <option value="">&mdash; Choisir une aide</option>
                {{html_options options=$consult_anesth->_aides.examenCardio.no_enum}}
              </select>
              <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('CConsultAnesth', this.form.examenCardio)">{{tr}}New{{/tr}}</button>
              <br />
              {{mb_field object=$consult_anesth field="examenCardio" onchange="submitFormAjax(this.form, 'systemMsg')"}}
            </form>
          </td>
        </tr>
      </table>

      <table class="form">
        <tr>
          <td>
            <form name="editAnesthExamenPulmo" action="?m={{$m}}" method="post" onsubmit="return checkForm(this);">
              {{mb_label object=$consult_anesth field="examenPulmo"}}
              <input type="hidden" name="m" value="dPcabinet" />
              <input type="hidden" name="del" value="0" />
              <input type="hidden" name="dosql" value="do_consult_anesth_aed" />
              {{mb_field object=$consult_anesth field="consultation_anesth_id" hidden=1 prop=""}}
              <select name="_helpers_examenPulmo" onchange="pasteHelperContent(this); this.form.examenPulmo.onchange();">
                <option value="">&mdash; Choisir une aide</option>
                {{html_options options=$consult_anesth->_aides.examenPulmo.no_enum}}
              </select>
              <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('CConsultAnesth', this.form.examenPulmo)">{{tr}}New{{/tr}}</button>
              <br />
              {{mb_field object=$consult_anesth field="examenPulmo" onchange="submitFormAjax(this.form, 'systemMsg')"}}
            </form>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td colspan="2">
      <table class="form">
        <tr>
          <td >
            <form name="editFrmExams" action="?m={{$m}}" method="post" onsubmit="return checkForm(this);">
            <input type="hidden" name="m" value="dPcabinet" />
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="dosql" value="do_consultation_aed" />
            {{mb_field object=$consult field="consultation_id" hidden=1 prop=""}}
            {{mb_label object=$consult field="examen"}}
            <select name="_helpers_examen" onchange="pasteHelperContent(this); this.form.examen.onchange();">
              <option value="">&mdash; Choisir une aide</option>
              {{html_options options=$consult->_aides.examen.no_enum}}
            </select>
            <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('CConsultation', this.form.examen)">{{tr}}New{{/tr}}</button>
            <br />
            {{mb_field object=$consult field="examen" onchange="submitFormAjax(this.form, 'systemMsg')"}}
            </form>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>