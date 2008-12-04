{{* $Id: $ *}}

<script type="text/javascript">
  var modifOk = false;
  function codeModified() {
    if (!modifOk) {
      return modifOk = confirm('Voulez vous réélement modifier le code ?\n Ceci peut impliquer des incohérences.');
    }
    else return true;
  }
</script>

<form name="EditDMI" action="?m={{$m}}" method="post" onsubmit="return checkForm(this);">
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="dosql" value="do_dmi_aed" />
  <input type="hidden" name="dmi_id" value="{{$dmi->_id}}" />
  
  <table class="form">
  	<tr>
  		<th class="category {{if $dmi->_id}}modify{{/if}}" colspan="2">
  			{{if $dmi->_id}}
	      	{{tr}}CDMI-title-modify{{/tr}} '{{$dmi->_view}}'
				{{else}}
				{{tr}}CDMI-title-create{{/tr}}
				{{/if}}
  		</th>
  	</tr>
  	<tr>
      <th>{{mb_label object=$dmi field=category_id}}</th>
      <td>
        <select name="category_id" class="{{$dmi->_props.category_id}}">
          <option value="">&mdash; Choisir une catégorie</option>
          {{foreach from=$DMICategories item=_DMICategory}}
          <option value="{{$_DMICategory->_id}}" {{if $_DMICategory->_id == $dmi->category_id}} selected="selected" {{/if}}>
            {{$_DMICategory->_view}}
          </option>
          {{/foreach}}
        </select>
      </td>
    </tr>
  	<tr>
  		<th>{{mb_label object=$dmi field=nom}}</th>
  		<td>{{mb_field object=$dmi field=nom}}</td>
  	</tr>
  	<tr>
  		<th>{{mb_label object=$dmi field=description}}</th>
  		<td>{{mb_field object=$dmi field=description}}</td>
  	</tr>
  	<tr>
  		<th>{{mb_label object=$dmi field=code}}</th>
  		<td>{{mb_field object=$dmi field=code onkeypress="return codeModified()"}}</td>
  	</tr>
  	<tr>
  		<th>{{mb_label object=$dmi field=dans_livret}}</th>
  		<td>{{mb_field object=$dmi field=dans_livret}}</td>
  	</tr>
  	<tr>
		  <td colspan="2" class="button">
		  	{{if $dmi->_id}}
				<button type="submit" class="submit">{{tr}}Modify{{/tr}}</button>
			{{else}}
				<button type="submit" class="submit">{{tr}}Save{{/tr}}</button>
			{{/if}}
		  </td>
		</tr>
  </table>
 </form>