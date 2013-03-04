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
  Calendar.regField(getForm("changeDate").date, null, {noView: true});
});

function printFicheBloc(interv_id) {
  var url = new Url("dPsalleOp", "print_feuille_bloc");
  url.addParam("operation_id", interv_id);
  url.popup(700, 600, 'FeuilleBloc');
}

function printAnapath() {
  var form = getForm(changeDate);
  var url = new Url("dPbloc", "print_anapath");
  url.addParam("date"   , $V(form.date));
  url.addParam("bloc_id", $V(form.bloc_id));
  url.popup(700, 600, 'Anapath');
}

function printBacterio() {
  var form = getForm(changeDate);
  var url = new Url("dPbloc", "print_bacterio");
  url.addParam("date"   , $V(form.date));
  url.addParam("bloc_id", $V(form.bloc_id));
  url.popup(700, 600, 'Bacterio');
}

function showLegend() {
  var url = new Url('bloc', 'legende').requestModal()
}
</script>

<table class="main" id="suivi-salles">
  <tr class="only-printable">
    <th colspan="100">
      <h1 class="no-break">{{$date_suivi|date_format:$conf.longdate}}</h1>
    </th>
  </tr>
  <tr class="not-printable">
    <th colspan="100">
      <button type="button" onclick="showLegend()" class="search" style="float: left;">Légende</button>
      <button type="button" onclick="$('suivi-salles').print();" class="print" style="float: right;">{{tr}}Print{{/tr}}</button>
      <button type="button" onclick="printAnapath();" class="print" style="float: right;">{{tr}}COperation-anapath{{/tr}}</button>
      <button type="button" onclick="printBacterio();" class="print" style="float: right;">{{tr}}COperation-labo{{/tr}}</button>

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
        {{$date_suivi|date_format:$conf.longdate}}
        <input type="hidden" name="date" class="date" value="{{$date_suivi}}" onchange="this.form.submit()" />
      </form>
    </th>
  </tr>
  <tr class="not-printable">
    <td class="button" colspan="100">
      {{foreach from=$bloc->_ref_salles item=_salle}}
        <label><input type="checkbox" onclick="Effect.toggle('salle-{{$_salle->_id}}', 'appear');" checked="checked" /> {{$_salle->nom}}</label>
      {{/foreach}}
      {{if $non_traitees|@count}}
        <label><input type="checkbox" onclick="Effect.toggle('non-traitees', 'appear');" checked="checked" /> {{tr}}CSejour.type.hors_plage{{/tr}}</label>
      {{/if}}
    </td>
  </tr>
  <tr>
    {{foreach from=$bloc->_ref_salles item=_salle}}
    <td id="salle-{{$_salle->_id}}">
      <table class="tbl">
        <tr>
          <th class="title">{{$_salle->nom}}</th>
        </tr>
      </table>
      {{mb_include module=salleOp template=inc_details_plages salle=$_salle}}
    </td>
    {{foreachelse}}
    <td class="empty">{{tr}}CSalle.none{{/tr}}</td>
    {{/foreach}}
    {{if $non_traitees|@count}}
      {{assign var=salle value=""}}
      <td id="non-traitees">
        <table class="tbl">
          <tr>
            <th class="title" colspan="5">{{tr}}CSejour.type.hors_plage{{/tr}}</th>
          </tr>
          {{include file="../../dPsalleOp/templates/inc_liste_operations.tpl" urgence=1 operations=$non_traitees}}
        </table>
      </td>
    {{/if}}
  </tr>
</table>