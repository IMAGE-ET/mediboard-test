<script type="text/javascript">

var oForm = document.forms["editDates-{{$curr_line->_id}}"];

Form.Element.setValue(oForm.debut,'{{$curr_line->debut}}');
{{if $curr_line->debut}}
var oDiv = $('editDates-'+{{$curr_line->_id}}+'_debut_da');
dDate = Date.fromDATE(oForm.debut.value);
oDiv.innerHTML = dDate.toLocaleDate();
{{/if}}
Form.Element.setValue(oForm.duree,'{{$curr_line->duree}}');

Form.Element.setValue(oForm.unite_duree,'{{$curr_line->unite_duree}}');

</script>

{{foreach from=$curr_line->_ref_prises item=prise}}
<form name="addPrise-{{$prise->_id}}" action="?" method="post" style="display: block;">
  <input type="hidden" name="dosql" value="do_prise_posologie_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="prise_posologie_id" value="{{$prise->_id}}" />
  <input type="hidden" name="prescription_line_id" value="{{$curr_line->_id}}" />
  <!-- Formulaire de selection de la quantite -->
  <button type="button" class="remove notext" onclick="this.form.quantite.value--;this.form.quantite.onchange();">Moins</button>
  {{mb_field object=$prise field=quantite size="3" onchange="submitFormAjax(this.form, 'systemMsg');"}}
  <button type="button" class="add notext" onclick="this.form.quantite.value++;this.form.quantite.onchange();">Plus</button>
  {{$curr_line->_unite_prise}}(s)
  
  {{if $prise->moment_unitaire_id}}
  <!-- Selection du moment -->
  <select name="moment_unitaire_id" style="width: 150px" onchange="submitFormAjax(this.form, 'systemMsg');">      
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
  
  {{if $prise->nb_fois}}
  {{mb_field object=$prise field=nb_fois onchange="submitFormAjax(this.form, 'systemMsg');"}} fois
  {{/if}}
  {{if $prise->unite_fois && !$prise->nb_tous_les}}
  par {{mb_field object=$prise field=unite_fois onchange="submitFormAjax(this.form, 'systemMsg');"}}
  {{/if}}
  
  
  {{if $prise->nb_tous_les && $prise->unite_tous_les}}
  tous les 
  {{mb_field object=$prise field=nb_tous_les onchange="submitFormAjax(this.form, 'systemMsg');"}}
  {{mb_field object=$prise field=unite_tous_les onchange="submitFormAjax(this.form, 'systemMsg');"}}
  {{/if}}
  
  
  <button type="button" class="submit notext" onclick="submitFormAjax(this.form, 'systemMsg');">Enregistrer</button>
  <button type="button" class="cancel notext" onclick="this.form.del.value = 1; submitPrise(this.form); ">Supprimer</button>

</form>
{{/foreach}}