<h1>Configuration de {{$m}}</h1>

<form name="editConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="dosql" value="do_configure" />
<input type="hidden" name="m" value="system" />

<table class="form">
  
  <!-- CSpObjectHandler --> 
  {{assign var=col value="object_handlers"}}
  {{assign var=class value="CSpObjectHandler"}}
  <tr>
    <th class="category" colspan="10">{{tr}}{{$class}}{{/tr}}</th>
  </tr>
  
  <tr>
    <th>
      <label for="{{$col}}[{{$class}}]" title="{{tr}}config-{{$col}}-{{$class}}{{/tr}}">
        {{tr}}config-{{$col}}-{{$class}}{{/tr}}
      </label>  
    </th>
    <td>
      <select class="bool" name="{{$col}}[{{$class}}]">
        <option value="0" {{if 0 == @$dPconfig.$col.$class}} selected="selected" {{/if}}>{{tr}}bool.0{{/tr}}</option>
        <option value="1" {{if 1 == @$dPconfig.$col.$class}} selected="selected" {{/if}}>{{tr}}bool.1{{/tr}}</option>
      </select>
    </td>
  </tr>  

	<tr>
	  {{assign var="var" value="import_segment"}}
	  <th>
	    <label for="{{$m}}[{{$var}}]" title="{{tr}}config-{{$m}}-{{$var}}{{/tr}}">
	      {{tr}}config-{{$m}}-{{$var}}{{/tr}}
	    </label>  
	  </th>
	  <td>
	    <input class="num" name="{{$m}}[{{$var}}]" value="{{$dPconfig.$m.$var}}" />
	  </td>
	</tr>
	
    
  <tr>
    <td class="button" colspan="2">
      <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
    </td>
  </tr>
  
</table>

</form>
  
<hr />

{{include file="../../system/templates/configure_dsn.tpl" dsn=sherpa-$g}}

<hr />

<!-- Import des tables -->
<script type="text/javascript">

var Action = {
  module: "sherpa",
  action: "httpreq_import_base",
  
  import: function (sClass, sAction) {
    var url = new Url;
    url.setModuleAction(this.module, this.action);
    url.addParam("class", sClass);
    url.addParam("action", sAction);
    url.requestUpdate("import-" + sClass);
  },
}

</script>

<table class="tbl">

<tr>
  <th class="title" colspan="100">{{tr}}sherpa-import-classes{{/tr}}</th>
</tr>

{{foreach from=$spClasses item=_class}}
<tr>
  <td>
    {{tr}}sherpa-import-class{{/tr}}
      '{{tr}}{{$_class}}{{/tr}}'
  </td>
  <td>
    <button class="new" onclick="Action.import('{{$_class}}', 'start')">
      {{tr}}Start{{/tr}}      
    </button>
    <button class="change" onclick="Action.import('{{$_class}}', 'retry')">
      {{tr}}Retry{{/tr}}      
    </button>
    <button class="tick" onclick="Action.import('{{$_class}}', 'continue')">
      {{tr}}Continue{{/tr}}      
    </button>
  </td>
  <td id="import-{{$_class}}"></td>
</tr>
{{/foreach}}
</table>
