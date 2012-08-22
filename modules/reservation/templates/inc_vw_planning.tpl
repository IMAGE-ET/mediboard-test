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
      $('planningWeek').style.height = "1500px";
  });
</script>

<style type="text/css">
  .planning .event .body{
    color: #000 !important;
    line-height: 120% !important;
  }
</style>

<div id="planningInterventions">
  {{mb_include module=ssr template=inc_vw_week}}
</div>

<script type="text/javascript">
  Main.add(function() {
    var planning = window["planning-{{$planning->guid}}"];
    planning.salles_ids = {{$salles_ids|@json}};
    
    planning.onMenuClick = function(event, operation_id, elem) {
      switch (event) {
        case 'edit':
          modifIntervention('', '', '', operation_id);
          break;
        case 'cut':
          cutIntervention(operation_id, elem);
          break;
        case 'clock':
          modifSejour(operation_id, null, "Control.Modal.close");
      }
    }
    
    planning.onEventChange = function(e) {
      var operation_guid = e.draggable_guid;
      var operation_id = operation_guid.split("-")[1];
      
      var entree_prevue = /entree_prevue='([0-9 \:-]*)'/.exec(e.title)[1];
      var prevue_split = entree_prevue.split(" ");
      var date_entree_prevue = prevue_split[0];
      var heure_entree_prevue = prevue_split[1];
      var sortie_prevue = /sortie_prevue='([0-9 \:-]*)'/.exec(e.title)[1];
      var heure_sortie_prevue = sortie_prevue.split(" ")[1];
      var time = e.getTime();
      var temp_operation = new Date(1970, 1, 1, 0, time.length).toTIME();
      var time_operation = time.start.toTIME();
      var index_salle = time.start.getFullYear()-2000;
      var salle_id = this.salles_ids[index_salle];
      
      if (index_salle < 0 || index_salle > this.salles_ids.length) {
        return;
      }
      
      var form = getForm("editOperation");
      
      // Popup de modification des dates d'entrée et sortie prévue du séjour
      // dans le cas où la date et heure d'intervention n'est pas dans cet intervalle
      
      if ("{{$date_planning}} "+time_operation < entree_prevue) {
        modifSejour(operation_id, "{{$date_planning}} "+time_operation, "afterModifSejour");
        
        window.save_operation =
          {"operation_id": operation_id,
           "time_operation": time_operation,
           "temp_operation": temp_operation,
           "salle_id": salle_id};
        return;
      }
      
      // Sinon, on peut enregistrer
      $V(form.operation_id,   operation_id);
      $V(form.time_operation, time_operation);
      $V(form.temp_operation, temp_operation);
      $V(form.salle_id,       salle_id);
      
      onSubmitFormAjax(form, {onComplete: refreshPlanning});
    }
    
    var planning_div = $("{{$planning->guid}}");
    
    
    {{if $can->edit}}
      // Création d'une interv sur une case à une heure donnée
      planning_div.select("td").each(function(elt) {
        elt.observe('dblclick', function() {
          var classes = elt.className.split("  ");
          var hour = classes[0].split("-")[2];
          var salle_id = planning.salles_ids[classes[0].split("-")[1]];
          
          if (window.cut_operation_id) {
            pasteIntervention(window.cut_operation_id, salle_id, hour);
            return;
          }
                      
          modifIntervention("{{$date_planning}}", hour, salle_id);
        });
      });
    {{/if}}
  });
  </script>