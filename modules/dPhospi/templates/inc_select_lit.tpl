{{**
  * Selecteur de lit sous la forme {{mbfield}}
  * @param $listServices array|CService
  * @param $field string Field name
  * @param $ajaxSubmit bool
  * @param $selected_id ref|CLit
  *}}
  
<select name="box_id" {{if $ajaxSubmit}}onchange="this.form.onsubmit();"{{/if}}>
  <option value="">&mdash; Choisir un box</option>
 {{foreach from=$listServicesUrgence item=_service}}
 <optgroup label="{{$_service->_view}}">
   {{foreach from=$_service->_ref_chambres item=_chambre}}
    {{foreach from=$_chambre->_ref_lits item=_lit}}
    <option value="{{$_lit->_id}}" {{if $selected_id == $_lit->_id}}selected="selected"{{/if}}>
    	{{$_lit->_view}}
    </option>
    {{foreachelse}}
    <option value="">Aucun lit disponible</option>
    {{/foreach}}
   {{foreachelse}}
   <option value="">Aucune chambre disponible</option>
   {{/foreach}}
 </optgroup>
 {{foreachelse}}
 <option value="">Aucun service disponible</option>
 {{/foreach}}
</select>
