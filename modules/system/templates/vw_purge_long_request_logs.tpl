{{*
 * $Id$
 *  
 * @category System
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org
 *}}

{{mb_script module=system script=long_request_log}}

<script>
  Main.add(function() {
    var form = getForm("Purge-Log");
    form.elements.duration.addSpinner({min: 10});
    form.elements.purge_limit.addSpinner({min: 1});
  });
</script>

<form name="Purge-Log" method="get" onsubmit="return onSubmitFormAjax(this);">
  <input type="hidden" name="a" value="ajax_purge_long_request_logs" />
  <input type="hidden" name="m" value="{{$m}}" />

  <table class="main form">
    <tr>
      <th class="narrow">{{mb_label object=$log field=user_id}}</th>
      <td>
        <select name="user_id" class="ref">
          <option value="">&mdash; {{tr}}CUser|all{{/tr}}</option>
          {{foreach from=$user_list item=_user}}
            <option value="{{$_user->_id}}" {{if $_user->_id == $log->user_id}}selected="selected"{{/if}} >
              {{$_user}}
            </option>
          {{/foreach}}
        </select>

        <label>
          {{mb_label object=$log field=duration}}

          <select name="duration_operand">
            <option value=">=" {{if $duration_operand|smarty:nodefaults == '>='}}selected="selected"{{/if}}> >= </option>
            <option value=">" {{if $duration_operand|smarty:nodefaults == '>'}}selected="selected"{{/if}}  > >  </option>
            <option value="<=" {{if $duration_operand|smarty:nodefaults == '<='}}selected="selected"{{/if}}> <= </option>
            <option value="<" {{if $duration_operand|smarty:nodefaults == '<'}}selected="selected"{{/if}}  > <  </option>
            <option value="=" {{if $duration_operand|smarty:nodefaults == '='}}selected="selected"{{/if}}  > =  </option>
          </select>

          <input type="text" name="duration" size="3" value="{{$log->duration}}">
        </label>
      </td>

      <td rowspan="3" class="greedyPane" id="resultPurgeLogs"></td>
    </tr>

    <tr>
      <th>{{tr}}common-noun-Date{{/tr}}</th>
      <td>
        {{mb_field object=$log field=_date_min form="Purge-Log" register=true}}
        &raquo;
        {{mb_field object=$log field=_date_max form="Purge-Log" register=true}}
      </td>
    </tr>

    <tr>
      <th>{{tr}}common-Limit at each passage{{/tr}}</th>
      <td>
        <input type="text" name="purge_limit" value="100" size="3" />

        <label>
          {{tr}}common-Auto{{/tr}}
          <input type="checkbox" name="auto" id="clean_auto" />
        </label>

        <button type="button" class="info" onclick="LongRequestLog.purgeSome(this.form, true);">{{tr}}common-action-Count{{/tr}}</button>

        <button type="submit" class="trash" onclick="LongRequestLog.purgeSome(this.form);">
          {{tr}}common-action-Purge{{/tr}}
        </button>
      </td>
    </tr>
  </table>
</form>

<div id="list-logs"></div>
