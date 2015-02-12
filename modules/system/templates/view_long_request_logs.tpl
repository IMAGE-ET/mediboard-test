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

<script type="text/javascript">
  Main.add(function() {
    var form = getForm("Filter-Log");
    form.elements.duration.addSpinner({min: 10});
    LongRequestLog.refresh();
  });

  function changePage(start) {
    var form = getForm("Filter-Log");
    $V(form.elements.start, start);
    form.onsubmit();
  }
</script>

<form name="Filter-Log" method="get" onsubmit="return LongRequestLog.refresh();">
  <input type="hidden" name="a" value="ajax_list_long_request_logs" />
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="start" value="0" />

  <table class="main form">
    <tr>
      <th class="narrow">{{mb_label object=$filter field=user_id}}</th>
      <td>
        <select name="user_id" class="ref" onchange="$V(this.form.elements.start, 0)">
          <option value="">&mdash; {{tr}}CUser|all{{/tr}}</option>
          {{foreach from=$user_list item=_user}}
            <option value="{{$_user->_id}}" {{if $_user->_id == $filter->user_id}}selected="selected"{{/if}} >
              {{$_user}}
            </option>
          {{/foreach}}
        </select>

        <label>
          {{mb_label object=$filter field=duration}}

          <select name="duration_operand">
            <option value=">="> >= </option>
            <option value=">">  >  </option>
            <option value="<="> <= </option>
            <option value="<">  <  </option>
            <option value="=">  =  </option>
          </select>

          <input type="text" name="duration" size="3">
        </label>
      </td>
    </tr>

    <tr>
      <th>{{tr}}common-noun-Date{{/tr}}</th>
      <td>
        {{mb_field object=$filter field=_date_min form="Filter-Log" register=true}}
        &raquo;
        {{mb_field object=$filter field=_date_max form="Filter-Log" register=true}}

        <button type="submit" class="search">{{tr}}Search{{/tr}}</button>
        <button type="button" class="lookup" onclick="LongRequestLog.showPurge(this.form);">
          {{tr}}common-action-Purge{{/tr}}
        </button>
      </td>
    </tr>
  </table>
</form>

<div id="list-logs"></div>
