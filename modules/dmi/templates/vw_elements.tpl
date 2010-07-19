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
  
  viewListElement('CDMI');
  viewElement('CDMI', '0');
  
  viewListElement('CDM');
  viewElement('CDM','0');
});

viewListElement = function(object_class, element_id){
  getForm('filter-'+object_class).onsubmit();
  /*var url = new Url("dmi", "httpreq_vw_list_elements");
  url.requestUpdate("elements_"+category_class, { onComplete: function(){
    $('element-'+element_id).addClassName("selected"); 
  }} );*/
}

viewElement = function(element_class, element_id){
  var url = new Url("dmi", "httpreq_edit_element");
  url.addParam("element_id", element_id);
  url.addParam("element_class", element_class);
  url.requestUpdate("edit_"+element_class); 
}

changePageCDMI = function(start){
  $V(getForm("filter-CDMI").start_CDMI, start);
}

changePageCDM = function(start){
  $V(getForm("filter-CDM").start_CDM, start);
}

</script>

<ul id="tab_dispositifs" class="control_tabs">
  <li><a href="#dmi">DMI</a></li>
  <li><a href="#dm">DM</a></li>
</ul>
<hr class="control_tabs" />

<table class="main">
  <col style="width: 50%" />
  
  <tr id="dmi" style="display: none;">
    <td>
      <form name="filter-CDMI" action="" method="get" onsubmit="return Url.update(this, 'elements_CDMI')">
        <input type="hidden" name="m" value="dmi" />
        <input type="hidden" name="a" value="httpreq_vw_list_elements" />
        <input type="hidden" name="start_CDMI" value="{{$start_CDMI}}" onchange="this.form.onsubmit()" />
        <input type="hidden" name="object_class" value="CDMI" />
        
        <select name="category_id" onchange="this.form.start_CDMI.value=0;this.form.onsubmit();">
          <option value=""> &ndash; Toutes les catégories</option>
          {{foreach from=$list_categories_CDMI item=_category}}
            <option value="{{$_category->_id}}">{{$_category}}</option>
          {{/foreach}}
        </select>
        
        <input type="text" name="keywords_CDMI" onchange="this.form.start_CDMI.value=0" value="{{$keywords_CDMI}}" />
        <button type="submit" class="search notext">{{tr}}Search{{/tr}}</button>
      </form>
      <div id="elements_CDMI"></div>
    </td>
    <td id="edit_CDMI"></td>
  </tr>
  
  <tr id="dm" style="display: none;">
    <td>
      <form name="filter-CDM" action="" method="get" onsubmit="return Url.update(this, 'elements_CDM')">
        <input type="hidden" name="m" value="dmi" />
        <input type="hidden" name="a" value="httpreq_vw_list_elements" />
        <input type="hidden" name="start_CDM" value="{{$start_CDM}}" onchange="this.form.onsubmit()" />
        <input type="hidden" name="object_class" value="CDM" />
        
        <select name="category_id" onchange="this.form.start_CDM.value=0;this.form.onsubmit();">
          <option value=""> &ndash; Toutes les catégories</option>
          {{foreach from=$list_categories_CDM item=_category}}
            <option value="{{$_category->_id}}">{{$_category}}</option>
          {{/foreach}}
        </select>
        
        <input type="text" name="keywords_CDM" onchange="this.form.start_CDM.value=0" value="{{$keywords_CDM}}" />
        <button type="submit" class="search notext">{{tr}}Search{{/tr}}</button>
      </form>
      
      <div id="elements_CDM"></div>
    </td>
    <td id="edit_CDM"></td>
  </tr>
</table>