<!--  $Id$ -->

{{mb_script module=compteRendu script=pack}}

<script>
  Main.add(function() {
    Pack.refreshList();

    var form = getForm("Filter");
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

  });
</script>

<button class="new" onclick="Pack.edit('0');">
  {{tr}}CPack-title-create{{/tr}}
</button>

<form name="Filter" method="get" onsubmit="return Pack.filter();">
  <table class="form">
    <tr>
       <th class="category" colspan="10">Filtrer les packs</th>
    </tr>

    <tr>
      <th>{{mb_label class=CPack field=user_id}}</th>
      <td>
        {{mb_field object=$filtre field=user_id hidden=1 onchange="\$V(this.form.function_id, '', false);
          if (this.form.function_id_view) {
            \$V(this.form.function_id_view, '', false);
          }
          this.form.onsubmit();"}}
        <input type="text" name="user_id_view" value="{{$filtre->_ref_user}}" />
      </td>

      {{if $access_function}}
        <th>{{mb_label class=CPack field=function_id}}</th>
        <td>
          {{mb_field object=$filtre field=function_id hidden=1 onchange="\$V(this.form.user_id, '', false); \$V(this.form.user_id_view, '', false); this.form.onsubmit();"}}
          <input type="text" name="function_id_view" value="{{$filtre->_ref_function}}" />
        </td>
      {{/if}}

      <th>{{mb_label class=CPack field=object_class}}</th>
      <td>
        <select name="object_class" onchange="this.form.onsubmit();">
          <option value="">&mdash; Tous</option>
          {{foreach from=$classes key=_class item=_locale}}
            <option value="{{$_class}}" {{if $_class == $filtre->object_class}}selected{{/if}}>
              {{$_locale}}
            </option>
          {{/foreach}}
        </select>
      </td>
    </tr>
  </table>
</form>

<div id="list-packs"></div>
