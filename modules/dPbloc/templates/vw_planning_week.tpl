{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module=bloc script=edit_planning}}

<table class="main">
  <tr>
    <td class="greedyPane" style="text-align:center;">
      {{if $nbIntervNonPlacees || $nbIntervHorsPlage || $nbAlertesInterv}}
        <div class="warning" style="float: right;">
          <a href="#nothing" onclick="EditPlanning.showAlerte('{{$date}}', '{{$bloc->_id}}', 'semaine');">
          {{if $nbAlertesInterv}}
            {{$nbAlertesInterv}} alerte(s) sur des interventions
            <br />
          {{/if}}
          {{if $nbIntervNonPlacees}}
            {{$nbIntervNonPlacees}} intervention(s) non validée(s)
            <br />
          {{/if}}
          {{if $nbIntervHorsPlage}}
            {{$nbIntervHorsPlage}} intervention(s) hors plage
            <br />
          {{/if}}
          </a>
        </div>
      {{/if}}
      <form action="?" name="selectBloc" method="get">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="tab" value="vw_planning_week" />
        <select name="bloc_id" onchange="this.form.submit();">
          {{foreach from=$listBlocs item=curr_bloc}}
            <option value="{{$curr_bloc->_id}}" {{if $curr_bloc->_id == $bloc->_id}}selected="selected"{{/if}}>
              {{$curr_bloc->nom}}
            </option>
          {{foreachelse}}
            <option value="" disabled="disabled">{{tr}}CBlocOperatoire.none{{/tr}}</option>
          {{/foreach}}
        </select>
      </form>
    </td>
    <td rowspan="100">
      {{include file="inc_legende_planning.tpl"}}
    </td>
  </tr>
  <tr>
    <td class="greedyPane">
      <table class="planningBloc">
      {{assign var=typeVuePlanning value="week"}}
      {{foreach from=$listDays key=curr_day item=plagesPerDay}}
        {{include file="inc_planning_day.tpl"}}
      {{/foreach}}
      </table>
    </td>
  </tr>
</table>