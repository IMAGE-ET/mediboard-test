{{* $id: $ *}}

<script type="text/javascript">
var urlDHEParams = {{$urlDHEParams|@json}};

newDHE = function() {
    var url = new Url;
    for (param in urlDHEParams) {
      if(param != "extends") {
        url.addParam(param, urlDHEParams[param]);
      }
    }
    url.popDirect("900", "600", "eCap", "{{$urlDHE|smarty:nodefaults}}");
}
</script>

{{if count($noDHEReasons)}}
<div class="little-warning" style="text-align: left">
	DHE non disponible pour la ou les raisons suivantes: 
	<ul>
	  {{foreach from=$noDHEReasons item=reason}}
	  <li>{{tr}}DHE-Reason-{{$reason}}{{/tr}}</li>
		{{/foreach}}
	</ul>
</div>

{{else}}
<button style="margin: 1px;" class="new" type="button" onclick="newDHE()">Nouvelle DHE</button>
<br />
{{/if}}