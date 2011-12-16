<script type="text/javascript">
Main.add(function () {
  var oExamCliniqueForm = getForm("editAnesthExamenClinique");
  var options = {
    objectClass: "CConsultAnesth",
    timestamp: "{{$conf.dPcompteRendu.CCompteRendu.timestamp}}",
    validateOnBlur: false
  };
  
  new AideSaisie.AutoComplete(oExamCliniqueForm.examenCardio, options);
  new AideSaisie.AutoComplete(oExamCliniqueForm.examenPulmo,  options);
  new AideSaisie.AutoComplete(oExamCliniqueForm.examenDigest, options);
  new AideSaisie.AutoComplete(oExamCliniqueForm.examenAutre,  options);
          
  var oExamForm = getForm("editFrmExamenConsult");
  new AideSaisie.AutoComplete(oExamForm.examen, {
    objectClass: "CConsultation",
    timestamp: "{{$conf.dPcompteRendu.CCompteRendu.timestamp}}",
    validateOnBlur: false
  });
  
});
</script>

<table class="main form">
  <tr>
    <td>
      <!-- Fiches d'examens -->
      {{mb_script module="dPcabinet" script="exam_dialog"}}
      <script type="text/javascript">
        ExamDialog.register('{{$consult->_id}}','{{$consult->_class}}');
      </script>
      <form name="editAnesthExamenClinique" action="?" method="post" onsubmit="return onSubmitFormAjax(this);">
        <input type="hidden" name="m" value="dPcabinet" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="dosql" value="do_consult_anesth_aed" />
        {{mb_key object=$consult_anesth}}
        <table class="layout main">
          <tr>
            <td class="halfPane">
              <fieldset>
                <legend>{{mb_label object=$consult_anesth field="examenCardio"}}</legend>
                {{mb_field object=$consult_anesth field="examenCardio" rows="4" onchange="this.form.onsubmit()"}}
              </fieldset>
            </td>
            <td class="halfPane">
              <fieldset>
                <legend>{{mb_label object=$consult_anesth field="examenPulmo"}}</legend>
                {{mb_field object=$consult_anesth field="examenPulmo" rows="4" onchange="this.form.onsubmit()"}}
              </fieldset>
            </td>
          </tr>
          <tr>
            <td class="halfPane">
              <fieldset>
                <legend>{{mb_label object=$consult_anesth field="examenDigest"}}</legend>
                {{mb_field object=$consult_anesth field="examenDigest" rows="4" onchange="this.form.onsubmit()"}}
              </fieldset>
            </td>
            <td class="halfPane">
              <fieldset>
                <legend>{{mb_label object=$consult_anesth field="examenAutre"}}</legend>
                {{mb_field object=$consult_anesth field="examenAutre" rows="4" onchange="this.form.onsubmit()"}}
              </fieldset>
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