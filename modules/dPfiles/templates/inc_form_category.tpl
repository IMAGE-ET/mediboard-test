<form name="EditCat" action="?m={{$m}}&amp;tab=vw_category" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="m" value="dPfiles" />
<input type="hidden" name="dosql" value="do_filescategory_aed" />
<input type="hidden" name="file_category_id" value="{{$category->_id}}" />
<input type="hidden" name="del" value="0" />

<table class="form">
  <tr>
    {{if $category->_id}}
    <th class="title modify" colspan="2">
      {{mb_include module=system template=inc_object_idsante400 object=$category}}
      {{mb_include module=system template=inc_object_history object=$category }}
       
    	{{tr}}CFilesCategory-title-modify{{/tr}} '{{$category}}'
    </th>
    {{else}}
    <th class="title" colspan="2">{{tr}}CFilesCategory-title-create{{/tr}}</th>
    {{/if}}
  </tr> 
  
  <tr>
    <th>{{mb_label object=$category field=nom}}</th>
    <td>{{mb_field object=$category field=nom}}</td>
  </tr>

  <tr>
    <th>{{mb_label object=$category field=send_auto}}</th>
    <td>{{mb_field object=$category field=send_auto}}</td>
  </tr>

  <tr>
    <th>{{mb_label object=$category field=class}}</th>
    <td>
      {{if $category->_count_doc_items}}
      {{tr}}{{$category->class|default:'All'}}{{/tr}}
      {{else}}
      <select name="class">
      <option value="">&mdash; Toutes</option>
      {{foreach from=$listClass item=_class}}
      <option value="{{$_class}}"{{if $category->class==$_class}} selected="selected"{{/if}}>{{tr}}{{$_class}}{{/tr}}</option>
      {{/foreach}}
      </select>
      {{/if}}
    </td>
  </tr>        
  <tr>
    <td class="button" colspan="2">
      {{if $category->_id}}
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
        <button class="trash" type="button" onclick="confirmDeletion(this.form, {typeName:'la catégorie',objName: this.form.nom.value })">{{tr}}Delete{{/tr}}</button>
      {{else}}
        <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
      {{/if}}
    </td>
  </tr>  
</table>

</form>

<table class="form">
  <tr>
    <th class="category" colspan="2">Objets liés</th>
  </tr>
  
  <tr>
    <th>{{tr}}CFilesCategory-back-files{{/tr}}</th>
    <td>{{$category->_count_files}}</td>
  </tr>

  <tr>
    <th>{{tr}}CFilesCategory-back-documents{{/tr}}</th>
    <td>{{$category->_count_documents}}</td>
  </tr>
</table>