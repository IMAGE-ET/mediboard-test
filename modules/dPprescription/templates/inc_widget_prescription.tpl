{{if !@$suffixe}}{{assign var=suffixe value="std"}}{{/if}}

<script type="text/javascript">

Prescription.suffixes.push("{{$suffixe}}");
Prescription.suffixes = Prescription.suffixes.uniq();



openPrescription = function(prescription_id){
  PrescriptionEditor.popup(prescription_id,'{{$object_id}}','{{$object_class}}','{{$type}}');
}

</script>


{{if is_array($prescriptions)}}
	  <!-- Pas de prescription -->
	  {{if !$prescriptions|@count}}
	  <form name="addPrescriptionSejour{{$suffixe}}" action="?">
	    <input type="hidden" name="m" value="dPprescription" />
	    <input type="hidden" name="dosql" value="do_prescription_aed" />
	    <input type="hidden" name="del" value="0" />
	    <input type="hidden" name="callback" value="openPrescription" />
	    <input type="hidden" name="object_id" value="{{$object_id}}" />
	    <input type="hidden" name="object_class" value="{{$object_class}}" />
	    <input type="hidden" name="type" value="{{$type}}" />
	    
	    <!-- Creation d'une prescription de type sejour (pre_admission/sejour/sortie) -->
	    {{if $object_class == "CSejour"}}
        <button type="button" class="new" onclick="submitFormAjax(this.form, 'systemMsg');">
	        Créer une prescription de séjour
	      </button>    
	    {{else}}
	    <!-- Creation d'une prescription d'externe -->
	    <button type="button" class="new" onclick="submitFormAjax(this.form, 'systemMsg');">
	      Créer une prescription de consultation
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
	      <tr>
				  <td>
				   {{if $_prescription->type != "traitement"}}
				   <a href="#{{$_prescription->_id}}" onclick="PrescriptionEditor.popup('{{$_prescription->_id}}');">
				     {{tr}}CPrescription.type.{{$_prescription->type}}{{/tr}}
				   </a>
				   {{else}}
				   {{tr}}CPrescription.type.{{$_prescription->type}}{{/tr}}
				   {{/if}}
				  </td>
				  <td>
				    {{$_prescription->_ref_praticien->_view}}
				  </td>
				  {{foreach from=$_prescription->_counts_by_chapitre key=chapitre item=count}}
				  {{if $totals_by_chapitre.$chapitre}}
				  <td style="text-align: center;">
				    {{if $count}}{{$count}}{{else}}-{{/if}}
				  </td>
				  {{/if}}
				  {{/foreach}}
				</tr>
	    {{/foreach}}
	    </table>
	    {{/if}}
{{else}}
  <div class="warning">
    Module Prescriptions non installé
  </div>
{{/if}}