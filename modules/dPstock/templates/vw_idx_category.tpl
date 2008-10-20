{{* $Id$ *}}

{{*  
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author Fabien Ménager
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
*}}

{{mb_include_script module="dPstock" script="numeric_field"}}

<table class="main">
  <tr>
    <td class="halfPane">
      <a class="buttonnew" href="?m=dPstock&amp;tab=vw_idx_category&amp;category_id=0">{{tr}}CProductCategory.create{{/tr}}</a>
      <table class="tbl">
        <tr>
          <th>{{tr}}CProductCategory{{/tr}}</th>
        </tr>
        {{foreach from=$list_categories item=curr_category}}
        <tr {{if $curr_category->_id == $category->_id}}class="selected"{{/if}}>
          <td class="text">
            <a href="?m=dPstock&amp;tab=vw_idx_category&amp;category_id={{$curr_category->_id}}" title="{{tr}}CProductCategory.modify{{/tr}}">
              {{mb_value object=$curr_category field=name}}
            </a>
          </td>
        </tr>
        {{/foreach}}        
      </table>  
    </td>
    <td class="halfPane">
      <form name="edit_category" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_category_aed" />
	  <input type="hidden" name="category_id" value="{{$category->_id}}" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          {{if $category->_id}}
          <th class="title modify" colspan="2">{{tr}}CProductCategory.modify{{/tr}} {{$category->name}}</th>
          {{else}}
          <th class="title" colspan="2">{{tr}}CProductCategory.create{{/tr}}</th>
          {{/if}}
        </tr> 
        <tr>
          <th>{{mb_label object=$category field="name"}}</th>
          <td>{{mb_field object=$category field="name"}}</td>
        </tr>
        <tr>
          <td class="button" colspan="4">
            {{if $category->_id}}
            <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
            <button type="button" class="trash" onclick="confirmDeletion(this.form,{typeName:'',objName:'{{$category->_view|smarty:nodefaults|JSAttribute}}'})">
              {{tr}}Delete{{/tr}}
            </button>
            {{else}}
            <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
            {{/if}}
          </td>
        </tr>  
      </table>
      </form>
    </td>
  </tr>
</table>