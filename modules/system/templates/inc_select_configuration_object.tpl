<select id="object_guid-selector" onchange="editObjectConfig($V(this))">
  <option value="" selected="selected" disabled="disabled"> &mdash; </option>
  <option value="global"> Global </option>
  {{mb_include module=system template=inc_select_options_configuration items=$object_tree level=0}}
</select>