{{*
  * @category
  * @package  Mediboard
  * @author   SARL OpenXtrem <dev@openxtrem.com>
  * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
  * @version  SVN: $Id:$ 
  * @link     http://www.mediboard.org
*}}


<script>
  Main.add(function() {
    var planning = new EventPlanning(
      '{{$planning->guid}}',
      '{{$planning->hour_min}}',
      '{{$planning->hour_max}}',
      {{* {{$planning->events|@json}}, *}}
      {{$planning->ranges|@json}},
      {{$planning->hour_divider}},
      window["planning-{{$planning->guid}}"] && window["planning-{{$planning->guid}}"].scrollTop,
      {{$planning->adapt_range|@json}},
      '{{$planning->selectable}}',
      {{$planning->dragndrop}},
      {{$planning->resizable}},
      {{$planning->no_dates}}
    );

    window["planning-{{$planning->guid}}"] = planning;
  });
</script>

<style>
  table#planning_horizontal,table#planning_horizontal td.division, .today_astreinte_indicator  {
  {{if $planning->max_height_event}}
    height: {{math equation="(x*(y+20))" x=$planning->max_height_event y=85}}px;
  {{else}}
    height: 100px;
  {{/if}}
  }

  td.column{
    width:{{math equation="100 / y" y=$planning->days|@count}}%;
  }
</style>

<table id="planning_horizontal" class="calendar_horizontal">
  {{assign var=nb_days value=$planning->days|@count}}
  <tr id="calendar_days">
    <!-- days -->
    {{foreach from=$planning->days key=_name item=_day}}
      <td class="column{{if !$planning->_hours|@count}} hoveringTd{{/if}}{{if in_array($_name|date_format:"%u", $planning->weekend_days)}} weekEnd{{/if}}">
        <table class="divisions">
          <tr>
            <th colspan="{{$planning->_hours|@count}}" class="dayLabel day-{{$_name}} {{if array_key_exists($_name, $planning->_ref_holidays)}}nonworking{{/if}}">
              {{mb_include module=system template="calendars/inc_hori/th_day"}}
            </th>
          </tr>
          {{if $planning->_hours|@count}}
          <tr>
            <!-- hours division -->
            {{foreach from=$planning->_hours key=num item=_hour}}
            <td class="division hoveringTd" style="
              width:{{math equation="100/b" b=$planning->_hours|@count}}%;
              left:{{math equation="a*100/b" a=$num b=$planning->_hours|@count}}%;">
              <span class="hourLabel">{{$_hour}}h</span>
            </td>
          {{/foreach}}
          </tr>
          {{/if}}
        </table>

        <div class="day">
          <!-- events -->
          {{foreach from=$_day item=_event}}
            <div class="event {{$_event->css_class}} {{$_event->guid}} {{$_event->type}}" style="
              top:{{math equation="(80*a)+15" a=$_event->height}}px;
              left:{{math equation="(((a*60)+b) * (100/1440))" a=$_event->hour b=$_event->minutes}}%;
              width:{{math equation="(a * (100/1440))" a=$_event->length}}%;
              min-width:{{math equation="(a * (100/1440))" a=$_event->length}}%!important;
              background:{{$_event->color}};
            ">
              {{if $_event->menu|@count > 0}}
                <div class="toolbar" style="background:{{$_event->color}} ">
                  {{foreach from=$_event->menu item=element}}
                    <a class="button {{$element.class}} notext"
                       onclick="window['planning-{{$planning->guid}}'].onMenuClick('{{$element.class}}','{{$_event->guid}}', this)"
                       title="{{$element.title}}"></a>
                  {{/foreach}}
                </div>
              {{/if}}

              {{if $_event->display_hours}}
                <span class="startTime incline" style="background:{{$_event->color}}; {{if $_event->start|date_format:"%H:%M" == "00:00"}}left:-10px;{{/if}}">{{$_event->start|date_format:"%H:%M"}}</span>
              {{/if}}

              <span class="event_libelle" onmouseover="ObjectTooltip.createEx(this,'{{$_event->_ref_object->_guid}}')">{{$_event->title|smarty:nodefaults}}</span>

              {{if $_event->display_hours}}
                <span class="endTime incline" style="background:{{$_event->color}};
                 {{if $_event->end|date_format:"%H%M" >= "2350"}}right:-10px;{{/if}}">{{$_event->end|date_format:"%H:%M"}}</span>
              {{/if}}
            </div>
          {{/foreach}}
        </div>
      </td>
    {{/foreach}}
  </tr>
</table>
