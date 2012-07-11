{{assign var=patient value=$consult->_ref_patient}}
{{assign var=praticien value=$consult->_ref_chir}}

<table class="form">
  <tr>
    <td class="halfPane">
      <fieldset>
        <legend>{{tr}}CFile{{/tr}} - {{tr}}{{$consult->_class}}{{/tr}}</legend>            
        <div id="files-fdr">
          <script type="text/javascript">
            File.register('{{$consult->_id}}','{{$consult->_class}}', 'files-fdr');
          </script>
        </div>
      </fieldset>
  	</td>
  	<td class="halfPane">
  	  {{mb_ternary var=object test=$consult->_is_anesth value=$consult->_ref_consult_anesth other=$consult}}
      <fieldset>
        <legend>{{tr}}CCompteRendu{{/tr}} - {{tr}}{{$object->_class}}{{/tr}}</legend>            
        <div id="documents-fdr">
          {{mb_script module="dPcompteRendu" script="document"}}
          <script type="text/javascript">
            Document.register('{{$object->_id}}','{{$object->_class}}','{{$consult->_praticien_id}}','documents-fdr');
          </script>
        </div>
      </fieldset>
    </td>
  </tr>
  <tr>
    <td colspan="2">
      {{if "dPprescription"|module_active && $conf.dPcabinet.CPrescription.view_prescription}}
      <fieldset>
        <legend>{{tr}}CPrescription{{/tr}}</legend>
        <div id="prescription_register">
          <script type="text/javascript">
            PrescriptionEditor.register('{{$consult->_id}}','{{$consult->_class}}','fdr','{{$consult->_praticien_id}}');
          </script>
        </div>
      </fieldset>
      {{/if}}
    </td>  
  </tr>
  {{if $consult->sejour_id}} {{* Cas d'un RPU *}}
  {{assign var=sejour value=$consult->_ref_sejour}}
  <tr>
    <td class="halfPane">
      <fieldset>
        <legend>{{tr}}CFile{{/tr}} - {{tr}}CSejour{{/tr}}</legend>            
        <div id="files-CSejour">
          <script type="text/javascript">
            File.register('{{$sejour->_id}}','{{$sejour->_class}}', 'files-CSejour');
          </script>
        </div>
      </fieldset>
    </td>
    <td class="halfPane">
      <fieldset>
        <legend>{{tr}}CCompteRendu{{/tr}} - {{tr}}CSejour{{/tr}}</legend>            
        <div id="documents-CSejour">
          <script type="text/javascript">
            Document.register('{{$sejour->_id}}','{{$sejour->_class}}','{{$sejour->_praticien_id}}','documents-CSejour');
          </script>
        </div>
      </fieldset>
    </td>
  </tr>
  {{/if}}
</table>