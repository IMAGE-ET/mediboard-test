{{unique_id var=uid}}

<script type="text/javascript">
updateObjectTree = function(inherit) {
  if (!inherit) {
    return;
  }

  var url = new Url("system", "ajax_configuration_object_tree");
  url.addParam("inherit", inherit);
  url.requestUpdate("object_guid-selector-container-{{$uid}}");
};

editObjectConfig = function(object_guid) {
  var url = new Url("system", "ajax_edit_object_config");
  url.addParam("object_guid", object_guid);
  url.addParam("module", "{{$module}}");
  url.addParam("inherit", $V($("inherit-{{$uid}}")));
  url.requestUpdate("object-config-editor-{{$uid}}");
};

Main.add(function(){
  updateObjectTree($V($("inherit-{{$uid}}")));
});
</script>

<table class="main form">
  <tr>
    <th class="narrow">Schema</th>
    <td class="narrow">
      <select id="inherit-{{$uid}}" onchange="updateObjectTree($V(this))">
        {{foreach from=$all_inherits item=_inherit}}
          {{if $inherit|@count == 0 || in_array($_inherit, $inherit)}}
            <option value="{{$_inherit}}">
              {{tr}}config-inherit-{{$_inherit}}{{/tr}}
            </option>
          {{/if}}
        {{/foreach}}
      </select>
    </td>

    <th class="narrow">Contexte</th>
    <td class="narrow" id="object_guid-selector-container-{{$uid}}"></td>
    <td></td>
  </tr>
</table>

<div id="object-config-editor-{{$uid}}"></div>
