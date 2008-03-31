<tbody class="hoverable">
  <tr>
    <td  style="width: 25px">
      <button type="button" class="trash notext" onclick="Prescription.delLineElement('{{$_line_element->_id}}','{{$element}}')">
        {{tr}}Delete{{/tr}}
      </button>
    </td>
    <td colspan="2">
     {{$_line_element->_ref_element_prescription->_view}}
    </td>
    <td>
      <form name="addCommentElement-{{$_line_element->_id}}" method="post" action="" onsubmit="return onSubmitFormAjax(this);">
        <input type="hidden" name="m" value="dPprescription" />
        <input type="hidden" name="dosql" value="do_prescription_line_element_aed" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="prescription_line_element_id" value="{{$_line_element->_id}}" />
        <input type="text" name="commentaire" value="{{$_line_element->commentaire}}" onchange="this.form.onsubmit();" />
      </form>
    </td>
    <td>
      {{assign var=category value=$_line_element->_ref_element_prescription->_ref_category_prescription}}
      {{$_line_element->_ref_praticien->_view}}
    </td>
    <td>
      {{assign var=category_id value=$category->_id}}
      {{if @$executants.$category_id}}
      <form name="addExecutant-{{$_line_element->_id}}" method="post" action="">
        <input type="hidden" name="m" value="dPprescription" />
        <input type="hidden" name="dosql" value="do_prescription_line_element_aed" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="prescription_line_element_id" value="{{$_line_element->_id}}" />
        <!-- Selection d'un executant -->
        <select class="executant-{{$category_id}}" name="executant_prescription_line_id" onchange="submitFormAjax(this.form, 'systemMsg');">
          <option value="">&mdash; Sélection d'un exécutant</option>
          {{foreach from=$executants.$category_id item=executant}}
          <option value="{{$executant->_id}}" {{if $executant->_id == $_line_element->executant_prescription_line_id}}selected="selected"{{/if}}>{{$executant->_view}}</option>
          {{/foreach}}
        </select>
      </form>
      
      <a href="#" style="display:inline" 
         onclick="preselectExecutant(document.forms['addExecutant-'+{{$_line_element->_id}}].executant_prescription_line_id.value,'{{$category_id}}');">
		    <img src="images/icons/updown.gif" alt="Préselectionner" border="0" />
			</a>
			
      {{else}}
        Aucun exécutant disponible
      {{/if}}
    </td>
    <td>
      <form name="addALD-{{$_line_element->_id}}" method="post" action="" onsubmit="return onSubmitFormAjax(this);">
        <input type="hidden" name="m" value="dPprescription" />
        <input type="hidden" name="dosql" value="do_prescription_line_element_aed" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="prescription_line_element_id" value="{{$_line_element->_id}}" />
        {{mb_field object=$_line_element field="ald" typeEnum="checkbox" onchange="submitFormAjax(this.form, 'systemMsg')"}}
        {{mb_label object=$_line_element field="ald" typeEnum="checkbox"}}
      </form>
    </td>
  </tr>
  </tbody>