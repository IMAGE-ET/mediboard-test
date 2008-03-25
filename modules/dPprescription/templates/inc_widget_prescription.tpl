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
	    <!-- Affichage du nombre de medicaments -->
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
	    
	    <!-- Affichage du nombre d'éléments -->
	    
	    <!-- Parcours des chapitres -->
	    {{foreach from=$prescription->_ref_lines_elements_comments key=name item=elementsChap}}
	    {{assign var=nb_lines_element value=0}}
	    {{assign var=nb_lines_comment value=0}}
	    
	    <!-- Parcours des categories -->
	    {{foreach from=$elementsChap item=elementsCat name="element"}} 
	      {{assign var=lines_element value=$elementsCat.element|@count}}
	      {{assign var=lines_comment value=$elementsCat.comment|@count}}
	      {{assign var=nb_lines_element value=$nb_lines_element+$lines_element}}  
	      {{assign var=nb_lines_comment value=$nb_lines_comment+$lines_comment}}
	    {{if $smarty.foreach.element.last}}
	    <li>
	      <strong>{{tr}}CCategoryPrescription.chapitre.{{$name}}{{/tr}}: </strong>
	        {{if $nb_lines_element}}
	          {{$nb_lines_element}} ligne(s) de prescription
	        {{/if}}
	        {{if $nb_lines_element && $nb_lines_comment}}
	         / 
	        {{/if}}
	        {{if $nb_lines_comment}}
	          {{$nb_lines_comment}} ligne(s) de commentaire
	        {{/if}}
	    </li>
	    {{/if}}
	    
	    {{/foreach}}
	    {{/foreach}}
	  </ul>
	  {{/if}}
	</div>	
{{else}}
  <div class="warning">
    Module Prescriptions non installé
  </div>
{{/if}}