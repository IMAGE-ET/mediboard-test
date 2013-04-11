{{*
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage dPcabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 *}}

<script type="text/javascript">
  Main.add(function() {
    ViewPort.SetAvlHeight("planning-plages", 1);
    $('planningWeek').setStyle({height : "2000px"});
    Calendar.regField(getForm("changeDate").debut, null, {noView: true});
    window.save_dates = {
      prev: '{{$prev}}',
      next: '{{$next}}'
    }
    $("debut_periode").update("{{$debut|date_format:"%A %d %b %Y"}}");
    $("fin_periode").update("{{$fin|date_format:"%A %d %b %Y"}}");
    var button_desistement = $("desistement_count");
    button_desistement.writeAttribute("disabled" {{if $count_si_desistement}}, null{{/if}});
    button_desistement.down("span").update("({{$count_si_desistement}})");
    
    $$(".body").each(function(elt) {
      elt.setStyle({backgroundColor: elt.up().getStyle("backgroundColor")});
    });
  });
</script>

{{assign var=chir_id value=$chirSel}}

{{mb_include module=system template=calendars/vw_week}}

<script type="text/javascript">
  Main.add(function() {
    var planning = window['planning-{{$planning->guid}}'];
    
    planning.onMenuClick = function(action, plageconsult_id, elt) {
      window.action_in_progress = true;
      var consultation_id = elt.get("consultation_id");
      
      if (window.save_elt && window.save_elt != elt) {
        window.save_elt.removeClassName("opacity-50");
      }
      
      window.cut_consult_id = null;
      window.copy_consult_id = null;
        
      if (elt.hasClassName("opacity-50")) {
        elt.removeClassName("opacity-50");
        window.save_elt = null;
      }
      else {
        elt.addClassName("opacity-50");
        if (action == "cut") {
          window.cut_consult_id = consultation_id;
        }
        else {
          window.copy_consult_id = consultation_id;
        }
        window.save_elt = elt;
      }
      
      updateStatusCut();
    }
    
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
    }
    
    $$(".droppable").each(function(elt) {
      Droppables.add(elt, {
      onDrop: function(from, to) {
        window.save_to = to;
      }});
    });
  });
</script>
