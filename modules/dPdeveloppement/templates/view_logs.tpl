{{* $Id$ *}}

<script>
Main.add(function(){
  {{if $conf.error_logs_in_db}}
    Control.Tabs.create("error-log-tabs", true);
    filterLogDB(getForm("filter-logs-db"));
  {{/if}}
});

function filterLogDB(form){
  var url = new Url("developpement", "ajax_list_error_logs");
  url.addFormData(form);
  url.requestUpdate("logs-db");

  return false;
}

function changePage(start) {
  var form = getForm("filter-logs-db");
  $V(form.start, start);
  form.onsubmit();
}

function toggleCheckboxes(checkbox) {
  var form = getForm("filter-logs-db");

  checkbox.up('fieldset').select('input.type').each(function(i){
    i.checked = checkbox.checked;
  });

  $V(form.start, 0);
}
</script>

{{if $conf.error_logs_in_db}}
  <ul id="error-log-tabs" class="control_tabs">
    <li><a href="#error-db">Journaux</a></li>
    <li><a href="#error-file">Fichier <small>({{$log_size}})</small></a></li>
  </ul>
{{/if}}

<div id="error-db" {{if !$conf.error_logs_in_db}} style="display: none;" {{/if}}>
  <form name="filter-logs-db" action="" method="get" onsubmit="return filterLogDB(this)">
    <input type="hidden" name="start" value="0" />

    <table class="layout">
      <tr>
        <td>
          <table class="main form">
            <tr>
              <th>{{mb_label object=$error_log field=text}}</th>
              <td>{{mb_field object=$error_log field=text prop=str}}</td>

              <th>{{mb_label object=$error_log field=_datetime_min}}</th>
              <td>{{mb_field object=$error_log field=_datetime_min register=true form="filter-logs-db"}}</td>
            </tr>
            <tr>
              <th>{{mb_label object=$error_log field=server_ip}}</th>
              <td>{{mb_field object=$error_log field=server_ip}}</td>

              <th>{{mb_label object=$error_log field=_datetime_max}}</th>
              <td>{{mb_field object=$error_log field=_datetime_max register=true form="filter-logs-db"}}</td>
            </tr>
            <tr>
              <th>Grouper les similaires</th>
              <td>
                <label>
                  <input type="radio" name="group_similar" value="1" {{if $group_similar}}checked{{/if}} onclick="$V(this.form.start, 0);" />
                  {{tr}}Yes{{/tr}}
                </label>
                <label>
                  <input type="radio" name="group_similar" value="0" {{if !$group_similar}}checked{{/if}} onclick="$V(this.form.start, 0);" />
                  {{tr}}No{{/tr}}
                </label>
              </td>

              <th>Trier par</th>
              <td>
                <select name="order_by">
                  <option value="date"     {{if $order_by == "date"}}     selected {{/if}}>{{tr}}CErrorLog-datetime{{/tr}}</option>
                  <option value="quantity" {{if $order_by == "quantity"}} selected {{/if}}>{{tr}}CErrorLog-_quantity{{/tr}}</option>
                </select>
              </td>
            </tr>
            <tr>
              <td></td>
              <td colspan="3">
                <button type="submit" class="search">{{tr}}Filter{{/tr}}</button>
              </td>
            </tr>
          </table>
        </td>
        <td>
          {{foreach from=$error_types key=_cat item=_types}}
            <fieldset style="display: inline-block;" class="error-{{$_cat}}">
              <legend>
                <label>
                  <input type="checkbox" onclick="toggleCheckboxes(this)" />
                  {{$_cat}}
                </label>
              </legend>

              {{foreach from=$_types item=_type}}
                <label>
                  <input type="checkbox" class="type" name="error_type[{{$_type}}]" value="1"
                    {{if array_key_exists($_type,$error_type)}} checked {{/if}}
                    onclick="$V(this.form.start, 0);" />
                  {{tr}}CErrorLog.error_type.{{$_type}}{{/tr}}
                </label>
              {{/foreach}}
            </fieldset>
          {{/foreach}}
        </td>
      </tr>
    </table>
  </form>

  <div id="logs-db"></div>
</div>

<div id="error-file">
  {{if $can->edit}}
  <button class="trash" type="button" onclick="removeByHash('clean')">
    {{tr}}Reset{{/tr}}
  </button>
  {{/if}}

  <button class="change" type="button" onclick="removeByHash()">
    {{tr}}Refresh{{/tr}}
  </button>

  <a class="button download" href="?m=developpement&amp;raw=download_log_file" target="_blank">
    {{tr}}Download{{/tr}}
  </a>

  <script type="text/javascript">
  Main.add(function(){
    var values = new CookieJar().get("filter-logs-file") || [".big-error", ".big-warning:not(.javascript)", ".big-info", ".javascript"];
    $V(getForm("filter-logs-file").filter, values);
    insertDeleteButtons();
    updateFilter();
  });

  function insertDeleteButtons(){
    $('logs-file').select('div[title]').each(function(e){
      e.insert({top: '<button class="trash notext" type="button" onclick="removeByHash(\''+e.title+'\')">Remove</button>'});
    });
  }

  function removeByHash(hash) {
    var url = new Url('dPdeveloppement', 'ajax_delete_logs');
    url.addParam('hash', hash);
    url.requestUpdate('logs-file', function(){
      insertDeleteButtons();
      updateFilter();
    });
  }

  function updateFilter() {
    var elements = getForm('filter-logs-file').filter;
    $A(elements).each(function(e){
      $('logs-file').select(e.value).invoke('setVisible', e.checked);
    });
    new CookieJar().put("filter-logs-file", $V(elements));
  }
  </script>

  <form name="filter-logs-file" action="" method="get" onsubmit="return false">
    <label><input type="checkbox" name="filter" value=".big-error" checked="checked" onchange="updateFilter()" /> Error</label>
    <label><input type="checkbox" name="filter" value=".big-warning:not(.javascript)" checked="checked" onchange="updateFilter()" /> Warning</label>
    <label><input type="checkbox" name="filter" value=".big-info" checked="checked" onchange="updateFilter()" /> Info</label>
    <label><input type="checkbox" name="filter" value=".javascript" checked="checked" onchange="updateFilter()" /> Javascript</label>
  </form>

  <div id="logs-file">
    {{$log|smarty:nodefaults}}
  </div>
</div>