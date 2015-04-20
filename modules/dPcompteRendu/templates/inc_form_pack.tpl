{{assign var=pdf_thumbnails value=$conf.dPcompteRendu.CCompteRendu.pdf_thumbnails}}
{{assign var=pdf_and_thumbs value=$app->user_prefs.pdf_and_thumbs}}

<script>
  Main.add(function() {
    var form = getForm("Edit-CPack");

    var urlUsers = new Url("mediusers", "ajax_users_autocomplete");
    urlUsers.addParam("edit", "1");
    urlUsers.addParam("input_field", "user_id_view");
    urlUsers.autoComplete(form.user_id_view, null, {
      minChars: 0,
      method: "get",
      select: "view",
      dropdown: true,
      afterUpdateElement: function(field, selected) {
        var id = selected.getAttribute("id").split("-")[2];
        $V(form.user_id, id);
      }
    });

    {{if $access_function}}
    var urlFunctions = new Url("mediusers", "ajax_functions_autocomplete");
    urlFunctions.addParam("edit", "1");
    urlFunctions.addParam("input_field", "function_id_view");
    urlFunctions.addParam("view_field", "text");
    urlFunctions.autoComplete(form.function_id_view, null, {
      minChars: 0,
      method: "get",
      select: "view",
      dropdown: true,
      afterUpdateElement: function(field, selected) {
        var id = selected.getAttribute("id").split("-")[2];
        $V(form.function_id, id);
      }
    });
    {{/if}}

    {{if $access_group}}
    var urlGroups = new Url("etablissement", "ajax_groups_autocomplete");
    urlGroups.addParam("edit", "1");
    urlGroups.addParam("input_field", "group_id_view");
    urlGroups.addParam("view_field", "text");
    urlGroups.autoComplete(form.group_id_view, null, {
      minChars: 0,
      method: "get",
      select: "view",
      dropdown: true,
      afterUpdateElement: function(field, selected) {
        var id = selected.getAttribute("id").split("-")[2];
        $V(form.group_id, id);
      }
    });
    {{/if}}
  });
</script>

<form name="Edit-CPack" method="post" onsubmit="return onSubmitFormAjax(this)">
  {{mb_class object=$pack}}
  {{mb_key   object=$pack}}

  {{if (!$pdf_thumbnails || !$pdf_and_thumbs)}}
  <input type="hidden" name="fast_edit_pdf" value="{{$pack->fast_edit_pdf}}" />
  {{/if}}

  <table class="form">
    {{mb_include module=system template=inc_form_table_header object=$pack}}

    <tr>
      <th style="width: 40%;">{{mb_label object=$pack field=user_id}}</th>
      <td style="width: 60%;">
        {{mb_field object=$pack field=user_id hidden=1
        onchange="
             \$V(this.form.function_id, '', false);
             if (this.form.function_id_view) {
               \$V(this.form.function_id_view, '', false);
             }
             \$V(this.form.group_id, '', false);
             if (this.form.group_id_view) {
               \$V(this.form.group_id_view, '', false);
             }"}}
        <input type="text" name="user_id_view" value="{{$pack->_ref_user}}" />
      </td>
    </tr>

    {{if $access_function}}
    <tr>
      <th>{{mb_label object=$pack field=function_id}}</th>
      <td>
        {{mb_field object=$pack field=function_id hidden=1
        onchange="
             \$V(this.form.user_id, '', false);
             \$V(this.form.user_id_view, '', false);
             \$V(this.form.group_id, '', false);
             if (this.form.group_id_view) {
               \$V(this.form.group_id_view, '', false);
             }"}}
        <input type="text" name="function_id_view" value="{{$pack->_ref_function}}" />
      </td>
    </tr>
    {{/if}}

    {{if $access_group}}
    <tr>
      <th>{{mb_label object=$pack field=group_id}}</th>
      <td>
        {{mb_field object=$pack field=group_id hidden=1
        onchange="
             \$V(this.form.user_id, '', false);
             \$V(this.form.user_id_view, '', false);
             \$V(this.form.function_id, '', false);
             if (this.form.function_id_view) {
               \$V(this.form.function_id_view, '', false);
             }"}}
        <input type="text" name="group_id_view" value="{{$pack->_ref_group}}" />
      </td>
    </tr>
    {{/if}}

    <tr>
      <th>{{mb_label object=$pack field=nom}}</th>
      <td>{{mb_field object=$pack field=nom style="width: 16em;"}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$pack field=object_class}}</th>
      <td>
        <select name="object_class" style="width: 16em;" {{if $pack->_id}} onchange="Pack.changeClass(this);" {{/if}}>
          <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
          {{foreach from=$pack->_specs.object_class->_list item=object_class}}
            <option value="{{$object_class}}" {{if $object_class == $pack->object_class}} selected = "selected" {{/if}}>
              {{tr}}{{$object_class}}{{/tr}}
            </option>
          {{/foreach}}
        </select>
      </td>
    </tr>
    <tr>
      <th>{{mb_label object=$pack field=merge_docs}}</th>
      <td>{{mb_field object=$pack field=merge_docs}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$pack field=fast_edit}}</th>
      <td>{{mb_field object=$pack field=fast_edit}}</td>
    </tr>

    {{if $pdf_thumbnails && $pdf_and_thumbs}}
    <tr>
      <th>{{mb_label object=$pack field=fast_edit_pdf}}</th>
      <td>{{mb_field object=$pack field=fast_edit_pdf canNull=false}}</td>
    </tr>
    {{/if}}

    <tr>
      <td class="button" colspan="2">
        {{if $pack->_id}}
        <button class="modify" type="submit">
          {{tr}}Save{{/tr}}
        </button>
        <button class="trash" type="button" onclick="Pack.confirmDeletion(this.form);">
          {{tr}}Delete{{/tr}}
        </button>
        {{else}}
        <button class="submit" type="submit">
          {{tr}}Create{{/tr}}
        </button>
        {{/if}}
      </td>
    </tr>
  </table>
  
</form>

