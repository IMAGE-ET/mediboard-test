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
  Calendar.regField(getForm("changeDate").date_suivi, null, {noView: true});
});

function printFeuilleBloc(oper_id) {
  var url = new Url("dPsalleOp", "print_feuille_bloc");
  url.addParam("operation_id", oper_id);
  url.popup(700, 600, 'FeuilleBloc');
}

function showLegend() {
  var url = new Url("dPbloc", "legende");
  url.popup(500, 150, "Legend");
}

function printSuivi(){
  var url = new Url("dPbloc", "print_suivi");
  url.addParam("date", '{{$date_suivi}}');
  url.addParam("vue", $V(getForm('changeDate').bloc_id));
  url.popup(800, 500, "impressionSuivi");
}
</script>

<table class="main">
  <tr>
    <th colspan="100">
      <button type="button" onclick="showLegend()" class="search" style="float: left;">Légende</button>
      <button type="button" onclick="printSuivi()" class="search" style="float: right;">Version imprimable</button>
      
      <form action="?" name="changeDate" method="get">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="tab" value="{{$tab}}" />
        
        <select name="bloc_id" onchange="this.form.submit()">
        {{foreach from=$listBlocs item=curr_bloc}}
          <option value="{{$curr_bloc->_id}}" {{if $curr_bloc->_id == $bloc->_id}}selected="selected"{{/if}}>
            {{$curr_bloc->nom}}
          </option>
        {{/foreach}}
        </select>
        {{$date_suivi|date_format:$dPconfig.longdate}}
        <input type="hidden" name="date_suivi" class="date" value="{{$date_suivi}}" onchange="this.form.submit()" />
      </form>
    </th>
  </tr>
  <tr>
    {{foreach from=$bloc->_ref_salles item=_salle}}
    <td>
      <table class="form">
        <tr>
          <th class="title">{{$_salle->nom}}</th>
        </tr>
      </table>
      {{mb_include module=dPsalleOp template=inc_details_plages salle=$_salle}}
    </td>
    {{foreachelse}}
    <td>{{tr}}CSalle.none{{/tr}}</td>
    {{/foreach}}
  </tr>
</table>