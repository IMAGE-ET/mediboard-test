{{* $Id: $ *}}

<form name="EditCategory" action="?m={{$m}}" method="post" onsubmit="return checkForm(this);">
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="dosql" value="do_dmi_category_aed" />

  <input type="hidden" name="category_id" value="{{$category->_id}}" />
  
  <table class="form">
	  <tr>
	    <th class="category {{if $category->_id}}modify{{/if}}" colspan="2">
	      {{if $category->_id}}
	      {{tr}}CDMICategory-title-modify{{/tr}} '{{$category->_view}}'
				{{else}}
				{{tr}}CDMICategory-title-create{{/tr}}
				{{/if}}
		    </th>
	  </tr>

    <tr>
      <th>{{mb_label object=$category field=group_id}}</th>
      <td>
        <select name="group_id" class="{{$category->_props.group_id}}">
          <option value="">&mdash; {{tr}}CGroups.select{{/tr}}</option>
          {{foreach from=$groups item=_group}}
          <option value="{{$_group->_id}}" {{if $_group->_id == $category->group_id}} selected="selected" {{/if}}>
            {{$_group->_view}}
          </option>
          {{/foreach}}
        </select>
      </td>
    </tr>

	  <tr>
	    <th>{{mb_label object=$category field=nom}}</th>
	    <td>{{mb_field object=$category field=nom}}</td>
	  </tr>

	  <tr>
	    <th>{{mb_label object=$category field=description}}</th>
	    <td>{{mb_field object=$category field=description}}</td>
	  </tr>

		<tr>
		  <td colspan="2" class="button">
		  	{{if $category->_id}}
				<button type="submit" class="submit">{{tr}}Modify{{/tr}}</button>
			{{else}}
				<button type="submit" class="submit">{{tr}}Save{{/tr}}</button>
			{{/if}}
		  </td>
		</tr>
	</table>
	
</form>
