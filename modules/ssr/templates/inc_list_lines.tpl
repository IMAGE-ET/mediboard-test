{{if array_key_exists($category_id, $lines) && $lines.$category_id|@count > 0}}
  {{foreach from=$lines.$category_id item=_line}}
	 {{if $_line->_id == $full_line_id}}
		<tr>
			<td />
			<td style="border: 1px solid #aaa;">
			  <form name="editLine-{{$_line->_id}}" action="?" method="post">
			  	<input type="hidden" name="m" value="dPprescription" />
					<input type="hidden" name="dosql" value="do_prescription_line_element_aed" />
					<input type="hidden" name="del" value="0" />
	        <input type="hidden" name="prescription_line_element_id" value="{{$_line->_id}}" />
					
					{{if $can_edit_prescription}}
          <button style="float: right" type="button" class="trash notext" 
                  onclick="$V(this.form.del, '1'); return onSubmitFormAjax(this.form, { 
                             onComplete: function(){ updateListLines('{{$category_id}}', '{{$_line->prescription_id}}', ''); }
                           } )"></button>
					{{/if}}
													 
					<button type="button" class="lock notext" onclick="updateListLines('{{$category_id}}', '{{$_line->prescription_id}}', '');">lock</button>
					
	        <span class="mediuser" style="border-left-color: #{{$_line->_ref_element_prescription->_color}};">
					  <strong onmouseover="ObjectTooltip.createEx(this, '{{$_line->_guid}}');"> {{$_line->_view}}</strong>
					</span>
					<br />
					{{mb_label object=$_line field="commentaire"}}
					{{if $can_edit_prescription}}
					  {{mb_field object=$_line field="commentaire" style="width: 20em" onchange="onSubmitFormAjax(this.form);"}}
          {{else}}
					  {{mb_value object=$_line field="commentaire"}}
          {{/if}}
					<br />
					{{if $can_edit_prescription}}
						Debut {{mb_field object=$_line field="debut" form=editLine-$full_line_id register=true onchange="onSubmitFormAjax(this.form);"}}
						Arret {{mb_field object=$_line field="date_arret" form=editLine-$full_line_id register=true onchange="onSubmitFormAjax(this.form);"}}
					{{else}}
						Debut {{mb_value object=$_line field="debut"}}
	          Arret {{mb_value object=$_line field="date_arret"}}
          {{/if}}
				</form>	
			 </td>
		</tr>
		{{else}}
		<tr>
			<td />
			<td>
      {{mb_include template=inc_vw_line}}
	   </td>
    </tr>
    {{/if}}
  {{/foreach}}
{{else}}
<tr>
	<td colspan="2">&nbsp;</td>
</tr>
{{/if}}