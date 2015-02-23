{{*
 * $Id$
 *  
 * @category Cabinet
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<script>
  Main.add(function() {
    ViewPort.SetAvlHeight("planningInterventions", 1);
    $('planningWeek').setStyle({height : "{{$height_calendar}}px"});
  });

  $("previous_day").setAttribute("data-date", '{{$pday}}');
  $("next_day").setAttribute("data-date", '{{$nday}}');
</script>

<div id="planningInterventions">
  {{mb_include module=system template=calendars/vw_week}}
</div>

<script>
  Main.add(function() {
    var planning = window['planning-{{$planning->guid}}'];

    planning.onMenuClick = function(action, plageconsult_id, elt) {
      window.action_in_progress = true;
      var consultation_id = elt.get("consultation_id");

      if (action == "tick" || action == "tick_cancel") {
        var oform = getForm('chronoPatient');
        $V(oform.consultation_id, consultation_id);
        $V(oform.chrono, action == "tick" ? 32 : 16);
        $V(oform.arrivee,  action == "tick" ? new Date().toDATETIME(true) : '');
        onSubmitFormAjax(oform, {onComplete: refreshPlanning });
        // clean up
        $V(oform.consultation_id, "");
        $V(oform.chrono, 0);
        return false;
      }
    };

    planning.onEventChange = function(e) {
      window.action_in_progress = true;
      if (!window.save_to) {
        refreshPlanning();
        return;
      }
      var time = e.getTime();
      var hour = time.start.toTIME();

      var form = getForm("editConsult");
      var consultation_id = e.draggable_guid.split("-")[1];
      var plageconsult_id = window.save_to.get("plageconsult_id");

      $V(form.consultation_id, consultation_id);
      $V(form.plageconsult_id, plageconsult_id);
      $V(form.heure, hour);
      onSubmitFormAjax(form, {onComplete: refreshPlanning });
      window.save_to = null;
    };

    $$(".droppable").each(function(elt) {
      Droppables.add(elt, {
        onDrop: function(from, to) {
          window.save_to = to;
        }});
    });
  });
</script>