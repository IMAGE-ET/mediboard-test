{{if !@$suffixe}}{{assign var=suffixe value="std"}}{{/if}}
  
<script type="text/javascript">

Prescription.suffixes.push("{{$suffixe}}");
Prescription.suffixes = Prescription.suffixes.uniq();

</script>

{{if $prescription}}
	<div id="prescription-{{$object_class}}-{{$suffixe}}" class="text">
	  <!-- Pas de prescription -->
	  {{if !$prescription->_id}}
	  <form name="addPrescriptionSejour{{$suffixe}}" action="?">
	    {{if $object_class == "CSejour"}}
	      {{mb_label object=$prescription field="type"}}
	      {{mb_field object=$prescription field="_type_sejour"}}
	      <button type="button" class="new" onclick="PrescriptionEditor.popup('{{$prescription->_id}}','{{$object_id}}','{{$object_class}}','{{$praticien_id}}', this.form._type_sejour.value)">
	        Créer une prescription
	      </button>
	    {{else}}
	    <button type="button" class="new" onclick="PrescriptionEditor.popup('{{$prescription->_id}}','{{$object_id}}','{{$object_class}}','{{$praticien_id}}')">
	      Créer une prescription
	    </button>
	    {{/if}}
	  </form>
	  {{else}}
	  
	  <table class="tbl">
      <tr>
        <th>Type</th>
        <th>Praticien</th>
        <th>Médicament</th>
        <th>DMI</th>
        <th>Anapath</th>
        <th>Biologie</th>
        <th>Imagerie</th>
        <th>Consult</th>
        <th>Kiné</th>
        <th>Soin</th>
      </tr>
  
	    <!-- Affichage de la prescription de consultation -->
	    {{if $prescription->object_class == "CConsultation"}}
	     {{include file="../../dPprescription/templates/inc_widget_vw_prescription.tpl"}}
	     {{if array_key_exists('traitement', $prescriptions)}}
	       {{include file="../../dPprescription/templates/inc_widget_vw_prescription.tpl" 
	                 prescription = $prescriptions.traitement}}
	     {{/if}}
	    {{else}}
	      <!-- Affichage des prescriptions de sejour -->
	      {{foreach from=$prescriptions item=catPrescription}}
	        {{foreach from=$catPrescription item=_prescription}}
	          {{include file="../../dPprescription/templates/inc_widget_vw_prescription.tpl" prescription=$_prescription}}
	        {{/foreach}}
	      {{/foreach}}
	    {{/if}}
	    </table>
	    {{/if}}
	</div>
{{else}}
  <div class="warning">
    Module Prescriptions non installé
  </div>
{{/if}}