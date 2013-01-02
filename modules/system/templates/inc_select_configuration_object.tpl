<select id="object_guid-selector" onchange="editObjectConfig($V(this))">
  {{if $object_tree|@count > 1}}
    <option value="" selected="selected" disabled="disabled"> &mdash; </option>
  {{/if}}
  {{if $app->_ref_user->isAdmin()}}
    <option value="global"> Global </option>
  {{/if}}
  {{mb_include module=system template=inc_select_options_configuration items=$object_tree level=0}}
</select>

{{if $object_tree|@count == 1}}
<script type="text/javascript">
Main.add(function(){
  editObjectConfig($V($("object_guid-selector")));
});
</script>
{{/if}}