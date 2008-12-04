{{* $Id: $ *}}

<a class="buttonnew" href="?m={{$m}}&amp;tab={{$tab}}&amp;dmi_id=0">
  {{tr}}CDMI-title-create{{/tr}}
</a>

<table class="tbl">
  <tr>
    <th>{{mb_title class=CDMI field=nom}}</th>
    <th>{{mb_title class=CDMI field=description}}</th>
    <th>{{mb_title class=CDMI field=code}}</th>
    <th>{{mb_title class=CDMI field=dans_livret}}</th>
    <th>{{mb_title class=CDMI field=_produit_existant}}</th>
    <th>{{mb_title class=CProduct field=renewable}}</th>
  </tr>

	{{foreach from=$DMICategories item=_category}}
		<tr>
		  <th class="category" colspan="10">
		    <a href="?m={{$m}}&amp;tab=vw_categories&amp;category_id={{$_category->_id}}">
			    {{$_category->_view}}
			  </a>
		  </th>
		</tr>

		{{foreach from=$_category->_ref_dmis item=_dmi}}
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
	    	{{mb_value object=$_dmi field=code}}
	    </td>
	    <td>
	    	{{mb_value object=$_dmi field=dans_livret}}
	    </td>
	    <td> 
	        {{if $_dmi->_produit_existant}}
	          <a href="?m=dPstock&amp;tab=vw_idx_product&amp;product_id={{$_dmi->_ref_product->_id}}">
            {{mb_value object=$_dmi field=_produit_existant}}
            </a>
	       {{else}}
	         {{mb_value object=$_dmi field=_produit_existant}}
	       {{/if}}
	      
	    </td>
      <td>
        {{mb_value object=$_dmi->_ref_product field=renewable}}
      </td>
	  </tr>
	  {{foreachelse}}
	  <tr>
	    <td colspan="10"><em>{{tr}}CDMI.none{{/tr}}</em></td>
	  </tr>
		{{/foreach}}

  {{foreachelse}}
  <tr>
    <td colspan="10"><em>{{tr}}CDMICategory.none{{/tr}}</em></td>
  </tr>
	{{/foreach}}

</table>