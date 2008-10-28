{{if $line->_can_modify_poso}}
	<form action="?m=dPprescription" method="post" name="editLine-{{$line->_id}}" onsubmit="return checkForm(this);">
	  <input type="hidden" name="m" value="dPprescription" />
	  <input type="hidden" name="dosql" value="do_prescription_line_medicament_aed" />
	  <input type="hidden" name="prescription_line_medicament_id" value="{{$line->_id}}"/>
	  <input type="hidden" name="del" value="0" />
	  <input type="hidden" name="_code_cip" value="{{$line->_ref_produit->code_cip}}" />
	  <input type="hidden" name="_delete_prises" value="0" />
	  {{assign var=posologies value=$line->_ref_produit->_ref_posologies}}
	  <select name="no_poso" {{if $dPconfig.dPprescription.CPrisePosologie.select_poso_bcb}}onchange="testPharma({{$line->_id}}); submitPoso(this.form, '{{$line->_id}}');"{{/if}} style="width: 230px;">
	    <option value="">&mdash; Posologies automatiques</option>
	    {{foreach from=$line->_ref_produit->_ref_posologies item=curr_poso}}
	    <option value="{{$curr_poso->code_posologie}}"
	      {{if $curr_poso->code_posologie == $line->no_poso}}selected="selected"{{/if}}>
	      {{$curr_poso->_view}}
	    </option>
	    {{/foreach}}
	  </select>  
	</form>
	<br />
{{else}}
  {{if $line->no_poso}}
    {{$line->_ref_posologie->_view}}
  {{else}}
    Aucune posologie sélectionnée
  {{/if}}
{{/if}}
  