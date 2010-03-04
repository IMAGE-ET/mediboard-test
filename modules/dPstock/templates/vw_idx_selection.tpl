{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
function changePage(start) {
  $V(getForm("filter-selections").start, start);
}

function changeLetter(letter) {
  var form = getForm("filter-selections");
  $V(form.start, 0, false);
  $V(form.letter, letter);
}

function filterSelections(form) {
  var url = new Url("dPstock", "httpreq_vw_selections_list");
  url.addFormData(form);
  url.requestUpdate("list-selections");
  return false;
}

function loadSelection(selection_id, selection_item) {
  if (selection_item) {
    selection_id = selection_item.selection_id;
  }
  
  var url = new Url("dPstock", "httpreq_vw_selection_form");
  
  if (!Object.isUndefined(selection_id))
    url.addParam("selection_id", selection_id);
    
  url.requestUpdate("selection-form");
  return false;
}

function deleteSelectionItem(item_id) {
  var form = getForm("edit_selection_item");
  var selection_id = $V(form.selection_id);
  $V(form.del, 1);
  $V(form.selection_item_id, item_id);
  form.product_id.className = "";
  form.onsubmit();
  loadSelection(selection_id);
}

Main.add(function(){
  filterSelections(getForm("filter-selections"));
  loadSelection();
});

</script>

<table class="main">
  <tr>
    <td class="halfPane" rowspan="10">
      <form name="filter-selections" action="?" method="post" onsubmit="return filterSelections(this)">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="start" value="0" onchange="this.form.onsubmit()" />
        <input type="hidden" name="letter" value="{{$letter}}" onchange="this.form.onsubmit()" />
        
        <input type="text" name="keywords" value="{{$keywords}}" />
        
        <button type="submit" class="search notext">{{tr}}Filter{{/tr}}</button>
        <button type="button" class="cancel notext" onclick="$(this.form).clear(false); this.form.onsubmit();"></button>
        
        {{mb_include module=system template=inc_pagination_alpha current=$letter change_page=changeLetter}}
      </form>

      <div id="list-selections"></div>
    </td>
    
    <td class="halfPane" id="selection-form"></td>
  </tr>
</table>