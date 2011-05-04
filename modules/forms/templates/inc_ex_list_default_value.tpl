{{if $spec instanceof CSetSpec}}
  <script type="text/javascript">
    Main.add(function(){
      var form = getForm('editFieldSpec');
      window.__defaultTF_{{$value}} = new TokenField(form["default"], {onChange : updateFieldSpec});
    });
  </script>
	<label style="display: block;">
	  <input type="checkbox" name="__default_item" {{if in_array($value, $spec->_list_default)}}checked="checked"{{/if}} 
	         onclick="window.__defaultTF_{{$value}}.toggle(this.value, this.checked)" value="{{$value}}" />
	</label>
{{else}}
  <label style="display: block;">
	  <input type="radio" name="__default_item" {{if $value == $spec->default}}checked="checked"{{/if}} 
	         onclick="$V(getForm('editFieldSpec')['default'], this.value); updateFieldSpec();" value="{{$value}}" />
	</label>
{{/if}}