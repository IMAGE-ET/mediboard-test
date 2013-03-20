<script>
  openModalTypeAnesth = function(type_id) {
    var url = new Url("planningOp", "ajax_form_typeanesth");
    url.addParam("type_anesth",type_id);
    url.requestModal();
  }
</script>

<!-- Liste des types d'anesthésie -->
<a class="button new" href="#" onclick="openModalTypeAnesth('0');">
  {{tr}}CTypeAnesth.create{{/tr}}
</a>

<form method="get" action="" name="showInactive">
  <input type="hidden" name="m" value="{{$m}}"/>
  <input type="hidden" name="tab" value="vw_edit_typeanesth"/>
  <input type="checkbox" onchange="$V(this.form.inactive, this.checked ? 1 : 0); this.form.submit()" {{if $show_inactive}}checked="checked"{{/if}} name="_show_caduc">
  <label for="showInactive__show_caduc">{{tr}}CRegleSectorisation-show-inactive{{/tr}}</label>
  <input type="hidden" name="inactive" value="{{$show_inactive}}" />
</form>

  <table class="tbl">
    <tr>
      <th>{{mb_title class=CTypeAnesth field=name}}</th>
      <th>{{tr}}CTypeAnesth-back-operations{{/tr}}</th>
      <th>{{mb_title class=CTypeAnesth field=ext_doc}}</th>
    </tr>
    {{foreach from=$types_anesth item=_type_anesth}}
    <tr {{if !$_type_anesth->actif}}class="hatching"{{/if}}">
      <td class="text">
        <a href="#{{$_type_anesth->_id}}" onclick="openModalTypeAnesth('{{$_type_anesth->_id}}');" title="{{tr}}CTypeAnesth-modify{{/tr}}">
          {{$_type_anesth->name}}
        </a>
      </td>
      <td>
        {{$_type_anesth->_count_operations}}
      </td>
      <td class="text {{if !$_type_anesth->ext_doc}} empty {{/if}}">
        {{mb_value object=$_type_anesth field=ext_doc}}
      </td>
    </tr>
    {{foreachelse}}
      <tr><td class="empty" colspan="3">{{tr}}CTypeAnesth.none{{/tr}}</td></tr>
    {{/foreach}}
  </table>