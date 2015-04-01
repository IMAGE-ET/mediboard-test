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

function insertDeleteButtons(eltName = 'logs-file'){
  $(eltName).select('div[title]').each(function(e){
    e.insert({
      top: '<button class="trash notext" type="button" onclick="removeByHash(\''+e.title+'\', \''+eltName+'\')">Remove</button>'
    });
  });
}

function removeByHash(hash, eltName = 'logs-file') {
  new Url('dPdeveloppement', 'ajax_delete_logs')
    .addParam('hash', hash)
    .addParam('type', eltName)
    .requestUpdate(eltName, function(){
      insertDeleteButtons(eltName);
      updateFilter();
    });
}

function updateFilter(eltName = 'logs-file') {
  var elements = getForm('filter-'+eltName).filter;
  $A(elements).each(function(e){
    $(eltName).select(e.value).invoke('setVisible', e.checked);
  });
  new CookieJar().put("filter-"+eltName, $V(elements));
}
</script>

{{if $conf.error_logs_in_db}}
  <ul id="error-log-tabs" class="control_tabs">
    <li><a href="#error-db">Journaux</a></li>
    <li><a href="#error-file">Fichier <small>({{$log_size}})</small></a></li>
    {{if $conf.debug}}
      <li><a href="#debug">{{tr}}Debug{{/tr}} <small>({{$debug_size}})</small></a></li>
    {{/if}}
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
              <th>Groupement</th>
              <td>
                <select name="group_similar" onchange="$V(form.start, 0);">
                  <option value="similar"   {{if $group_similar == 'similar'}}    selected{{/if}}>Grouper les similaires</option>
                  <option value="signature" {{if $group_similar == 'signature'}}  selected{{/if}}>Grouper par signature </option>
                  <option value="no"        {{if $group_similar == 'no'}}         selected{{/if}}>Ne pas grouper        </option>
                </select>
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
              <th>Utilisateur</th>
              <td>
                <select name="user_id" class="ref" style="max-width: 14em;">
                  <option value="">&mdash; Tous les utilisateurs</option>
                  {{foreach from=$list_users item=_user}}
                    <option value="{{$_user->user_id}}" {{if $_user->user_id == $user_id}}selected="selected"{{/if}}>
                      {{$_user}}
                    </option>
                  {{/foreach}}
                </select>
              </td>

              <th>Type</th>
              <td>
                <label>
                  <input type="checkbox" name="human" value="1" {{if $human}}checked{{/if}}/>
                  {{tr}}Humans{{/tr}}
                </label>
                <label>
                  <input type="checkbox" name="robot" value="1" {{if $robot}}checked{{/if}}/>
                  {{tr}}Robots{{/tr}}
                </label>
              </td>
            </tr>
            <tr>
              <td></td>
              <td colspan="3">
                <button type="submit" class="search">{{tr}}Filter{{/tr}}</button>
                <button type="button" class="close" onclick="this.form.clear();">{{tr}}Reset{{/tr}}</button>
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

{{if $conf.debug}}
  <div id="debug">
    {{if $can->edit}}
      <button class="trash" type="button" onclick="removeByHash('clean', 'debug-file')">
        {{tr}}Reset{{/tr}}
      </button>
    {{/if}}

    <button class="change" type="button" onclick="removeByHash(null, 'debug-file')">
      {{tr}}Refresh{{/tr}}
    </button>

    <script type="text/javascript">
      Main.add(function(){
        var values = new CookieJar().get("filter-debug-file") || [".big-error", ".big-warning:not(.javascript)", ".big-info", ".javascript"];
        $V(getForm("filter-debug-file").filter, values);
        insertDeleteButtons('debug-file');
        updateFilter('debug-file');
      });
    </script>

    <form name="filter-debug-file" action="" method="get" onsubmit="return false">
      <label><input type="checkbox" name="filter" value=".big-error" checked="checked" onchange="updateFilter()" /> Error</label>
      <label><input type="checkbox" name="filter" value=".big-warning:not(.javascript)" checked="checked" onchange="updateFilter()" /> Warning</label>
      <label><input type="checkbox" name="filter" value=".big-info" checked="checked" onchange="updateFilter()" /> Info</label>
      <label><input type="checkbox" name="filter" value=".javascript" checked="checked" onchange="updateFilter()" /> Javascript</label>
    </form>

    <div id="debug-file">
      {{$debug|smarty:nodefaults}}
    </div>
  </div>
{{/if}}