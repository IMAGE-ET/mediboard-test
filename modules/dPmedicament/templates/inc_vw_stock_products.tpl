{{* $Id: inc_vw_livret.tpl 7991 2010-02-03 16:33:54Z phenxdesign $ *}}

{{*
 * @package Mediboard
 * @subpackage dPmedicament
 * @version $Revision: 7991 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
function changePageProducts(start) {
  $V(getForm("filter-stock-products").start, start);
}

function changeLetter(letter) {
  var form = getForm("filter-stock-products");
  $V(form.start, 0, false);
  $V(form.letter, letter);
}

function filterProducts(form) {
  var url = new Url("dPmedicament", "httpreq_vw_list_stock_products");
  url.addFormData(form);
  url.requestUpdate("list-stock-products");
  return false;
}

Main.add(function(){
  filterProducts(getForm("filter-stock-products"));
});
</script>

<form action="?" method="get" name="filter-stock-products" onsubmit="return filterProducts(this)">
  <input type="hidden" name="start" value="0" onchange="this.form.onsubmit()" />
  <input type="hidden" name="letter" value="%" onchange="this.form.onsubmit()" />
  
  <table class="main form">
    <tr>
      <th>Mots clés</th>
      <td><input type="text" name="keywords" value="" onchange="$V(this.form.start, 0)" /></td>
      <th>Catégorie</th>
      <td>
        <select name="category_id" onchange="this.form.onsubmit()">
          {{foreach from=$categories item=_category}}
            <option value="{{$_category->_id}}" {{if $_category->_id == $category_id}}selected="selected"{{/if}}>{{$_category}}</option>
          {{/foreach}}
        </select>
      </td>
      <td>
        <button type="submit" class="search">{{tr}}Filter{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>

{{mb_include module=system template=inc_pagination_alpha current=$lettre change_page=changeLetter}}

<div id="list-stock-products"></div>
