{{if !@$suffixe}}{{assign var=suffixe value="std"}}{{/if}}
  
<script type="text/javascript">

Prescription.suffixes.push("{{$suffixe}}");
Prescription.suffixes = Prescription.suffixes.uniq();

</script>

{{if $prescription}}
<div id="prescription-{{$suffixe}}">
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
  {{assign var=med_element value=$prescription->_ref_lines_med_comments.med|@count}}
  {{assign var=med_comment value=$prescription->_ref_lines_med_comments.comment|@count}}
    
    {{if $med_element || $med_comment}}
    <li>
      <strong>Médicaments: </strong>
      {{if $med_element}}
        {{$med_element}} ligne(s) de prescription
      {{/if}}
      {{if $med_element && $med_comment}}/{{/if}}
      {{if $med_comment}}
        {{$med_comment}} ligne(s) de commentaire
      {{/if}}
    </li>  
    {{/if}}
   
   {{foreach from=$prescription->_ref_lines_elements_comments key=name item=cat}}
    {{assign var=line_element value=$cat.element|@count}}
    {{assign var=line_comment value=$cat.comment|@count}}
    {{if $line_element || $line_comment}}
    <li>
      <strong>{{tr}}CCategoryPrescription.chapitre.{{$name}}{{/tr}}: </strong>
      {{if $line_element}}
        {{$line_element}} ligne(s) de prescription
      {{/if}}
      {{if $line_element && $line_comment}}/{{/if}}
      {{if $line_comment}}
        {{$line_comment}} ligne(s) de commentaire
      {{/if}}
    </li>  
    {{/if}}
  {{/foreach}}
  </ul>
  {{/if}}
</div>

{{else}}
<div class="warning">
  Module Prescriptions non installé
</div>
{{/if}}

