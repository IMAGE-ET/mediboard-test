{{*
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage dPcabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 *}}

{{mb_script module=cabinet script=plage_consultation}}
{{mb_script module=ssr script=planning}}
{{assign var=chir_id value=$chirSel}}

<script type="text/javascript">
  Main.add(function() {
    ViewPort.SetAvlHeight("planning-plages", 1);
    $('planningWeek').setStyle({height : "2000px"});
    Calendar.regField(getForm("changeDate").debut, null, {noView: true});
  });
  
  function printPlanning() {
    var url = new Url("cabinet", "print_planning");
    url.addParam("date", "{{$debut}}");
    url.addParam("chir_id", "{{$chirSel}}");
    url.popup(900, 600, "Planning");
  }
  
  function showConsultSiDesistement(){
    var url = new Url("cabinet", "vw_list_consult_si_desistement");
    url.addParam("chir_id", '{{$chirSel}}');
    url.pop(500, 500, "test");
  }
</script>

<form action="?" name="changeDate" method="get">
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="tab" value="{{$tab}}" />
  <input type="hidden" name="plageconsult_id" value="0" />
  <table class="main">
    <tr>
      <th style="width: 25%; text-align: left;">
        <button type="button" style="float: left;" class="new" onclick="PlageConsultation.edit('0');">Créer une nouvelle plage</button>
        
        <select name="chirSel" style="width: 15em;" onchange="this.form.submit()">
          <option value="-1" {{if $chirSel == -1}} selected="selected" {{/if}}>&mdash; Choisir un professionnel</option>
          {{foreach from=$listChirs item=curr_chir}}
          <option class="mediuser" style="border-color: #{{$curr_chir->_ref_function->color}};" value="{{$curr_chir->user_id}}" {{if $chirSel == $curr_chir->user_id}} selected="selected" {{/if}}>
            {{$curr_chir->_view}}
          </option>
          {{/foreach}}
        </select>
      </th>
      <th style="width: 50%">
        <a href="#1" onclick="$V($(this).getSurroundingForm().debut, '{{$prec}}')">&lt;&lt;&lt;</a>
        
        Semaine du {{$debut|date_format:"%A %d %b %Y"}} au {{$fin|date_format:"%A %d %b %Y"}}
        <input type="hidden" name="debut" class="date" value="{{$debut}}" onchange="this.form.submit()" />
        
        <a href="#1" onclick="$V($(this).getSurroundingForm().debut, '{{$suiv}}')">&gt;&gt;&gt;</a>
        <br />
        <a href="#1" onclick="$V($(this).getSurroundingForm().debut, '{{$today}}')">Aujourd'hui</a>
      </th>
      <th style="width: 25%; text-align: right;">
        <button type="button" class="print" onclick="printPlanning();">{{tr}}Print{{/tr}}</button>
        <br />
        {{if $chirSel && $chirSel != -1}}
          <button type="button" class="lookup" 
                  {{if !$count_si_desistement}}disabled="disabled"{{/if}}
                  onclick="showConsultSiDesistement()">
            {{tr}}CConsultation-si_desistement{{/tr}} ({{$count_si_desistement}})
          </button>
        {{/if}}
      </th>
    </tr>
  </table>
</form>

<div id="planning-plages">
  {{mb_include module=ssr template=inc_vw_week}}
</div>

<form name="editConsult" method="post">
  <input type="hidden" name="m" value="cabinet" />
  <input type="hidden" name="dosql" value="do_consultation_aed" />
  <input type="hidden" name="consultation_id" />
  <input type="hidden" name="plageconsult_id" />
  <input type="hidden" name="heure" />
</form>

<script type="text/javascript">

Main.add(function() {
  var planning = window['planning-{{$planning->guid}}'];
  
  planning.onEventChange = function(e) {
    window.is_dropping = true;
    if (!window.save_to) {
      document.location.reload();
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
    onSubmitFormAjax(form, {onComplete: function() { document.location.reload()} });
    window.save_to = null;
  }
  
  $$(".droppable").each(function(elt) {
    Droppables.add(elt, {
    onDrop: function(from, to) {
      window.save_to = to;
    }});
  });
});

setClose = function(heure, plage_id, date, chir_id, consult_id) {
  if (window.is_dropping) {
    window.is_dropping = false;
    return;
  }
  if (consult_id) {
    modalPriseRDV(consult_id);
  }
  else {
    modalPriseRDV(0, Date.fromLocaleDate(date.split(" ")[1]).toDATE(), heure, plage_id);
  }
}

modalPriseRDV = function(consult_id, date, heure, plage_id) {
  var url = new Url("dPcabinet", "edit_planning");
  
  url.addParam("dialog", 1);
  url.addParam("consultation_id", consult_id);
  
  url.addParam("date_planning", date);
  url.addParam("heure", heure);
  url.addParam("plageconsult_id", plage_id);
  
  url.modal({width: 950, height: 700});
}
</script>