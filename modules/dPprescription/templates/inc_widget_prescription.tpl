{{if !@$suffixe}}{{assign var=suffixe value="std"}}{{/if}}
  
<script type="text/javascript">

Prescription.suffixes.push("{{$suffixe}}");
Prescription.suffixes = Prescription.suffixes.uniq();

</script>


{{if is_array($prescriptions)}}
	<div id="prescription-{{$object_class}}-{{$suffixe}}" class="text">
	  <!-- Pas de prescription -->
	  {{if !$prescriptions|@count}}
	  <form name="addPrescriptionSejour{{$suffixe}}" action="?">
	    {{if $object_class == "CSejour"}}
	      Type
	      
	      <select name="_type_sejour">
	        <option value="pre_admission">Pré-admission</option>
	        <option value="sejour">Séjour</option>
	        <option value="sortie">Sortie</option>
	      </select>
	      
	      <button type="button" class="new" onclick="PrescriptionEditor.popup('','{{$object_id}}','{{$object_class}}','{{$praticien_id}}', this.form._type_sejour.value)">
	        Créer une prescription
	      </button>
	    {{else}}
	    <button type="button" class="new" onclick="PrescriptionEditor.popup('','{{$object_id}}','{{$object_class}}','{{$praticien_id}}')">
	      Créer une prescription
	    </button>
	    {{/if}}
	  </form>
	  {{else}}
	  
	  <table class="tbl">
      <tr>
        <th>Type</th>
        <th>Praticien</th>
        
        {{foreach from=$totals_by_chapitre item=total key=chapitre}}
        {{if $total}}
        <th>
					{{tr}}CCategoryPrescription.chapitre.{{$chapitre}}{{/tr}}				
				</th>
				{{/if}}
				{{/foreach}}
        
      </tr>
  
	    {{foreach from=$prescriptions item=_prescription}}
	      {{include file="../../dPprescription/templates/inc_widget_vw_prescription.tpl" prescription=$_prescription}}
	    {{/foreach}}
	 
	    </table>
	    {{/if}}
	</div>
{{else}}
  <div class="warning">
    Module Prescriptions non installé
  </div>
{{/if}}