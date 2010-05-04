{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dmi
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

Main.add(function () {
  var tabs = Control.Tabs.create('tab_dispositifs', false);
  
  viewListCat('CDMICategory');
  viewCat('CDMICategory', '0');
});

viewListCat = function(category_class, cat_id){
  var url = new Url("dmi", "httpreq_vw_list_categories");
  url.addParam("category_class", category_class);
  url.requestUpdate("list_cat_"+category_class, { onComplete: function(){ $('cat-'+cat_id).addClassName("selected"); } } );
}

viewCat = function(category_class, category_id){
  var url = new Url("dmi", "httpreq_edit_category");
  url.addParam("category_class", category_class);
  url.addParam("category_id", category_id);
  url.requestUpdate("cat_"+category_class);
}

function markAsSelected(element) {
  removeSelectedTr();
  $(element).up(1).addClassName('selected');
}

function removeSelectedTr(){
  $("div_categories").select('.selected').each(function (e) {e.removeClassName('selected')});
}

</script>

<ul id="tab_dispositifs" class="control_tabs">
  <li onmousedown="viewListCat('CDMICategory'); viewCat('CDMICategory','0');"><a href="#dmi">DMI</a></li>
  <li onmousedown="viewListCat('CCategoryDM'); viewCat('CCategoryDM','0');"><a href="#dm">DM</a></li>
</ul>
<hr class="control_tabs" />

<div id="div_categories">
<table class="main" id="dmi" style="display: none;">
 <tr>
   <td id="list_cat_CDMICategory"></td>
   <td id="cat_CDMICategory"></td>
 </tr>
</table>

<table class="main" id="dm" style="display: none;">
  <tr>
    <td id="list_cat_CCategoryDM"></td>
    <td id="cat_CCategoryDM"></td>
  </tr>
</table>
</div>