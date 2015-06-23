<script>
  {{if $category->_id}}
    var target = $('category_line_{{$category->_id}}');
    if (target) {
      target.addUniqueClassName('selected');
    }
  {{else}}
    $$('#list_file_categories tr').each(function(line) {
      line.removeClassName('selected');
    });
  {{/if}}

</script>

<form name="EditCat" action="?" method="post" onsubmit="return onSubmitFormAjax(this);">
  <input type="hidden" name="m" value="dPfiles" />
  <input type="hidden" name="dosql" value="do_filescategory_aed" />
  <input type="hidden" name="file_category_id" value="{{$category->_id}}" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="callback" value="FilesCategory.callback"/>

  <table class="form">
    {{mb_include module=system template=inc_form_table_header object=$category}}

    <tr>
      <th>{{mb_label object=$category field=nom}}</th>
      <td>{{mb_field object=$category field=nom}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$category field=send_auto}}</th>
      <td>{{mb_field object=$category field=send_auto}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$category field=eligible_file_view}}</th>
      <td>{{mb_field object=$category field=eligible_file_view}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$category field=class}}</th>
      <td>
        {{if $category->_count_doc_items}}
        {{tr}}{{$category->class|default:'All'}}{{/tr}}
        {{else}}
        <select name="class">
          <option value="">&mdash; {{tr}}All{{/tr}}</option>
          {{foreach from=$listClass item=_class}}
            <option value="{{$_class}}">
              {{$_class}}
            </option>
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
    <th style="width:50%;"><strong>{{tr}}CFilesCategory-back-files{{/tr}}</strong></th>
    <td {{if !$category->_count_files}}class="empty"{{/if}}>{{$category->_count_files}}</td>
  </tr>

  <tr>
    <th><strong>{{tr}}CFilesCategory-back-documents{{/tr}}</strong></th>
    <td {{if !$category->_count_documents}}class="empty"{{/if}}>{{$category->_count_documents}}</td>
  </tr>
</table>