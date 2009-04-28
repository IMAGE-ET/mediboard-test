{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
Main.add(function () {
  Calendar.regRedirectPopup("{{$date_suivi}}", "?m={{$m}}&tab={{$tab}}&date_suivi=");
});

function printFeuilleBloc(oper_id) {
  var url = new Url;
  url.setModuleAction("dPsalleOp", "print_feuille_bloc");
  url.addParam("operation_id", oper_id);
  url.popup(700, 600, 'FeuilleBloc');
}

function showLegend() {
  var url = new Url;
  url.setModuleAction("dPbloc", "legende");
  url.popup(500, 150, "Legend");
}

function printSuivi(){
  var url = new Url;
  url.setModuleAction("dPbloc", "print_suivi");
  url.addParam("date", '{{$date_suivi}}');
  url.addParam("vue", document.getElementById('bloc_id').value);
  url.popup(800, 500, "impressionSuivi");
}
</script>

<table class="main">
  <tr>
    <th colspan="100">
      <button type="button" onclick="showLegend()" class="search" style="float: left;">Légende</button>
      <button type="button" onclick="printSuivi()" class="search" style="float: right;">Version imprimable</button>
      <select id="bloc_id" name="bloc_id" onchange="location.href='?m={{$m}}&tab={{$tab}}&date_suivi={{$date_suivi}}&bloc_id='+$V(this)">
      {{foreach from=$listBlocs item=curr_bloc}}
        <option value="{{$curr_bloc->_id}}" {{if $curr_bloc->_id == $bloc->_id}}selected="selected"{{/if}}>
          {{$curr_bloc->nom}}
        </option>
      {{/foreach}}
      </select>
      {{$date_suivi|date_format:$dPconfig.longdate}}
      <img id="changeDate" src="./images/icons/calendar.gif" title="Choisir la date" alt="calendar" />
    </th>
  </tr>
  <tr>
    {{foreach from=$bloc->_ref_salles item=_salle}}
    <td>
      <table class="form">
        <tr>
          <th class="category">{{$_salle->nom}}</th>
        </tr>
      </table>
      {{assign var="salle" value=$_salle}}     
      {{include file="../../dPsalleOp/templates/inc_details_plages.tpl"}}
    </td>
    {{foreachelse}}
    <td>{{tr}}CSalle.none{{/tr}}</td>
    {{/foreach}}
  </tr>
</table>