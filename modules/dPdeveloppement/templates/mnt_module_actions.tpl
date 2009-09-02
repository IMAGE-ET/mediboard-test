

<script type="text/javascript">

Documentation = {
  check: function(page) {
	  var url = new Url("dPdeveloppement", "ajax_check_documentation");
	  url.addParam("page", page);
	  url.requestUpdate(page);
  },
  checkAll: function() {
  	$$("td.page").each(function(element) { 
  		Documentation.check(element.id);
  	} );
  }
}


</script>

<button class="change" onclick="Documentation.checkAll()" >
  Check all documentation
</button>

<table class="tbl">
  <tr>
    <th>Action</th>
    <th>Locale</th>
    <th colspan="2">Documentation</th>
  </tr>

	{{foreach from=$modules item=module}}
  <tr>
    <th class="category" colspan="2">mod-{{$module->mod_name}}</th>
	  <td class="page" id="mod-{{$module->mod_name}}" />
	  <td style="width: 1%">
	  	<a class="button search notext" href="http://www.mediboard.org/public/mod-{{$module->mod_name}}">
	  		{{tr}}Link{{/tr}}
	  	</a>
	  </td>
  </tr>

	  {{foreach from=$module->_tabs item=tab}}
		<tr>
		  <td>{{$tab.0}}</td>
		  <td class="{{mb_ternary test=$tab.locale value=ok other=warning}}">
		    {{$tab.locale|default:$tab.name}}
		  </td>
		  <td class="page" id="{{$tab.name}}" />
		  <td style="width: 1%">
		  	<a class="button search notext" href="http://www.mediboard.org/public/{{$tab.name}}">
		  		{{tr}}Link{{/tr}}
		  	</a>
		  </td>
		</tr>	
		{{/foreach}}
	{{/foreach}}
</table>

