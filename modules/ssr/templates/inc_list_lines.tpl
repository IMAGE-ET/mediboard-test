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
					
					<button style="float: right" type="button" class="trash notext" 
                  onclick="$V(this.form.del, '1'); return onSubmitFormAjax(this.form, { 
                             onComplete: function(){ updateListLines('{{$category_id}}', '{{$_line->prescription_id}}', ''); }
                           } )"></button>
					<button type="button" class="lock notext" onclick="updateListLines('{{$category_id}}', '{{$_line->prescription_id}}', '');">lock</button>
	        
					<strong onmouseover="ObjectTooltip.createEx(this, '{{$_line->_guid}}');"> {{$_line->_view}}</strong>
					<br />
					{{mb_label object=$_line field="commentaire"}}
					{{mb_field object=$_line field="commentaire" style="width: 20em" onchange="onSubmitFormAjax(this.form);"}}
					<br />
					Debut
					{{mb_field object=$_line field="debut" form=editLine-$full_line_id register=true onchange="onSubmitFormAjax(this.form);"}}
					Arret
					{{mb_field object=$_line field="date_arret" form=editLine-$full_line_id register=true onchange="onSubmitFormAjax(this.form);}}
				</form>	
		  </td>
		</tr>
		{{else}}
		<tr>
			<td />
			<td>
			<button class="edit notext" type="button" onclick="updateListLines('{{$category_id}}', '{{$_line->prescription_id}}', '{{$_line->_id}}');">Edit</button>
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