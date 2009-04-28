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
      <a class="button new" href="?m={{$m}}&amp;tab={{$tab}}&amp;bloc_id=0">{{tr}}CBlocOperatoire-title-create{{/tr}}</a>
      <table class="tbl">
        <tr>
          <th>{{tr}}CBlocOperatoire-nom{{/tr}}</th>
          <th>{{tr}}Salles{{/tr}}</th>
        </tr>
        
        {{foreach from=$blocs_list item=curr_bloc}}
        <tr {{if $curr_bloc->_id == $bloc->_id}}class="selected"{{/if}}>
          <td><a href="?m={{$m}}&amp;tab={{$tab}}&amp;bloc_id={{$curr_bloc->_id}}">{{$curr_bloc->nom}}</a></td>
          <td>{{$curr_bloc->_ref_salles|@count}}</td>
        </tr>
        {{/foreach}}
      </table>
    </td>
    
    <td class="halfPane">
      <form name="bloc-edit" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
        <input type="hidden" name="dosql" value="do_bloc_operatoire_aed" />
        <input type="hidden" name="bloc_operatoire_id" value="{{$bloc->_id}}" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="group_id" value="{{$g}}" />
        <table class="form">
          <tr>
            <th class="category" colspan="2">
            {{if $bloc->_id}}
              {{tr}}CBlocOperatoire-title-modify{{/tr}} "{{$bloc->nom}}"
            {{else}}
              {{tr}}CBlocOperatoire-title-create{{/tr}}
            {{/if}}
            </th>
          </tr>
          
          <tr>
            <th>{{mb_label object=$bloc field="nom"}}</th>
            <td>{{mb_field object=$bloc field="nom"}}</td>
          </tr>
          
          <tr>
            <td class="button" colspan="2">
              {{if $bloc->_id}}
              <button class="submit" type="submit">{{tr}}Modify{{/tr}}</button>
              <button type="button" class="trash" onclick="confirmDeletion(this.form,{typeName:'',objName:'{{$bloc->nom|smarty:nodefaults|JSAttribute}}'})">
                {{tr}}Delete{{/tr}}
              </button>
              {{else}}
              <button type="submit" class="new">{{tr}}Create{{/tr}}</button>
              {{/if}}
            </td>
          </tr>
        </table>
      </form>
    </td>
  </tr>
</table>
