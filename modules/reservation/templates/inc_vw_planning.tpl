{{*
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage reservation
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 *}}

{{mb_script module=ssr script=planning ajax=1}}

<script type="text/javascript">
  Main.add(function() {
    ViewPort.SetAvlHeight("planningInterventions", 1);
      $('planningWeek').style.height = "4500px";
  });
</script>

<style type="text/css">
  .planning .event {
    color: #000 !important;
  }
</style>

<div id="planningInterventions">
  {{mb_include module=ssr template=inc_vw_week}}
</div>

<script type="text/javascript">
  Main.add(function() {
    var planning = window["planning-{{$planning->guid}}"];
    planning.salles_ids = {{$salles_ids|@json}};
    
    /*planning.setPlanningHeight(2000);
    planning.adaptRangeHeight();
    planning.scroll();*/
    
    planning.onMenuClick = function(event, plage, elem) {
      modifIntervention('', '', '', plage);
    }
    
    planning.onEventChange = function(e) {
      var time = e.getTime();
      var index_salle = time.start.getFullYear()-2000;
      
      if (index_salle < 0 || index_salle > this.salles_ids.length) {
        return;
      }
      
      var form = getForm("editOperation");
      
      
      var time_operation = time.start.toTIME();
      
      $V(form.operation_id, e.draggable_guid.split('-')[1]);
      $V(form.time_operation, time_operation);
      $V(form.temp_operation, new Date(1970, 1, 1, 0, time.length).toTIME());
      
      $V(form.salle_id, this.salles_ids[index_salle]);
      
      onSubmitFormAjax(form, {onComplete: refreshPlanning});
    }
    
    var planning_div = $("{{$planning->guid}}");
    
    
    {{if $can->admin || ($can->edit && $today_tomorrow)}}
      // Cr�ation d'une interv sur une case � une heure donn�e
      planning_div.select("td").each(function(elt) {
        elt.observe('dblclick', function() {
          var classes = elt.className.split("  ");
          var hour = classes[0].split("-")[2];
          
          var salle_id = planning.salles_ids[classes[0].split("-")[1]];
          modifIntervention("{{$date_planning}}", hour, salle_id);
        });
      });
    {{/if}}
  });
  </script>