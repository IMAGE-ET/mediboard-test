{{*
  * Ajout de personnel et changement de salle
  *  
  * @category dPbloc
  * @package  Mediboard
  * @author   SARL OpenXtrem <dev@openxtrem.com>
  * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
  * @version  SVN: $Id:$ 
  * @link     http://www.mediboard.org
*}}

<script type="text/javascript">
  Main.add(reloadPersonnel.curry('{{$operation->_id}}'));
</script>
<table class="form">
  <tr>
    <th class="title" colspan="2">Changement de salle</th>
  </tr>
  <tr>
    <th>{{mb_label object=$operation field=salle_id}}</th>
    <td>
      <form name="editOp" method="post" onsubmit="return onSubmitFormAjax(this)">
        <input type="hidden" name="m" value="dPplanningOp" />
        <input type="hidden" name="dosql" value="do_planning_aed" />
        <input type="hidden" name="ajax" value="1" />
        {{mb_key object=$operation}}
        <select name="salle_id" onchange="this.form.onsubmit()">
          {{foreach from=$blocs item=curr_bloc}}
            <optgroup label="{{$curr_bloc->nom}}">
              {{foreach from=$curr_bloc->_ref_salles item=curr_salle}}
                <option value="{{$curr_salle->_id}}" {{if $curr_salle->_id == $operation->salle_id}}selected="selected"{{/if}}>
                  {{$curr_salle->nom}}
                </option>
                {{foreachelse}}
                <option value="" disabled="disabled">{{tr}}CSalle.none{{/tr}}</option>
              {{/foreach}}
            </optgroup>
          {{/foreach}}
        </select>
      </form>
    </td>
  </tr>
  <tr>
    <td colspan="2" id="listPersonnel"></td>
  </tr>
</table>