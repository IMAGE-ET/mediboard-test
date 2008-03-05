<div id="prescription">
  <!-- Pas de prescription -->
  {{if !$prescription->_id}}
  <button type="button" class="new" onclick="PrescriptionEditor.popup('{{$prescription->_id}}','{{$object_id}}','{{$object_class}}','{{$praticien_id}}')">
    Créer une prescription
  </button>
  {{else}}
  <!-- Une prescription -->
  <a href="#{{$prescription->_id}}" onclick="PrescriptionEditor.popup('{{$prescription->_id}}','{{$object_id}}','{{$object_class}}','{{$praticien_id}}')">
    {{$prescription->_view}}
  </a>
  <button type="button" class="print notext" onclick="Prescription.print('{{$prescription->_id}}')">
    Imprimer
  </button>
  <br />
  <ul>
  {{if $prescription->_ref_prescription_lines|@count}}
    <li>{{$prescription->_ref_prescription_lines|@count}} médicaments</li>
  {{/if}}
  {{foreach from=$prescription->_ref_prescription_lines_element_by_cat key=name item=cat}}
    {{if $cat|@count}}
      <li>{{$cat|@count}} {{$name}}</li>
    {{/if}}
  {{/foreach}}
  </ul>
  {{/if}}
</div>