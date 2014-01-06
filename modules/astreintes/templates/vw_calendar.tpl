{{*
  * View of the current day
  *  
  * @category Astreintes
  * @package  Mediboard
  * @author   SARL OpenXtrem <dev@openxtrem.com>
  * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
  * @version  SVN: $Id:$ 
  * @link     http://www.mediboard.org
*}}

{{mb_script module="astreintes" script="plage"}}
{{mb_script module=ssr script=planning}}


<button style="float:left;" onclick="PlageAstreinte.modal()" class="new">{{tr}}Create{{/tr}} {{tr}}CPlageAstreinte{{/tr}}</button>
<!-- Selector -->
{{mb_include template="inc_select_mode_cal"}}


<script>
  Main.add(function() {
    var height_planning = '{{$height_planning_astreinte}}';
    ViewPort.SetAvlHeight("planningAstreinte", 1.0);
  });
</script>

<!-- Calendar -->
<div id="planningAstreinte">
  {{mb_include module=system template="calendars/planning_horizontal"}}
  {{* {{mb_include module=system template="calendars/vw_week"}} *}}
</div>

<!-- clic menu -->
<script>
  Main.add(function() {
    var planning_object = window["planning-{{$planning->guid}}"];

    planning_object.onMenuClick = function(event, object_id, elt) {
      switch(event) {
        case 'edit':
          PlageAstreinte.modal(object_id);
        break;
      }
    };

    {{if $can->edit}}
    // Création d'une interv sur une case à une heure donnée
    $$(".hoveringTd").each(function(elt) {

      elt.observe('dblclick', function() {
        var date = elt.get("date");
        var hour = elt.get("hour");
        var minutes = elt.get("minutes");

        PlageAstreinte.modal(null, date, (hour) ? hour : "00", (minutes) ? minutes : "00");
        // - Création
      });
    });
    {{/if}}
  });
</script>