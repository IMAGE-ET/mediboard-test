{{*
  * @category
  * @package  Mediboard
  * @author   SARL OpenXtrem <dev@openxtrem.com>
  * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
  * @version  SVN: $Id:$ 
  * @link     http://www.mediboard.org
*}}

{{mb_script module=system script=Event_planning ajax=true}}

<script>
  Main.add(function() {

    var planning = new EventPlanning(
      '{{$planning->guid}}',
      '{{$planning->hour_min}}',
      '{{$planning->hour_max}}'
    );

    window["planning-{{$planning->guid}}"] = planning;
  });
</script>

<style>
  table#calendar_horizontal-{{$planning->guid}},table#calendar_horizontal-{{$planning->guid}} td.division, .today_astreinte_indicator  {
    height: {{math equation="(x*(y+20))" x=$planning->max_height_event y=85}}px;
  }

  .calendar_horizontal td.column{
    width:{{math equation="100 / y" y=$planning->days|@count}}%;
  }
</style>

<table class="calendar_horizontal main" id="calendar_horizontal-{{$planning->guid}}">
  {{assign var=nb_days value=$planning->days|@count}}
  <tr id="calendar_days">
    <!-- days -->
    {{foreach from=$planning->days key=_name item=_day}}
      <td class="column{{if !$planning->_hours|@count}} hoveringTd{{/if}}{{if in_array($_name|date_format:"%u", $planning->weekend_days)}} weekEnd{{/if}}" data-date="{{$_name}}">
        <table class="divisions">
          <tr>
            <th colspan="{{$planning->_hours|@count}}" class="dayLabel {{if array_key_exists($_name, $planning->_ref_holidays)}}nonworking{{/if}}">
              {{mb_include module=system template="calendars/inc_hori/th_day"}}
            </th>
          </tr>
          {{if $planning->_hours|@count}}
            <tr>
              <!-- hours division -->
              {{foreach from=$planning->_hours key=num item=_hour}}
              <td class="division hoveringTd" data-hour="{{$_hour}}" data-date="{{$_name}}" style="
                width:{{math equation="100/b" b=$planning->_hours|@count}}%;"
                >
                <span class="hourLabel">{{$_hour}}h</span>
              </td>
            {{/foreach}}
            </tr>
          {{/if}}

          <div class="day">
            <!-- events -->
            {{foreach from=$_day item=_event}}
              <div class="event {{$_event->css_class}} {{$_event->type}}" data-guid="{{$_event->guid}}" style="
                top:{{math equation="(80*a)+40" a=$_event->height}}px;
                left:{{math equation="(((a*60)+b) * (100/1440))" a=$_event->hour b=$_event->minutes}}%;
                width:{{math equation="(a * (100/1440))" a=$_event->length}}%;
                min-width:{{math equation="(a * (100/1440))" a=$_event->length}}%!important;
                background-color:{{$_event->color}};
                ">
                {{if $_event->menu|@count > 0}}
                  <div class="toolbar" style="background:{{$_event->color}} ">
                    {{foreach from=$_event->menu item=element}}
                      <a class="button {{$element.class}} notext"
                         onclick="window['planning-{{$planning->guid}}'].onMenuClick('{{$element.class}}','{{$_event->mb_object.id}}', this)"
                         title="{{$element.title}}"></a>
                    {{/foreach}}
                  </div>
                {{/if}}

                {{if $_event->display_hours}}
                  <span class="startTime incline" style="background:{{$_event->color}}; {{if $_event->start|date_format:"%H:%M" == "00:00"}}left:-10px;{{/if}}">{{$_event->start|date_format:"%H:%M"}}</span>
                {{/if}}

                <span class="event_libelle" {{if $_event->mb_object.guid != ""}}onmouseover="ObjectTooltip.createEx(this,'{{$_event->mb_object.guid}}')"{{/if}}>
                  {{if $_event->title}}
                    {{$_event->title|smarty:nodefaults}}
                  {{/if}}
                </span>

                {{if $_event->display_hours}}
                  <span class="endTime incline" style="background:{{$_event->color}};
                  {{if $_event->end|date_format:"%H%M" >= "2350"}}right:-10px;{{/if}}">{{$_event->end|date_format:"%H:%M"}}</span>
                {{/if}}
              </div>
            {{/foreach}}
          </div>

        </table>
      </td>
    {{/foreach}}
  </tr>
</table>
