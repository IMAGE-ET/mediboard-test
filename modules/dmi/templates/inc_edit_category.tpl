<script type="text/javascript">

// refresh apres une sauvegarde ou une suppression
refreshCat = function(category_id){
  viewListCat('{{$category->_class_name}}', category_id);
  viewCat('{{$category->_class_name}}', category_id);
}

</script>

{{if $category->_class_name == "CDMICategory"}}
  {{assign var=dosql value=do_dmi_category_aed}}
  {{assign var=object_class value=CDMI}}
{{else}}
  {{assign var=dosql value=do_category_dm_aed}}
  {{assign var=object_class value=CDM}}
{{/if}}

<form name="editCategory" action="?m={{$m}}" method="post" onsubmit="return checkForm(this);">
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="dosql" value="{{$dosql}}" />
  {{if !$category->_id}}
  <input type="hidden" name="callback" value="refreshCat" />
  {{/if}}
  <input type="hidden" name="{{$category->_spec->key}}" value="{{$category->_id}}" />
  <input type="hidden" name="group_id" value="{{$g}}" />
  <table class="form">
	  <tr>
	    <th class="category {{if $category->_id}}modify{{/if}}" colspan="2">
	      {{if $category->_id}}
	      {{tr}}{{$category->_class_name}}-title-modify{{/tr}} '{{$category->_view}}'
				{{else}}
				{{tr}}{{$category->_class_name}}-title-create{{/tr}}
				{{/if}}
		    </th>
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
				  <button type="button" class="trash" onclick="this.form.del.value = 1; submitFormAjax(this.form, 'systemMsg', { onComplete: function() { refreshCat('0') } } )">Supprimer</button>
				  <button type="button" class="submit" onclick="submitFormAjax(this.form, 'systemMsg', { onComplete: function(){ viewListCat('{{$category->_class_name}}','{{$category->_id}}') } } );">{{tr}}Modify{{/tr}}</button>
			  {{else}}
			  	<button type="button" class="submit" onclick="submitFormAjax(this.form, 'systemMsg');">{{tr}}Save{{/tr}}</button>
			  {{/if}}
		  </td>
		</tr>
	</table>
</form>

<!-- Affichage du detail d'une categorie -->
{{if $category->_id}}
	<table class="tbl">
	  <tr>
	    <th colspan="10">Elements dans cette categorie</th>
	  </tr>
	  <tr>  
	    <th>{{mb_title class=$object_class field=nom}}</th>
	    <th>{{mb_title class=$object_class field=code}}</th>
	  </tr>
	  {{foreach from=$category->_ref_elements item=_element}}
	  <tr>
	    <td>{{mb_value object=$_element field=nom}}</td>
	    <td>{{mb_value object=$_element field=code}}</td>
	  </tr>
	  {{foreachelse}}
	  <tr>
	    <td colspan="10"><em>{{tr}}{{$object_class}}.none{{/tr}}</em></td>
	  </tr>
	  {{/foreach}}
	</table>
{{/if}}