{{* $id: $ *}}

<h1>Liste des indentifiants synchronisés</h1>

<table class="tbl">
  <tr>
  	<th class="title" colspan="100">
  		{{tr}}CGroups{{/tr}}
		</th>
	</tr>

  <tr>
    <th>{{mb_title class=CGroups field=text}}</th>
    {{foreach from=$groups_tags key=_tag item=_editable}}
    <th>{{$_tag}}</th>
    {{/foreach}}
  </tr>

{{foreach from=$groups key=group_id item=_group name=groups}}

  {{assign var=ids value=$groups_ids.$group_id}}
	
  <tr>
    <td>{{$_group}}</td>
		{{assign var=id value=$ids.eCap}}
    {{foreach from=$ids key=_tag item=_id}}
      {{if $groups_tags.$_tag}} 
      <td>
	      <form name="EditId-{{$_group->_guid}}-{{$_tag|replace:' ':'_'}}" action="?" method="post" onsubmit="return onSubmitFormAjax(this)">
	        <input type="hidden" name="m" value="dPsante400" />
	        <input type="hidden" name="dosql" value="do_idsante400_aed" />
	        <input type="hidden" name="del" value="0" />
					{{mb_key object=$_id}}
	        {{mb_field hidden=1 object=$_id field=object_class}}
	        {{mb_field hidden=1 object=$_id field=object_id}}
	        {{mb_field hidden=1 object=$_id field=tag}}
          <input type="hidden" name="last_update" value="now" />
	        {{mb_field object=$_id field=id400}}
	        <button type="submit" class="notext submit">{{tr}}Submit{{/tr}}</button>
	      </form>
      </td>
      {{else}}
      <td>{{$_id->id400}}</td>
      {{/if}}
    {{/foreach}}
  </tr>
{{/foreach}}
</table>