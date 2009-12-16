{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{$count}} 
{{if $count==0}}
  {{tr}}CProductCategory.one{{/tr}}
{{else}}
  {{tr}}CProductCategory.more{{/tr}}
{{/if}} {{if $total}}(sur {{$total}}){{/if}}<br />
<select name="category_id" id="category_id" onchange="refreshProductsList(this.value); this.form.search_category.value=''; this.form.search_product.value='';" size="15" style="width: 150px;">
  <option value="0">&mdash; {{tr}}CProductCategory.all{{/tr}}</option>
  {{foreach from=$list_categories item=curr_category}}
  <option value="{{$curr_category->_id}}" {{if $curr_category->_id==$selected_category}}selected="selected"{{/if}}>{{$curr_category->name}}</option>
  {{/foreach}}
</select>