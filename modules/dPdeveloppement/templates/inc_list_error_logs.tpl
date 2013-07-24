{{* $Id$ *}}

<script>
Main.add(function(){
  Control.Tabs.setTabCount("error-db", "{{$total}}");
});
</script>

<style type="text/css">
.error-logs pre {
  width: 100%;
  border: none;
  margin: 0;
  max-height: 220px;
}

.error-logs tr td {
  vertical-align: top;
  padding: 1px;
}
</style>

{{if $list_ids}}
  <div style="float: left">
    <form name="delete-logs-db" action="" method="post" onsubmit="return onSubmitFormAjax(this, filterLogDB.curry(getForm('filter-logs-db')))">
      <input type="hidden" name="m" value="developpement" />
      <input type="hidden" name="dosql" value="do_error_log_multi_delete" />
      <input type="hidden" name="log_ids" value="{{'-'|implode:$list_ids}}" />
      <button class="trash compact" onclick="return confirm('Voulez-vous supprimer ces {{$list_ids|@count}} journaux d\'erreur ?');">
        {{tr}}Delete{{/tr}}
      </button>
    </form>

    <form name="manage-logs-db" action="" method="post" onsubmit="return onSubmitFormAjax(this, filterLogDB.curry(this))">
      <input type="hidden" name="m" value="developpement" />
      <input type="hidden" name="dosql" value="do_error_log_purge" />
      <button class="trash compact" onclick="return confirm('Voulez-vous vider complètement les journaux d\'erreur ?')">
        {{tr}}Reset{{/tr}}
      </button>
    </form>
  </div>
{{/if}}

{{mb_include module=system template=inc_pagination change_page="changePage" total=$total current=$start step=30}}

<table class="main tbl error-logs">
  <tr>
    <th></th>
    <th>{{mb_title class=CErrorLog field=stacktrace_id}}</th>
    <th>{{mb_title class=CErrorLog field=param_GET_id}}</th>
    <th>{{mb_title class=CErrorLog field=param_POST_id}}</th>
    <th>{{mb_title class=CErrorLog field=session_data_id}}</th>
  </tr>
  {{foreach from=$error_logs item=_log}}
    <tbody>
      <tr style="border-top: 2px solid #666;">
        <td class="narrow error-{{$_log->_category}}" rowspan="2" style=" line-height: 1.5;" title="{{mb_value object=$_log field=error_type}}">
          {{if $group_similar && $group_similar !== 'no'}}
            <div class="rank">{{$_log->_similar_count}}</div>

            <form name="delete-error-log-{{$_log->_id}}" method="post"
                  onsubmit="return onSubmitFormAjax(this, function(){getForm('filter-logs-db').onsubmit()})">
              <input type="hidden" name="m" value="developpement" />
              <input type="hidden" name="dosql" value="do_error_log_multi_delete" />

              {{assign var=_log_ids value="-"|implode:$_log->_similar_ids}}
              <input type="hidden" name="log_ids" value="{{$_log_ids}}" />
              <button class="trash notext">{{tr}}Delete{{/tr}}</button>
            </form>
          {{else}}
            <form name="delete-error-log-{{$_log->_id}}" method="post"
                  onsubmit="return onSubmitFormAjax(this, function(){getForm('filter-logs-db').onsubmit()})">
              {{mb_class object=$_log}}
              {{mb_key object=$_log}}
              <input type="hidden" name="del" value="1" />
              <button class="trash notext">{{tr}}Delete{{/tr}}</button>
            </form>
          {{/if}}

          <br />
          {{mb_value object=$_log field=server_ip}}<br />
          {{mb_value object=$_log field=datetime}}<br />

          {{mb_value object=$_log field=user_id tooltip=true}}
        </td>

        <td class="narrow text" style="font-weight: bold;">
          {{$_log->text}}
        </td>

        <td style="width: 15%; max-width: 200px; position: relative;" rowspan="2">
          {{if $_log->_url}}
            <a href="{{$_log->_url}}" class="button link" target="_blank" style="position: absolute; right: 0;">
              Lien
            </a>
          {{/if}}

          <pre>{{$_log->_param_GET|@print_r:true}}</pre>
        </td>

        <td style="width: 15%; max-width: 200px;" rowspan="2">
          <pre>{{$_log->_param_POST|@print_r:true}}</pre>
        </td>

        <td style="width: 30%; max-width: 200px;" rowspan="2">
          <button class="lookup compact" onclick="this.next().toggle()">
            {{$_log->_session_data|@count}}
          </button>
          <div style="display: none;">
            <pre>{{$_log->_session_data|@print_r:true}}</pre>
          </div>
        </td>
      </tr>

      <tr>
        <td style="padding: 0; background: none !important;">
          <table class="main tbl">
            <tr>
              <td class="narrow"></td>
              <td>{{mb_value object=$_log field=file_name}}</td>
              <td class="narrow" style="text-align: right;">{{mb_value object=$_log field=line_number}}</td>
            </tr>
            {{foreach from=$_log->_stacktrace_output item=_output}}
              <tr>
                <td>{{$_output.function}}</td>
                <td>{{$_output.file}}</td>
                <td style="text-align: right;">{{$_output.line}}</td>
              </tr>
            {{/foreach}}
          </table>
        </td>
      </tr>
    </tbody>
  {{foreachelse}}
    <tr>
      <td class="empty" colspan="5">{{tr}}CErrorLog.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>

{{mb_include module=system template=inc_pagination change_page="changePage" total=$total current=$start step=30}}
