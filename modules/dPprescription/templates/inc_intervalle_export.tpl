{{if $nb_protocoles == 0}}
  <script type="text/javascript">
  getForm("exportProtocoles").export_button.disabled = "true";
  </script>
{{else}}
  <select name="lower_bound">
    {{foreach from=1|range:$nb_protocoles item=i}}
      <option value="{{$i}}">{{$i}}</option>
    {{/foreach}}
  </select>
  <select name="upper_bound">
    {{foreach from=1|range:$nb_protocoles item=i}}
      <option value="{{$i}}">{{$i}}</option>
    {{/foreach}}
  </select>
  
  <script type="text/javascript">
    var oForm = getForm("exportProtocoles"); 
    oForm.upper_bound.selectedIndex = oForm.upper_bound.length - 1;
  </script>
{{/if}}