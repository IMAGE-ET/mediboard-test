<table class="main form">
  <tr>
    <td>
      <table class="main layout">
        <tr>
          <td style="width: 50%;">
            <!-- Fiches d'examens -->
            {{mb_script module="dPcabinet" script="exam_dialog"}}
            <script type="text/javascript">
              ExamDialog.register('{{$consult->_id}}','{{$consult_anesth->_id}}');
            </script>
          </td>
          
          {{if "forms"|module_active}}
            <td>
              {{unique_id var=unique_id_exam_forms}}
              
              <script type="text/javascript">
                Main.add(function(){
                  ExObject.loadExObjects("{{$consult_anesth->_class}}", "{{$consult_anesth->_id}}", "{{$unique_id_exam_forms}}", 0.5);
                });
              </script>
              
              <fieldset id="list-ex_objects">
                <legend>Formulaires</legend>
                <div id="{{$unique_id_exam_forms}}"></div>
              </fieldset>
            </td>
          {{/if}}
        </tr>
      </table>
      
      
      
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
                {{mb_field object=$consult_anesth field="examenCardio" rows="4" onchange="this.form.onsubmit()" form="editAnesthExamenClinique"
                  aidesaisie="validateOnBlur: 0"}}
              </fieldset>
            </td>
            <td class="halfPane">
              <fieldset>
                <legend>{{mb_label object=$consult_anesth field="examenPulmo"}}</legend>
                {{mb_field object=$consult_anesth field="examenPulmo" rows="4" onchange="this.form.onsubmit()" form="editAnesthExamenClinique"
                  aidesaisie="validateOnBlur: 0"}}
              </fieldset>
            </td>
          </tr>
          <tr>
            <td class="halfPane">
              <fieldset>
                <legend>{{mb_label object=$consult_anesth field="examenDigest"}}</legend>
                {{mb_field object=$consult_anesth field="examenDigest" rows="4" onchange="this.form.onsubmit()" form="editAnesthExamenClinique"
                  aidesaisie="validateOnBlur: 0"}}
              </fieldset>
            </td>
            <td class="halfPane">
              <fieldset>
                <legend>{{mb_label object=$consult_anesth field="examenAutre"}}</legend>
                {{mb_field object=$consult_anesth field="examenAutre" rows="4" onchange="this.form.onsubmit()" form="editAnesthExamenClinique"
                  aidesaisie="validateOnBlur: 0"}}
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
          {{mb_field object=$consult field="examen" rows="4" onchange="this.form.onsubmit()" form="editFrmExamenConsult"
                  aidesaisie="validateOnBlur: 0"}}
        </fieldset>
      </form>
    </td>
  </tr>
</table>