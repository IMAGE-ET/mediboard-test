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

{{mb_script module=ssr script=planning}}
{{mb_script module=cabinet script=plage_consultation}}

<script>
  assignDate = function(button) {
    button = $(button);
    var date = button.get("date");

    if (date) {
      var form = getForm("filter_day");
      $V(form.date, date, true);
      $V(form.date_da, DateFormat.format(Date.fromDATE(date), "dd/MM/yyyy"), true);
    }
  };

  refreshPlanning = function() {
    var form = getForm("filter_day");
    var week_containers = $$(".week-container");
    if (week_containers.length > 0) {
      $V(form.scroll_top, week_containers[0].scrollTop);
    }
    form.onsubmit();
  };

  // Clic sur une consultation
  setClose = function(heure, plage_id, date, chir_id, consult_id, element) {
    if (window.action_in_progress) {
      window.action_in_progress = false;
      return;
    }

    if (consult_id) {
      modalPriseRDV(consult_id);
    }
    else {
      // ugly method to get time
      var time = "";
      $w(element.className).each(function(elt) {
        if (elt.indexOf(":") != -1) {
          time = elt;
        }
      });
      modalPriseRDV(0, Date.fromLocaleDate(date.split(" ")[1]).toDATE(), time, plage_id);
    }
  };

  modalPriseRDV = function(consult_id, date, heure, plage_id) {
    var url = new Url("dPcabinet", "edit_planning");

    url.addParam("dialog", 1);
    url.addParam("consultation_id", consult_id);

    url.addParam("date_planning", date);
    url.addParam("heure", heure);
    url.addParam("plageconsult_id", plage_id);

    url.modal({
      width: "95%",
      height: "95%"
    });

    url.modalObject.observe("afterClose", refreshPlanning);
  };

  Main.add(function() {
    ViewPort.SetAvlHeight("planning", 1);
    Calendar.regField(getForm("filter_day").date);
    refreshPlanning();
  });
</script>

<form name="chronoPatient" method="post">
  <input type="hidden" name="m" value="dPcabinet"/>
  <input type="hidden" name="dosql" value="do_consultation_aed" />
  <input type="hidden" name="consultation_id" />
  <input type="hidden" name="chrono" />
  <input type="hidden" name="arrivee" />
</form>

<form name="editConsult" method="post">
  <input type="hidden" name="m" value="dPcabinet" />
  <input type="hidden" name="dosql" value="do_consultation_aed" />
  <input type="hidden" name="consultation_id" />
  <input type="hidden" name="plageconsult_id" />
  <input type="hidden" name="heure" />
</form>

<form method="get" name="filter_day" onsubmit="return onSubmitFormAjax(this, {}, 'planning')">
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="a" value="ajax_vw_journee_new" />
  <input type="hidden" name="scroll_top" value="" />

  <fieldset id="jfilters">
    <label>Cabinet
      <select name="function_id" onchange="refreshPlanning();">
        {{foreach from=$functions item=_function}}
          <option value="{{$_function->_id}}" {{if $function_id == $_function->_id}}selected="selected" {{/if}}>{{$_function}}</option>
        {{/foreach}}
      </select>
    </label>

    <button class="lookup" type="button" onclick="Modal.open('filter_more', {showClose: true, onClose:refreshPlanning, title:'Filtres'})">{{tr}}Filter{{/tr}}</button>
    <div id="filter_more" style="display: none;">
      {{mb_include module=cabinet template=inc_filter_new_planning}}
    </div>

    <button type="button" id="previous_day" class="left notext" onclick="assignDate(this);" data-date=""></button>
    <input type="hidden" name="date" value="{{$date}}" onchange="this.form.onsubmit();"/>
    <button type="button" id="next_day" class="right notext" onclick="assignDate(this);" data-date=""></button>

    <button class="change notext"></button>
  </fieldset>
</form>


<div id="planning" style="clear: both">

</div>