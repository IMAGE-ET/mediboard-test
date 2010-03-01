{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="main">
  <tr>
    <td class="halfPane">
      <a class="button new" href="?m={{$m}}&amp;tab={{$tab}}&amp;salle_id=0">{{tr}}CSalle-title-create{{/tr}}</a>
      <table class="tbl">
        {{foreach from=$blocs_list item=curr_bloc}}
          <tr>
            <th class="title">{{$curr_bloc->nom}}</th>
          </tr>
          {{foreach from=$curr_bloc->_ref_salles item=curr_salle}}
            <tr {{if $curr_salle->_id == $salle->_id}}class="selected"{{/if}}>
              <td><a href="?m={{$m}}&amp;tab={{$tab}}&amp;salle_id={{$curr_salle->_id}}">{{$curr_salle->nom}}</a></td>
            </tr>
          {{foreachelse}}
            <tr><td>{{tr}}CSalle.none{{/tr}}</td></tr>
          {{/foreach}}
        {{/foreach}}
      </table>
    </td>
    
    <td class="halfPane">
      <form name="salle" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
        <input type="hidden" name="dosql" value="do_salle_aed" />
        <input type="hidden" name="salle_id" value="{{$salle->_id}}" />
        <input type="hidden" name="del" value="0" />
    
        <table class="form">
    
        <tr>
          <th class="title {{if $salle->_id}}modify{{/if}}" colspan="2">
          {{if $salle->_id}}
            {{tr}}CSalle-title-modify{{/tr}} "{{$salle->nom}}"
          {{else}}
            {{tr}}CSalle-title-create{{/tr}}
          {{/if}}
          </th>
        </tr>
    
        <tr>
          <th>{{mb_label object=$salle field="bloc_id"}}</th>
          <td>
            <select class="{{$salle->_props.bloc_id}}" name="bloc_id">
              <option value="">&mdash; {{tr}}CBlocOperatoire.select{{/tr}}</option>
              {{foreach from=$blocs_list item=curr_bloc}}
              <option value="{{$curr_bloc->_id}}" {{if ($salle->_id && $salle->_ref_bloc->_id==$curr_bloc->_id)}} selected="selected"{{/if}}>{{$curr_bloc->nom}}</option>
              {{/foreach}}
            </select>
          </td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$salle field="nom"}}</th>
          <td>{{mb_field object=$salle field="nom"}}</td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$salle field="stats"}}</th>
          <td>{{mb_field object=$salle field="stats"}}</td>
        </tr>
        
        <tr>
          <td class="button" colspan="2">
            {{if $salle->salle_id}}
            <button class="submit" type="submit">{{tr}}Save{{/tr}}</button>
            <button type="button" class="trash" onclick="confirmDeletion(this.form,{typeName:'la salle',objName:'{{$salle->nom|smarty:nodefaults|JSAttribute}}'})">
              {{tr}}Delete{{/tr}}
            </button>
            {{else}}
            <button type="submit" class="new">
              {{tr}}Create{{/tr}}
            </button>
            {{/if}}
          </td>
        </tr>
        </table>
      </form>
    </td>
  </tr>
</table>
