{{* $Id$ *}}

{{if $can->edit}}
<button class="trash" type="button" onclick="removeByHash('clean')">
  {{tr}}Reset{{/tr}}
</button>
{{/if}}

<button class="change" type="button" onclick="removeByHash()">
  {{tr}}Refresh{{/tr}}
</button>

<script type="text/javascript">
Main.add(function(){
  var values = new CookieJar().get("filter-logs") || ["big-error", "big-warning", "big-info"];
  $V(getForm("filter-logs").filter, values);
  insertDeleteButtons();
  updateFilter();
});

function insertDeleteButtons(){
  $('logs').select('div[title]').each(function(e){
    e.insert({top: '<button class="trash notext" type="button" onclick="removeByHash(\''+e.title+'\')">Remove</button>'});
  });
}

function removeByHash(hash) {
  var url = new Url('dPdeveloppement', 'ajax_delete_logs');
  url.addParam('hash', hash);
  url.requestUpdate('logs', { onComplete: function(){insertDeleteButtons(); updateFilter();}});
}

function updateFilter() {
  var elements = getForm('filter-logs').filter;
  $A(elements).each(function(e){
    $('logs').select('.'+e.value).invoke('setVisible', e.checked);
  });
  new CookieJar().put("filter-logs", $V(elements));
}
</script>

<form name="filter-logs" action="" method="get" onsubmit="return false">
  <label><input type="checkbox" name="filter" value="big-error" checked="checked" onchange="updateFilter()" /> Error</label>
  <label><input type="checkbox" name="filter" value="big-warning" checked="checked" onchange="updateFilter()" /> Warning</label>
  <label><input type="checkbox" name="filter" value="big-info" checked="checked" onchange="updateFilter()" /> Info</label>
</form>

<div id="logs">
  {{$log|smarty:nodefaults}}
</div>
