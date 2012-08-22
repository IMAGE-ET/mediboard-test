{{*
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage reservation
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 *}}
 
<script type="text/javascript">
  Main.add(function() {
    var form = getForm("filterPlanning");
    window.calendar_planning = Calendar.regField(form.date_planning);
    refreshPlanning();
  });
  
  refreshPlanning = function() {
    var form = getForm("filterPlanning");
    var url = new Url("reservation", "ajax_vw_planning");
    url.addParam("date_planning", $V(form.date_planning));
    url.addParam("praticien_id" , $V(form.praticien_id));
    url.addParam("bloc_id"      , $V(form.bloc_id));
    
    var week_container = $$(".week-container")[0];
    
    if (week_container) {
      url.addParam("scroll_top", week_container.scrollTop);
    }
    url.requestUpdate("planning");
  }
  
  modifIntervention = function(date, hour, salle_id, operation_id) {
    var url = new Url("dPplanningOp", "vw_edit_urgence");
    
    url.addParam("operation_id", operation_id);
    url.addParam("date_urgence", date);
    url.addParam("hour_urgence", hour);
    url.addParam("salle_id"    , salle_id);
    url.addParam("min_urgence" , "00");
    url.addParam("dialog", 1);
    url.modal({width: 1000, height: 700});
    url.modalObject.observe("afterClose", function() {
      refreshPlanning();
    });
  }
  
  cutIntervention = function(operation_id, elt) {
    if ((window.cut_operation_id && window.cut_operation_id != operation_id)) {
      return;
    }
    
    if (elt.hasClassName("opacity-50")) {
      elt.removeClassName("opacity-50");
      window.cut_operation_id = null;
    }
    else {
      elt.addClassName("opacity-50");
      window.cut_operation_id = operation_id;
      var span_infos = elt.up('div.toolbar').next('div.body').down('span').down('span');
      window.save_entree_prevue = span_infos.get("entree_prevue");
      window.save_sortie_prevue = span_infos.get("sortie_prevue");
    }
    updateStatusCut();
  }
  
  pasteIntervention = function(operation_id, salle_id, heure) {
    var heure = heure+":00:00"
    var date = window.calendar_planning.altElement.defaultValue;
    var datetime_interv = date+ " " + heure;
    
    if (datetime_interv < window.save_entree_prevue || datetime_interv > window.save_sortie_prevue) {
      window.save_cut_operation =
      { "operation_id": operation_id,
        "date": date,
        "time_operation": heure,
        "salle_id": salle_id
      };
      
      modifSejour(operation_id, datetime_interv, "afterModifSejour");
      return;
    }
    
    var form = getForm("cutOperation");
    
    $V(form.operation_id  , operation_id);
    $V(form.date          , date);
    $V(form.salle_id      , salle_id);
    $V(form.time_operation, heure);
    
    onSubmitFormAjax(form, {onComplete: function() {
      window.cut_operation_id = null;
      updateStatusCut();
      refreshPlanning();
    } });
  }
  
  updateStatusCut = function() {
    var div = $("status_cut");
    if (window.cut_operation_id) {
      div.update("En cours");
      div.setStyle({borderColor: "#080"});
    }
    else {
      div.update();
      div.setStyle({borderColor: "#ddd"});
    }
  }
  
  modifSejour = function(operation_id, date_move, callback) {
    var url = new Url("dPplanningOp", "ajax_edit_dates_sejour");
    url.addParam("operation_id", operation_id);
    url.addParam("date_move", date_move);
    if (callback) {
      url.addParam("callback", callback);
    }
    url.requestModal(300);
    url.modalObject.observe("afterClose", refreshPlanning);
  }
  
  afterModifSejour = function() {
    // Après un drag and drop
    if (window.save_operation) {
      var form = getForm("editOperation");
      $V(form.operation_id  , window.save_operation.operation_id);
      $V(form.time_operation, window.save_operation.time_operation);
      $V(form.temp_operation, window.save_operation.temp_operation);
      $V(form.salle_id      , window.save_operation.salle_id);
      
      onSubmitFormAjax(form, {onComplete: function() {
        getForm("editSejour").onsubmit = "";
        window.cut_operation_id = null;
        updateStatusCut();
        onSubmitFormAjax(getForm("editSejour"), {onComplete: Control.Modal.close});
        }
      });
      window_save_operation = null;
      return;
    }
    // Après un couper coller
    if (window.save_cut_operation) {
      var form = getForm("cutOperation");
      $V(form.operation_id, window.save_cut_operation.operation_id);
      $V(form.date, window.save_cut_operation.date);
      $V(form.time_operation, window.save_cut_operation.time_operation);
      $V(form.salle_id, window.save_cut_operation.salle_id);
      onSubmitFormAjax(form, {onComplete: function() {
        getForm("editSejour").onsubmit = "";
        window.cut_operation_id = null;
        updateStatusCut();
        onSubmitFormAjax(getForm("editSejour"), {onComplete: Control.Modal.close});
        }
      });
      window.save_cut_operation = null;
    }
  }
</script>

<form name="editOperation" method="post">
  <input type="hidden" name="m" value="dPplanningOp" />
  <input type="hidden" name="dosql" value="do_planning_aed" />
  <input type="hidden" name="operation_id" />
  <input type="hidden" name="time_operation" />
  <input type="hidden" name="temp_operation" />
  <input type="hidden" name="salle_id" />
</form>

<form name="cutOperation" method="post">
  <input type="hidden" name="m" value="dPplanningOp" />
  <input type="hidden" name="dosql" value="do_cut_operation" />
  <input type="hidden" name="operation_id" />
  <input type="hidden" name="date" />
  <input type="hidden" name="time_operation" />
  <input type="hidden" name="salle_id" />
</form>

<form name="filterPlanning" method="get"> 
  <table class="form">
    <tr>
      <th class="category" colspan="4">
        Filtre
      </th>
    </tr>
    <tr>
      <td>
        <a href="#1" onclick="window.calendar_planning.datePicked(new Date(new Date(window.calendar_planning.altElement.defaultValue).setHours('-24')))">&lt;&lt;&lt;</a>
        <label>
        Date <input name="date_planning" type="hidden" value="{{$date_planning}}" class="date" onchange="refreshPlanning();"/>
        </label>
        <a href="#1" onclick="window.calendar_planning.datePicked(new Date(new Date(window.calendar_planning.altElement.defaultValue).setHours('+24')))">&gt;&gt;&gt;</a>
      </td>
      <td>
        <label>
          Praticien
          <select name="praticien_id" onchange="refreshPlanning();">
            <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
            {{mb_include module=mediusers template=inc_options_mediuser list=$praticiens selected=$praticien_id}}
          </select>
        </label>
      </td>
      <td>
        <label>
          {{tr}}CBlocOperatoire{{/tr}}
          <select name="bloc_id" onchange="refreshPlanning();">
            <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
            {{foreach from=$blocs item=_bloc}}
              <option value="{{$_bloc->_id}}" {{if $bloc_id == $_bloc->_id}}selected{{/if}}>{{$_bloc->nom}}</option>
            {{/foreach}}
          </select>
        </label>
      </td>
      <td class="narrow">
        <div id="status_cut" style="width: 100px; height: 14px; border: 2px dashed #ddd; font-weight: bold; text-align: center;">
        </div>
      </td>
    </tr>
  </table>
</form>

<div id="planning"></div>
