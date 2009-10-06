{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPqualite
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $item->ei_item_id}}
<a class="button new" href="?m={{$m}}&amp;tab=vw_edit_ei&amp;ei_item_id=0">
  {{tr}}CEiItem.create{{/tr}}
</a>
{{/if}}
<form name="editCategorie" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
<input type="hidden" name="dosql" value="do_eiItem_aed" />
<input type="hidden" name="ei_item_id" value="{{$item->ei_item_id}}" />
<input type="hidden" name="del" value="0" />
<table class="form">
  <tr>
    {{if $item->ei_item_id}}
    <th colspan="2" class="category modify">
      {{tr}}CEiItem-title-modify{{/tr}} : {{$item->_view}}
    {{else}}
    <th colspan="2" class="category">
      {{tr}}CEiItem-title-create{{/tr}}
    {{/if}}
    </th>
  </tr>
  <tr>
    <th>{{mb_label object=$item field="nom"}}</th>
    <td>{{mb_field object=$item field="nom"}}</td>
  </tr>
  <tr>
  	<th>{{mb_label object=$item field="ei_categorie_id"}}</th>
    <td>
      <select name="ei_categorie_id" class="{{$item->_props.ei_categorie_id}}">
        <option value="">&mdash; {{tr}}CEiItem-ei_categorie_id-desc{{/tr}}</option>
        {{foreach from=$listCategories item=curr_cat}}        
        <option value="{{$curr_cat->ei_categorie_id}}"{{if $curr_cat->ei_categorie_id==$item->ei_categorie_id}} selected="selected"{{/if}}>
          {{$curr_cat->nom}}
        </option>
        {{/foreach}}
      </select>
    </td>
  </tr>
  <tr>
    <td class="button" colspan="2">            
      {{if $item->ei_item_id}}
      <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
      <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'{{tr escape="javascript"}}CEiItem.one{{/tr}}',objName:'{{$item->_view|smarty:nodefaults|JSAttribute}}'})">{{tr}}Delete{{/tr}}</button>
      {{else}}
      <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
      {{/if}}
    </td>
  </tr>  
</table>
</form>
<br />

<table class="tbl">
  <tr>
    <th>{{tr}}CEiItem-nom-court{{/tr}}</th>
    <th>
      <form name="chgMode" action="?m={{$m}}" method="get">
      <input type="hidden" name="m" value="{{$m}}" />
      <select name="vue_item" onchange="submit()">
        <option value="">&mdash; {{tr}}CEiCategorie.all{{/tr}}</option>
        {{foreach from=$listCategories item=curr_cat}}        
          <option value="{{$curr_cat->ei_categorie_id}}"{{if $curr_cat->ei_categorie_id==$vue_item}} selected="selected"{{/if}}>
            {{$curr_cat->nom}}
          </option>
        {{/foreach}}
      </select>
      </form>
    </th>
  </tr>
  {{foreach from=$listItems item=curr_item}}
  <tr>
    <td class="text">
      <a href="?m={{$m}}&amp;tab=vw_edit_ei&amp;ei_item_id={{$curr_item->ei_item_id}}" title="{{tr}}CEiItem.modify{{/tr}}">
        {{$curr_item->nom}}
      </a>
    </td>
    <td class="text">
      {{$curr_item->_ref_categorie->nom}}
    </td>
  </tr>
  {{foreachelse}}
  <tr>
    <td colspan="3">
      {{tr}}CEiItem.none{{/tr}}
    </td>
  </tr>
  {{/foreach}}
</table>