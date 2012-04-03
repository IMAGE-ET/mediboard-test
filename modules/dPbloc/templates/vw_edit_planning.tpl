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
      {{if $can->edit}}
      {{if $nbIntervNonPlacees || $nbIntervHorsPlage || $nbAlertesInterv}}
        <div class="warning" style="float: right; text-align:left;">
          <a href="#nothing" onclick="EditPlanning.showAlerte('{{$date}}', '{{$bloc->_id}}', 'jour')">
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
      <button class="print" onclick="EditPlanning.popPlanning('{{$date}}');" style="font-weight: bold;">
        {{$date|date_format:"%A %d %B"}}
      </button>
      {{/if}}
      
      <form action="?" name="selectBloc" method="get">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="tab" value="vw_edit_planning" />
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
      
      <table class="planningBloc">
      {{assign var=typeVuePlanning value="day"}}
      {{assign var=curr_day value=$date}}
      {{include file="inc_planning_day.tpl"}}
      </table>
      {{if $can->edit}}
      <button type="button" class="new" style="float: left;" onclick="EditPlanning.edit('','{{$date}}');">
        {{tr}}CPlageOp-title-create{{/tr}}
      </button>
			
    </form>
    {{/if}}
   </td>
   <td>
     {{include file="inc_legende_planning.tpl" listSpec=$specs}}
   </td>
  </tr>
</table>