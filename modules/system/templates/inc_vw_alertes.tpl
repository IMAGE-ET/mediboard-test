{{*
  * Handle the alerts
  *
  * @category System
  * @package  Mediboard
  * @author   SARL OpenXtrem <dev@openxtrem.com>
  * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
  * @version  SVN: $Id:$
  * @link     http://www.mediboard.org
*}}

{{assign var=alerts value=$object->_refs_alerts_not_handled}}
{{assign var=object_guid value=$object->_guid}}

<table class="tbl">
  <tr>
    <th class="title" colspan="2">
      {{$alerts|@count}} alertes
      {{if $alerts|@count}}
        <form name="closeAlertes-{{$level}}-{{$object_guid}}" method="post"
              onsubmit="return onSubmitFormAjax(this, function() {
                $('tooltip-alerts-{{$level}}-{{$object_guid}}').up().hide();
                Alert.callback();})">
          <input type="hidden" name="m"         value="system" />
          <input type="hidden" name="dosql"     value="do_alert_aed" />
          <input type="hidden" name="alert_ids" value="{{$alert_ids|@join:"-"}}" />
          <input type="hidden" name="handled"   value="1" />
          <button type="submit" class="singleclick tick">
            Traiter toutes les alertes
          </button>
        </form>
      {{/if}}
    </th>
  </tr>
  {{foreach from=$alerts item=_alert}}
  <tr>
    <td class="narrow">
      <form name="editAlert-{{$_alert->_id}}" method="post"
            onsubmit="return onSubmitFormAjax(this, function(){
              $('tooltip-alerts-{{$level}}-{{$object_guid}}').up().hide();
              Alert.callback();})">
        <input type="hidden" name="m" value="system" />
        <input type="hidden" name="dosql" value="do_alert_aed" />
        {{mb_key object=$_alert}}
        <input type="hidden" name="handled" value="1" />
        <button type="submit" class="tick notext">Traiter</button>
      </form>
    </td>
    <td class="text compact">
      {{mb_value object=$_alert field=comments}}
    </td>
  </tr>
  {{/foreach}}
</table>