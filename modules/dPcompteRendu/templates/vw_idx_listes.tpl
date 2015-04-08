<!--  $Id$ -->

{{mb_script module=compteRendu script=liste_choix}}

<script>
  Main.add(function() {
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

    ListeChoix.filter();
  });
</script>

<button id="vw_idx_list_create_list_choix" class="new singleclick" onclick="ListeChoix.edit(0);">
  {{tr}}CListeChoix-title-create{{/tr}}
</button>
    
<form name="Filter" method="get" onsubmit="return ListeChoix.filter();">
  <table class="form">
    <tr>
      <th class="category" colspan="4">{{tr}}Filter{{/tr}}</th>
    </tr>
    <tr>
      <th>{{mb_label object=$filtre field=user_id}}</th>
      <td>
        {{mb_field object=$filtre field=user_id hidden=1 onchange="\$V(this.form.function_id, '', false); \$V(this.form.function_id_view, '', false); this.form.onsubmit();"}}
        <input type="text" name="user_id_view" value="{{$filtre->_ref_user}}" />
      </td>
      <th>{{mb_label object=$filtre field=function_id}}</th>
      <td>
        {{mb_field object=$filtre field=function_id hidden=1 onchange="\$V(this.form.user_id, '', false); \$V(this.form.user_id_view, '', false); this.form.onsubmit();"}}
        <input type="text" name="function_id_view" value="{{$filtre->_ref_function}}" />
      </td>
    </tr>
  </table>
</form>

<div id="list-listes_choix"></div>
 
