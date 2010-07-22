{{* $id: $ *}}


<table class="tbl">
  <tr>
  	<th class="title" colspan="100">
  		{{tr}}{{$object_class}}{{/tr}}
		</th>
	</tr>

  <tr>
    <th>{{tr}}Object{{/tr}}</th>
    {{foreach from=$object_tags.$object_class key=_tag item=_editable}}
    <th>{{$_tag}}</th>
    {{/foreach}}
  </tr>

{{foreach from=$objects key=object_id item=_object}}

  {{assign var=ids value=$object_ids.$object_id}}
	
  <tr>
    <td>
    	<span onmouseover="ObjectTooltip.createEx(this, '{{$_object->_guid}}')">
	    	{{$_object}}
			</span>
		</td>
		{{assign var=id value=$ids.eCap}}
    {{foreach from=$ids key=_tag item=_id}}
      <td style="text-align: center;">
      {{if $object_tags.$object_class.$_tag}} 
	      <form name="EditId-{{$_object->_guid}}-{{$_tag|replace:' ':'_'}}" action="?" method="post" onsubmit="return onSubmitFormAjax(this)">
	        <input type="hidden" name="m" value="dPsante400" />
	        <input type="hidden" name="dosql" value="do_idsante400_aed" />
	        <input type="hidden" name="del" value="0" />
					{{mb_key object=$_id}}
	        {{mb_field hidden=1 object=$_id field=object_class}}
	        {{mb_field hidden=1 object=$_id field=object_id}}
	        {{mb_field hidden=1 object=$_id field=tag}}
          <input type="hidden" name="last_update" value="now" />
	        {{mb_field object=$_id field=id400}}
	        <button type="submit" class="notext submit singleclick">{{tr}}Submit{{/tr}}</button>
	      </form>
      {{else}}
      	{{$_id->id400}}
	    {{/if}}
      </td>
    {{/foreach}}
  </tr>
{{/foreach}}
</table>