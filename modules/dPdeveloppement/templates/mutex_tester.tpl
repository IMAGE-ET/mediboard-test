<h2>{{tr}}mutex_tester-title{{/tr}}</h2>

<div class="big-info">
	{{tr}}mutex_tester-info1{{/tr}}
	<br />
	{{tr}}mutex_tester-info2{{/tr}}
	<br />
	<strong>{{tr}}mutex_tester-info3{{/tr}}</strong>
</div>

<script type="text/javascript">

function test(action) {
  var url = new Url;
  url.setModuleAction("dPdeveloppement", "httpreq_test_mutex");
  url.addParam("action", action);
  url.requestUpdate(action);
}

</script>

<table class="tbl">
  <tr>
    <th>{{tr}}Action{{/tr}}</th>
    <th>{{tr}}Status{{/tr}}</th>
  </tr>
  
  {{foreach from=$actions item=_action}}
  <tr>
    <td><button class="tick" onclick="test('{{$_action}}')" >{{tr}}test_mutex-{{$_action}}{{/tr}}</button></td>
    <td id="{{$_action}}"></td>
  </tr>
	{{/foreach}}
</table>
