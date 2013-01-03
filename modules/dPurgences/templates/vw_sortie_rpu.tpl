{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module=urgences   script=main_courante}}
{{mb_script module=planningOp script=cim10_selector}}

{{mb_script module=ecap script=dhe}}

<script type="text/javascript">
var refreshExecuter;

Main.add(function () {
	Missing.refresh();
  
  refreshExecuter = new PeriodicalExecuter(function(){
    getForm("changeDate").submit();
  }, 60);
});

function validCotation(consutation_id) {
  return onSubmitFormAjax(getForm('validCotation-'+consutation_id));
}

Sortie = {
  modal: null,
	refresh: function(rpu_id) {
	  var url = new Url("dPurgences", "ajax_refresh_sortie");
	  url.addParam("rpu_id", rpu_id);
	  url.requestUpdate('CRPU-'+rpu_id, {onComplete: function(){refreshExecuter.resume()}});
	},
	
	edit: function(rpu_id) {
	  refreshExecuter.stop();
	  var url = new Url("dPurgences", "ajax_edit_sortie")
    url.addParam("rpu_id", rpu_id);
		url.requestModal(500, 300);
    this.modal = url.modalObject;
	},
	
	close: function() {
    refreshExecuter.resume();
	  if (this.modal) {
		  this.modal.close();
			this.modal = null;
		}
	}
}

function filterPatient(input, indicator) {
  $$("#list-sorties tr").invoke("show");
  indicator = $(indicator);
  
  var term = $V(input);
  if (!term) return;
  
  if (indicator) {
    indicator.show();
  }
  
  $$("#list-sorties .CPatient-view").each(function(p) {
    if (!p.innerHTML.like(term)) {
      p.up("tr").hide();
    }
  });
}

function editFieldsRpu(rpu_id) {
  refreshExecuter.stop();
  var url = new Url("dPurgences", "ajax_edit_fields_rpu");
  url.addParam("rpu_id", rpu_id);
  url.requestModal(500, 240);
  url.modalObject.observe("afterClose", function(){
    refreshExecuter.resume();
    Sortie.refresh(rpu_id);  
  });
}

function submitSejour() {
  return onSubmitFormAjax(getForm("editSejour"));
}
</script>

<table class="main">
  <tr>
    <td style="text-align: left;">
      {{mb_include template=inc_hide_previous_rpus}}
    </td>
		
    <th style="text-align: center;">
      <big>{{$date|date_format:$conf.longdate}}</big>
      <form action="?" name="changeDate" method="get">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="tab" value="{{$tab}}" />
        <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
      </form>
      <script type="text/javascript">
          Main.add(Calendar.regField.curry(getForm("changeDate").date, null, {noView: true}));
      </script>
    </th>

    <td style="text-align: right;">
      Type d'affichage
      <form name="selView" action="?m=dPurgences&amp;tab=vw_sortie_rpu" method="post">
        <select name="view_sortie" onchange="this.form.submit()">
          <option value="tous"      {{if $view_sortie == "tous"     }} selected="selected" {{/if}}>Tous</option>
          <option value="sortie"    {{if $view_sortie == "sortie"   }} selected="selected" {{/if}}>Sorties à effectuer</option>
          <option value="normal"    {{if $view_sortie == "normal"   }} selected="selected" {{/if}}>Sorties normales</option>
          <option value="mutation"  {{if $view_sortie == "mutation" }} selected="selected" {{/if}}>Sorties en mutation</option>
          <option value="transfert" {{if $view_sortie == "transfert"}} selected="selected" {{/if}}>Sorties en transfert</option>
          <option value="deces"     {{if $view_sortie == "deces"    }} selected="selected" {{/if}}>Sorties en décès</option>
        </select>
      </form>
      <button class="print" onclick="MainCourante.printSortie('{{$date}}','{{$view_sortie}}')">Sortie des patients</button>
    </td>
  </tr>
</table>

<div class="small-info" style="display: none;" id="filter-indicator">
  <strong>Résultats filtrés</strong>.
  <br />
  Les résultats sont filtrés et le rafraîchissement est désactivé. 
  <button class="change" onclick="getForm('changeDate').submit()">Relancer</button>
</div>

<table class="tbl" id="list-sorties">
  <tr>
    <th>{{mb_title class=CRPU field="_patient_id"}}</th>
    <th class="narrow"><input type="text" onkeyup="filterPatient(this, 'filter-indicator')" id="filter-patient-name" size="6" /></th>
    {{if $conf.dPurgences.responsable_rpu_view}}
      <th>{{mb_title class=CRPU field="_responsable_id"}}</th>
    {{/if}}
    <th>Prise en charge</th>
    <th>{{mb_title class=CRPU field="rpu_id"}}</th>
    <th>
      {{mb_title class=CSejour field=_entree}} /
      {{mb_title class=CSejour field=_sortie}}
		</th>
    {{if $conf.dPurgences.check_can_leave}}
    <th>{{mb_title class=CRPU field="_can_leave"}}</th>
		{{/if}}
  </tr>
  {{foreach from=$listSejours item=sejour}}
    {{assign var=rpu value=$sejour->_ref_rpu}}
    {{assign var=patient value=$sejour->_ref_patient}}
    <tr id="{{$rpu->_guid}}" {{if !$sejour->sortie_reelle && $sejour->_veille}} class="veille" {{/if}}>
      {{mb_include module=urgences template=inc_sortie_rpu}}
    </tr>
  {{foreachelse}}
	  <tr><td colspan="{{$conf.dPurgences.responsable_rpu_view|ternary:7:6}}" class="empty">Aucune sortie à effectuer</td></tr>
  {{/foreach}}
</table>