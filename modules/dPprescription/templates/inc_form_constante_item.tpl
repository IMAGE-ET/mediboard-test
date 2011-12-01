{{if $element_prescription_id}}
  <form name="editConstanteItem" method="post" action="?" onsubmit="return onSubmitFormAjax(this)">
    <input type="hidden" name="m" value="dPprescription" />
    <input type="hidden" name="dosql" value="do_constante_item_aed" />
    <input type="hidden" name="callback" value="refreshListConstantesItems" />
    <input type="hidden" name="del" value="0" />
    {{mb_key object=$constante_item}}
    {{mb_field object=$constante_item field=element_prescription_id hidden=true}}
    
    <table class="form">
      <tr>
         {{if $constante_item->_id}}
           <th class="title text modify" colspan="2">
             {{tr}}CConstanteItem-title-modify{{/tr}} ({{$constante_item->_ref_element_prescription->_view}})
           </th>
         {{else}}
           <th class="title text" colspan="2">
             {{tr}}CConstanteItem-title-create{{/tr}} ({{$constante_item->_ref_element_prescription->_view}})
           </th>
         {{/if}}
        </tr>
        <tr>
          <th>{{mb_label object=$constante_item field="field_constante"}}</th>
          <td>
          	<select name="field_constante" class="{{$constante_item->_props.field_constante}}">
						  {{foreach from=$list_constantes key=_type item=_constantes}}
							  <optgroup label="{{tr}}CConstantesMedicales.type.{{$_type}}{{/tr}}">
							  	{{foreach from=$_constantes item=_const}}
									  <option value="{{$_const}}" {{if $_const == $constante_item->field_constante}} selected="selected" {{/if}}>
									  	{{tr}}CConstanteItem.field_constante.{{$_const}}{{/tr}}
										</option>
									{{/foreach}}
							  </optgroup>
							{{/foreach}}
						</select>
          </td>
        </tr>
        <tr>
         <th>{{mb_label object=$constante_item field="commentaire"}}</th>
         <td>{{mb_field object=$constante_item field="commentaire"}}</td>
       </tr>
       <tr>
        <td class="button" colspan="2">
          <button type="submit" class="submit">{{tr}}Save{{/tr}}</button>
          {{if $constante_item->_id}}
          <button class="trash" type="button" name="delete" onclick="confirmDeletion(this.form,{ ajax: true, typeName:'le champ de constante',objName:'{{$constante_item->_view|smarty:nodefaults|JSAttribute}}'})">
            {{tr}}Delete{{/tr}}
          </button>
          {{/if}}
        </td>
    </table>
  </form>
{{/if}}