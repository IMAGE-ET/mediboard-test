{{*
 * $Id$
 *  
 * @category System
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<style>

  .month_calendar {
    border-collapse: collapse;
    height: 100%;
    table-layout: fixed;
  }

  .day_number {
    margin:5px;
    font-size: 1.2em;
  }

  .month_calendar th {
    text-align: center;
    vertical-align: middle!important;
    width: 1%;
  }

  .month_calendar td.month_day {
    vertical-align: top;
    border:solid 1px #b6b6b6;
    width: 12.5%;
    background-color: white;
  }

  .month_calendar td.month_day.ferie {
    background-color: #fffc9b;
  }

  .month_calendar td:hover {
    background-color: #d7eaff;
  }

  td.month_day.disabled {
    background-color: #d9d9d7;
    background-image: url('images/icons/ray.gif');
    color:white;
  }

  td.disabled p.day_number:after {
    content: ' (Congés)';
  }

  .month_calendar td.today {
    border: solid 4px black;
  }

  div.day_events {
    color:black;
  }

  div.CIntervHorsPlage {
    background-color: #ffd699;
    background-size: auto 100%;
    padding:4px;
    border-radius: 3px;
  }

  div.CPlageconsult {
  {{*background: url('modules/dPcabinet/images/icon.png') no-repeat right;*}}
    background-color: rgb(187, 255, 187);
    background-size: auto 100%;
    padding:4px;
    border-radius: 3px;
  }

  div.CPlageOp {
  {{*background: url('modules/dPsalleOp/images/icon.png') no-repeat right;*}}
  background-color: rgb(187, 204, 238);
  background-size: auto 100%;
  padding:4px;
  border-radius: 3px;
}

.event {
  margin:2px;
  box-shadow: 0 0 3px #404040;
  background-color: white;
}

.event:hover {
  box-shadow: 0 0 10px black;
}

.month_calendar td.date_not_in_month {
  color:grey;
  background-image: url('images/icons/ray.gif');
}
</style>

<table class="month_calendar">
    {{* used to count week number && week-end fills *}}
    {{assign var=_week_nb value=1}}
    {{assign var=_week_end_filled value=0}}
    {{foreach from=$calendar->days key=_day item=_events name=loop}}
      {{assign var=day_u value=$_day|date_format:"%u"}}
      {{if ($day_u == 6 || $day_u == 7) && $_events|@count}}
        {{assign var=_week_end_filled value=1}}
      {{/if}}

      {{if $day_u == 7 && !$smarty.foreach.loop.last}}
        {{assign var=_week_nb value=$_week_nb+1}}
      {{/if}}
    {{/foreach}}

  {{if $calendar->title}}
    <tr>
      <th colspan="{{if $_week_end_filled}}8{{else}}6{{/if}}" class="title">
        {{$calendar->title}}
      </th>
    </tr>
  {{/if}}
  <tr>
    <th></th>
    <th>{{tr}}Monday{{/tr}}</th>
    <th>{{tr}}Tuesday{{/tr}}</th>
    <th>{{tr}}Wednesday{{/tr}}</th>
    <th>{{tr}}Thursday{{/tr}}</th>
    <th>{{tr}}Friday{{/tr}}</th>
    {{if $_week_end_filled}}
      <th>{{tr}}Saturday{{/tr}}</th>
      <th>{{tr}}Sunday{{/tr}}</th>
    {{/if}}
  </tr>
  <tr class="week">
    {{* drawing the calendar *}}
    {{assign var=week_nb value=$calendar->first_day_of_first_week|date_format:"%U"}}
    {{foreach from=$calendar->days key=_day item=_events name=loop}}
      {{assign var=day_u value=$_day|date_format:"%u"}}
      {{assign var=oday value=$calendar->year_day_list.$_day}}

      {{* week number *}}
      {{if $_day|date_format:"%u" == 1 || $smarty.foreach.loop.first}}
        <th class="narrow">{{$week_nb+1}}</th>
      {{/if}}
      {{* /week number *}}

      {{if !$_week_end_filled && ($day_u == 6 || $day_u == 7)}}
      {{else}}
        <td class=" month_day
        {{foreach from=$calendar->classes_for_days.$_day item=_class}}{{$_class}} {{/foreach}}
        {{if $_day >= $calendar->date_min && $_day <= $calendar->date_max}}date_in_month{{else}}date_not_in_month{{/if}}
        {{if $calendar->today == $_day}} today{{/if}}
        {{if $oday->ferie}} ferie{{/if}}" style="height:{{math equation="100/a" a=$_week_nb}}%">

            <div class="day_events">
              <p class="day_number">{{$_day|date_format:"%e"}}{{if $oday->ferie}} ({{$calendar->_ref_holidays.$_day}}){{/if}}</p>
              {{foreach from=$_events item=_event}}
                <div id="{{$_event->guid}}" class="{{$_event->css_class}} event" {{if $_event->color}}style="border-left:solid 4px {{$_event->color}}" {{/if}}>
                  {{if $_event->mb_object.guid}}
                    <span onmouseover="ObjectTooltip.createEx(this, '{{$_event->mb_object.guid}}')">
                      {{$_event->title|smarty:nodefaults}} - {{$_event->color}}
                    </span>
                  {{else}}
                    {{$_event->title|smarty:nodefaults}}
                  {{/if}}
                </div>
              {{/foreach}}
            </div>
          </td>
      {{/if}}

      {{if $day_u == 7 && !$smarty.foreach.loop.last}}
        {{assign var=week_nb value=$_day|date_format:"%U"}}
        </tr>
        <tr class="week">
      {{/if}}
    {{/foreach}}
  </tr>
</table>