<script type="text/javascript">

{{if $type == "Med"}}
{{if !$line->_traitement && $line->_ref_prescription->object_id}}
{{if !$line->signee && !$line->fin}}
	var oForm = document.forms["editDates-Med-{{$line->_id}}"];
	Form.Element.setValue(oForm.debut,'{{$line->debut}}');
	{{if $line->debut}}
	  var oDiv = $('editDates-Med-'+{{$line->_id}}+'_debut_da');
	  dDate = Date.fromDATE(oForm.debut.value);
	  oDiv.innerHTML = dDate.toLocaleDate();
	{{/if}}
	Form.Element.setValue(oForm.duree,'{{$line->duree}}');
  Form.Element.setValue(oForm.unite_duree,'{{$line->unite_duree}}');
{{/if}}
{{/if}}


// On grise le formulaire de signature de la ligne si aucune prise n'est créée
var oButton = $('signature_{{$line->_id}}'); 
if(oButton){
{{if $line->_count_prises_line}}
  oButton.disabled = false;
  oButton.setOpacity(1.0);
{{else}}
  oButton.disabled = true;
  oButton.setOpacity(0.3);
{{/if}}
}
{{/if}}

</script>

{{assign var=line_id value=$line->_id}}

{{foreach from=$line->_ref_prises item=prise}}
  {{assign var=prise_id value=$prise->_id}}
  
  <form name="addPrise-{{$prise->_id}}" action="?" method="post" style="display: block;">
	  <input type="hidden" name="dosql" value="do_prise_posologie_aed" />
	  <input type="hidden" name="del" value="0" />
	  <input type="hidden" name="m" value="dPprescription" />
	  <input type="hidden" name="prise_posologie_id" value="{{$prise->_id}}" />
	  <input type="hidden" name="object_id" value="{{$line_id}}" />
	  <input type="hidden" name="object_class" value="{{$line->_class_name}}" />
						  
	  <!-- Formulaire de selection de la quantite -->
	  {{mb_field object=$prise field=quantite size="3" increment=1 min=1 form=addPrise-$prise_id onchange="testPharma($line_id); submitFormAjax(this.form, 'systemMsg');"}}	  
	  {{if $line->_class_name == "CPrescriptionLineMedicament"}}
	  {{$line->_unite_prise}}(s)
	  {{/if}}
	  <!-- Cas d'un moment unitaire_id -->
	  {{if $prise->moment_unitaire_id}}
	  <!-- Selection du moment -->
	  <select name="moment_unitaire_id" style="width: 150px" onchange="testPharma({{$line_id}}); submitFormAjax(this.form, 'systemMsg');">      
	    <option value="">&mdash; Sélection du moment</option>
	    {{foreach from=$moments key=type_moment item=_moments}}
	     <optgroup label="{{$type_moment}}">
	     {{foreach from=$_moments item=moment}}
	     <option value="{{$moment->_id}}" {{if $prise->moment_unitaire_id == $moment->_id}}selected="selected"{{/if}}>{{$moment->_view}}</option>
	     {{/foreach}}
	     </optgroup>
	    {{/foreach}}
	  </select>
	  {{/if}}
	  
	  <!-- Cas des fois par -->
	  {{if $prise->nb_fois && $prise->unite_fois}}
      {{mb_field object=$prise field=nb_fois size=3 increment=1 min=1 form=addPrise-$prise_id onchange="testPharma($line_id); submitFormAjax(this.form, 'systemMsg')"}} fois par 
			{{mb_field object=$prise field=unite_fois onchange="testPharma(); submitFormAjax(this.form, 'systemMsg')"}}
		{{/if}}
  
    <!-- Cas des tous les -->
    {{if $prise->nb_tous_les && $prise->unite_tous_les}}
      tous les
			{{mb_field object=$prise field=nb_tous_les size=3 increment=1 min=1 form=addPrise-$prise_id onchange="testPharma($line_id); submitFormAjax(this.form, 'systemMsg')"}}				   
			{{mb_field object=$prise field=unite_tous_les onchange="testPharma($line_id); submitFormAjax(this.form, 'systemMsg')"}}
		  (J+{{mb_field object=$prise field=decalage_prise size=1 increment=1 min=0 form=addPrise-$prise_id onchange="testPharma($line_id); submitFormAjax(this.form, 'systemMsg')"}})
		{{/if}}
		  
    <button type="button" class="cancel notext" onclick="this.form.del.value = 1; testPharma({{$line_id}}); submitPrise(this.form,'{{$type}}'); ">Supprimer</button> 
  </form>
{{/foreach}}

<script type="text/javascript">
{{foreach from=$line->_ref_prises item=prise}}
  prepareForm(document.forms['addPrise-{{$prise->_id}}']);
{{/foreach}}
</script>

