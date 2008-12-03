{{* $Id: $ *}}

<a class="buttonnew" href="?m={{$m}}&amp;tab={{$tab}}&amp;category_id=0">
  {{tr}}CDMICategory-title-create{{/tr}}
</a>

<table class="tbl">
  <tr>
    <th>{{mb_title class=CDMICategory field=nom}}</th>
    <th>{{mb_title class=CDMICategory field=description}}</th>
    <th colspan="2">Nombre de DMI</th>
  </tr>

  {{foreach from=$groups item=_group}}
    <tr>
      <th class="category" colspan="10">{{$_group->_view}}</th>
    </tr>
	   {{foreach from=$_group->_ref_dmi_categories item=_category}}
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
          {{$_category->_count_dmis}}
        </td>
        {{if $_category->group_id == $g}}
        <td class="button" style="width: 1%">
          <a class="buttonadd action" href="?m={{$m}}&amp;tab=vw_elements&amp;dmi_id=0&amp;category_id={{$_category->_id}}">
            {{tr}}Add{{/tr}}
          </a>
        </td>
        {{else}}
          <td></td>
        {{/if}}
      </tr>
     {{foreachelse}}
      <tr>
        <td colspan="10"><em>{{tr}}CDMICategory.none{{/tr}}</em></td>
      </tr>
	   {{/foreach}}
	   {{/foreach}}

</table>


