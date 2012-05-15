<script type="text/javascript">
updateObjectTree = function(inherit) {
  if (!inherit) {
    return;
  }
  
  var url = new Url("system", "ajax_configuration_object_tree");
  url.addParam("inherit", inherit);
  url.requestUpdate("object_guid-selector-container");
}

editObjectConfig = function(object_guid) {
  var url = new Url("system", "ajax_edit_object_config");
  url.addParam("object_guid", object_guid);
  url.addParam("module", "{{$module}}");
  url.addParam("inherit", $V($("inherit")));
  url.requestUpdate("object-config-editor");
}

Main.add(function(){
  updateObjectTree($V($("inherit")));
});
</script>

<table class="main form">
  <tr>
    <th class="narrow">Schema</th>
    <td class="narrow">
      <select id="inherit" onchange="updateObjectTree($V(this))">
        {{if !$inherit}}
          {{foreach from=$all_inherits item=_inherit}}
            <option value="{{$_inherit}}">{{$_inherit}}</option>
          {{/foreach}}
        {{else}}
          <option value="{{$inherit}}">{{$inherit}}</option>
        {{/if}}
      </select>
    </td>
    
    <th class="narrow">Contexte</th>
    <td class="narrow" id="object_guid-selector-container"></td>
    <td></td>
  </tr>
</table>

<div id="object-config-editor"></div>
