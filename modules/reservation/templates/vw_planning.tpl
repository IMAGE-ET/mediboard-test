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
    Calendar.regField(form.date_planning);
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
    url.addParam("pat_id"  , "{{$conf.reservation.patient_fictif_id}}");
    url.addParam("dialog", 1);
    url.modal({width: 1000, height: 700});
    url.modalObject.observe("afterClose", function() {
      refreshPlanning();
    });
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

<form name="filterPlanning" method="get"> 
  <table class="form">
    <tr>
      <th class="category" colspan="3">
        Filtre
      </th>
    </tr>
    <tr>
      <td>
        <label>
        Date <input name="date_planning" type="hidden" value="{{$date_planning}}" class="date" onchange="refreshPlanning();"/>
        </label>
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
    </tr>
  </table>
</form>

<div id="planning"></div>
