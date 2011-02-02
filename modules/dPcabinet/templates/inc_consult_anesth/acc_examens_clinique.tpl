<script type="text/javascript">
Main.add(function () {
  
  var oExamCliniqueForm = getForm("editAnesthExamenClinique");
  new AideSaisie.AutoComplete(oExamCliniqueForm.examenCardio, {
            objectClass: "CConsultAnesth",
            timestamp: "{{$conf.dPcompteRendu.CCompteRendu.timestamp}}",
            validateOnBlur:0
          });

  new AideSaisie.AutoComplete(oExamCliniqueForm.examenPulmo, {
            objectClass: "CConsultAnesth",
            timestamp: "{{$conf.dPcompteRendu.CCompteRendu.timestamp}}",
            validateOnBlur:0
          });
          
  var oExamForm = getForm("editFrmExamenConsult");
  new AideSaisie.AutoComplete(oExamForm.examen, {
            objectClass: "CConsultation",
            timestamp: "{{$conf.dPcompteRendu.CCompteRendu.timestamp}}",
            validateOnBlur:0
          });
  
});
</script>

<table class="main form">
  <tr>
    <td>
      <!-- Fiches d'examens -->
      {{mb_include_script module="dPcabinet" script="exam_dialog"}}
      <script type="text/javascript">
        ExamDialog.register('{{$consult->_id}}','{{$consult->_class_name}}');
      </script>
      <form name="editAnesthExamenClinique" action="?" method="post" onsubmit="return onSubmitFormAjax(this);">
        <input type="hidden" name="m" value="dPcabinet" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="dosql" value="do_consult_anesth_aed" />
        {{mb_key object=$consult_anesth}}
        <table class="layout" style="width: 100%">
          <tr>
            <td class="halfPane">
              <fieldset>
                <legend>{{mb_label object=$consult_anesth field="examenCardio"}}</legend>
                {{mb_field object=$consult_anesth field="examenCardio" rows="4" onchange="this.form.onsubmit()"}}
              </fieldset>
              </form>
            </td>
            <td class="halfPane">
              <fieldset>
                <legend>{{mb_label object=$consult_anesth field="examenPulmo"}}</legend>
                {{mb_field object=$consult_anesth field="examenPulmo" rows="4" onchange="this.form.onsubmit()"}}
              </fieldset>
              </form>
            </td>
          </tr>
        </table>
      </form>
      
      <form name="editFrmExamenConsult" action="?" method="post" onsubmit="return onSubmitFormAjax(this);">
        <input type="hidden" name="m" value="dPcabinet" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="dosql" value="do_consultation_aed" />
        {{mb_key object=$consult}}
        <fieldset>
          <legend>{{mb_label object=$consult field="examen"}}</legend>
          {{mb_field object=$consult field="examen" rows="4" onchange="this.form.onsubmit()"}}
        </fieldset>
      </form>
    </td>
  </tr>
</table>