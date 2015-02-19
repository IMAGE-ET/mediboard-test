{{assign var=patient value=$consult->_ref_patient}}
{{assign var=praticien value=$consult->_ref_chir}}

<table class="form">
  <tr>
    {{mb_ternary var=object test=$consult->_refs_dossiers_anesth|@count value=$consult->_ref_consult_anesth other=$consult}}
    <td class="halfPane">
      <fieldset>
        <legend>{{tr}}CFile{{/tr}} - {{tr}}{{$object->_class}}{{/tr}}</legend>
        <div id="files-fdr">
          <script>
            File.use_mozaic = 1;
            File.register('{{$object->_id}}','{{$object->_class}}', 'files-fdr');
          </script>
        </div>
      </fieldset>
  	</td>
  	<td class="halfPane">
      <fieldset>
        <legend>{{tr}}CCompteRendu{{/tr}} - {{tr}}{{$object->_class}}{{/tr}}</legend>            
        <div id="documents-fdr">
          {{mb_script module="dPcompteRendu" script="document"}}
          <script>
            Document.register('{{$object->_id}}','{{$object->_class}}','{{$consult->_praticien_id}}','documents-fdr');
          </script>
        </div>
      </fieldset>
    </td>
  </tr>
  {{if $consult->sejour_id}} {{* Cas d'un RPU *}}
    {{assign var=sejour value=$consult->_ref_sejour}}
    <tr>
      <td class="halfPane">
        <fieldset>
          <legend>{{tr}}CFile{{/tr}} - {{tr}}CSejour{{/tr}}</legend>
          <div id="files-CSejour">
            <script>
              File.register('{{$sejour->_id}}','{{$sejour->_class}}', 'files-CSejour');
            </script>
          </div>
        </fieldset>
      </td>
      <td class="halfPane">
        <fieldset>
          <legend>{{tr}}CCompteRendu{{/tr}} - {{tr}}CSejour{{/tr}}</legend>
          <div id="documents-CSejour">
            <script>
              Document.register('{{$sejour->_id}}','{{$sejour->_class}}','{{$sejour->_praticien_id}}','documents-CSejour');
            </script>
          </div>
        </fieldset>
      </td>
    </tr>
  {{/if}}
  <tr>
    <td class="halfPane">
      {{if "dPprescription"|module_active && "dPcabinet CPrescription view_prescription_externe"|conf:"CGroups-$g"}}
      <fieldset>
        <legend>{{tr}}CPrescription{{/tr}}</legend>
        <div id="prescription_register">
          <script>
            PrescriptionEditor.register('{{$consult->_id}}','{{$consult->_class}}','fdr','{{$consult->_praticien_id}}');
          </script>
        </div>
      </fieldset>
      {{/if}}
    </td>
    <td class="halfPane">
      <fieldset>
        <legend>{{tr}}CDevisCodage{{/tr}}</legend>
        {{mb_script module=ccam script=DevisCodage ajax=1}}
        <script>
          Main.add(function() {
            DevisCodage.list('{{$consult->_class}}', '{{$consult->_id}}');
          });
        </script>
        <div id="view-devis"></div>
      </fieldset>
    </td>
  </tr>
</table>