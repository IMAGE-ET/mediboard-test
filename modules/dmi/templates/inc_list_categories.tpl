{{* $Id: $ *}}

<a class="buttonnew" href="?m={{$m}}&amp;tab={{$tab}}&amp;category_id=0">
  {{tr}}CDMICategory-title-create{{/tr}}
</a>

<table class="tbl">
  <tr>
    <th>{{mb_title class=CDMICategory field=nom}}</th>
    <th>{{mb_title class=CDMICategory field=description}}</th>
    <th>{{mb_title class=CDMICategory field=group_id}}</th>
  </tr>

	{{foreach from=$categories item=_category}}
  <tr {{if $_category->_id == $category->_id}}class="selected"{{/if}}>
    <td>
      <a href="?m={{$m}}&amp;tab={{$tab}}&amp;category_id={{$_category->_id}}">
    		{{mb_value object=$_category field=nom}}
    	</a>
    </td>
    <td>
    	{{mb_value object=$_category field=description}}
    </td>
    <td>
      {{assign var=group_id value=$_category->group_id}}
    	{{$groups.$group_id->_view}}
    </td>
  </tr>
	{{/foreach}}

</table>


