{{* $Id$ *}}

{{if $can->edit}}
<button class="trash" type="button" onclick="removeByHash()">
  Réinitialiser les logs
</button>
{{/if}}

<script type="text/javascript">
Main.add(function(){
  var values = new CookieJar().get("filter-logs");
  $V(getForm("filter-logs").filter, values);
  $('logs').select('div[title]').each(function(e){
    e.insert({top: '<button class="trash notext" type="button" onclick="removeByHash(\''+e.title+'\')">Remove</button>'});
  });
});

function removeByHash(hash) {
  var url = new Url('dPdeveloppement', 'ajax_delete_logs');
  url.addParam('hash', hash);
  url.requestUpdate('logs');
}

function updateFilter(element) {
  $('logs').select('.'+element.value).invoke('setVisible', element.checked);
  new CookieJar().put("filter-logs", $V(element.form.elements[element.name]));
}
</script>

<form name="filter-logs" action="" method="get" onsubmit="return false">
  <label><input type="checkbox" name="filter" value="big-error" checked="checked" onchange="updateFilter(this)" /> Error</label>
  <label><input type="checkbox" name="filter" value="big-warning" checked="checked" onchange="updateFilter(this)" /> Warning</label>
  <label><input type="checkbox" name="filter" value="big-info" checked="checked" onchange="updateFilter(this)" /> Info</label>
</form>

<div id="logs">{{$logs|smarty:nodefaults}}</div>
