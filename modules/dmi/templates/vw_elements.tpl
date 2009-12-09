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
  viewListElement('CDMICategory');
  viewElement('CDMI', '0');
});


viewListElement = function(category_class, element_id){
  var url = new Url;
  url.setModuleAction("dmi", "httpreq_vw_list_elements");
  url.addParam("category_class", category_class);
  url.requestUpdate("elements_"+category_class, { onComplete: function(){ $('element-'+element_id).addClassName("selected"); }  } );
}

viewElement = function(element_class, element_id){
  var url = new Url;
  url.setModuleAction("dmi", "httpreq_edit_element");
  url.addParam("element_id", element_id);
  url.addParam("element_class", element_class);
  url.requestUpdate("edit_"+element_class); 
}

removeSelectedTr = function(){
  $("div_elements").select('.selected').each(function (e) {e.removeClassName('selected')});
}

function markAsSelected(element) {
  removeSelectedTr();
  $(element).up(1).addClassName('selected');
}

</script>

<ul id="tab_dispositifs" class="control_tabs">
  <li onclick="viewListElement('CDMICategory'); viewElement('CDMI','0');"><a href="#dmi">DMI</a></li>
  <li onclick="viewListElement('CCategoryDM'); viewElement('CDM','0');"><a href="#dm">DM</a></li>
</ul>
<hr class="control_tabs" />

<div id="div_elements">
<table class="main" id="dmi">
  <tr>
    <td id="elements_CDMICategory"></td>
    <td id="edit_CDMI"></td>
  </tr>
</table>

<table class="main" id="dm">
  <tr>
    <td id="elements_CCategoryDM"></td>
    <td id="edit_CDM"></td>
  </tr>
</table>
</div>