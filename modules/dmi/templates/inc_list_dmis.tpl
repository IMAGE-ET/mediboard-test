{{* $Id: $ *}}

<a class="buttonnew" href="?m={{$m}}&amp;tab={{$tab}}&amp;dmi_id=0">
  {{tr}}CDMI-title-create{{/tr}}
</a>

<table class="tbl">
  <tr>
    <th>{{mb_title class=CDMI field=nom}}</th>
    <th>{{mb_title class=CDMI field=description}}</th>
    <th>{{mb_title class=CDMI field=reference}}</th>
    <th>{{mb_title class=CDMI field=lot}}</th>
    <th>{{mb_title class=CDMI field=category_id}}</th>
    <th>{{mb_title class=CDMI field=en_lot}}</th>
    <th>{{mb_title class=CDMI field=dans_livret}}</th>
  </tr>

	{{foreach from=$dmis item=_dmi}}
  <tr {{if $_dmi->_id == $dmi->_id}}class="selected"{{/if}}>
    <td>
      <a href="?m={{$m}}&amp;tab={{$tab}}&amp;dmi_id={{$_dmi->_id}}">
    		{{mb_value object=$_dmi field=nom}}
    	</a>
    </td>
    <td>
    	{{mb_value object=$_dmi field=description}}
    </td>
    <td>
    	{{mb_value object=$_dmi field=reference}}
    </td>
    <td>
    	{{mb_value object=$_dmi field=lot}}
    </td>
    <td>
      {{assign var=category_id value=$_dmi->category_id}}
    	{{$DMICategories.$category_id->_view}}
    </td>
    <td>
    	{{mb_value object=$_dmi field=en_lot}}
    </td>
    <td>
    	{{mb_value object=$_dmi field=dans_livret}}
    </td>
  </tr>
	{{/foreach}}

</table>