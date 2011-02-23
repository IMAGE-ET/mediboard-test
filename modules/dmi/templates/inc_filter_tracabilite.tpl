{{* $Id: inc_div_dmi.tpl 9439 2010-07-12 16:40:48Z phenxdesign $ *}}

{{*
  * @package Mediboard
  * @subpackage dmi
  * @version $Revision: 9439 $
  * @author SARL OpenXtrem
  * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
  *}}

{{mb_include_script module="dPpatients" script="pat_selector"}}

<script type="text/javascript">
function changePage(page){
  $V(getForm("tracabiliteFilter").elements.start, page);
}

Main.add(function(){
  var form = getForm("tracabiliteFilter");
  
  var url = new Url("system", "ajax_seek_autocomplete");
  url.addParam("object_class", "CProduct");
  url.addParam("field", "product_id");
  url.addParam("input_field", "_product_view");
  url.autoComplete(form.elements._product_view, null, {
    minChars: 3,
    method: "get",
    select: "view",
    dropdown: true,
    afterUpdateElement: function(field,selected){
      $V(field.form.product_id, selected.getAttribute("id").split("-")[2]);
      if ($V(field.form.elements._product_view) == "") {
        $V(field.form.elements._product_view, selected.down('.view').innerHTML);
      }
    }
  });
});
</script>

<form class="not-printable" name="tracabiliteFilter" action="?" method="get">
  <input name="m" value="{{$m}}" type="hidden" />
  <input name="{{$actionType}}" value="{{$action}}" type="hidden" />
  <input name="dialog" value="{{$dialog}}" type="hidden" />
  <input name="start" value="{{$start}}" type="hidden" onchange="this.form.submit()" />

  <table class="form">
  	<tr>
      <th>{{mb_label object=$filter field=_patient_id}}</th>
      <td>
        {{mb_field object=$filter field=_patient_id hidden=true}}
        <input type="text" name="_patient_view" size="30" value="{{$filter->_ref_patient}}" readonly="readonly"
          ondblclick="PatSelector.init()"
        />
        <button type="button" class="search" onclick="PatSelector.init()">{{tr}}Choose{{/tr}}</button>
        <button type="button" class="cancel notext" onclick="$V(this.form._patient_id,'');$V(this.form._patient_view,'');">{{tr}}Reset{{/tr}}</button>

        <script type="text/javascript">
          PatSelector.init = function(){
            this.sForm = "tracabiliteFilter";
            this.sId   = "_patient_id";
            this.sView = "_patient_view";
            this.pop();
          }
        </script>
      </td>
      
      <th>{{mb_label object=$filter field=product_id}}</th>
      <td>
        {{mb_field object=$filter field=product_id canNull=true hidden=true}}
        <input name="_product_view" value="{{$filter->_ref_product}}" size="35" />
        <button type="button" class="cancel notext" onclick="$V(this.form.product_id,'');$V(this.form._product_view,'');">{{tr}}Reset{{/tr}}</button>
      </td>
      
      <td rowspan="2">
        <button type="submit" class="search">{{tr}}Search{{/tr}}</button>
      </td>
    </tr>
		
		<tr>
      <th>{{mb_label object=$product_reference field=societe_id}}</th>
      <td>
      	{{mb_field object=$product_reference field=societe_id form=tracabiliteFilter autocomplete="true,1,50,true,true"}}
        <button type="button" class="cancel notext" onclick="$V(this.form.societe_id,'');$V(this.form.societe_id_autocomplete_view,'');">{{tr}}Reset{{/tr}}</button>
			</td>
			
      <th>{{mb_label object=$filter field=order_item_reception_id}}</th>
      <td>
      	<input type="text" name="lot" value="{{$lot}}" />
				
				{{mb_label object=$filter field=septic}}
				{{mb_field object=$filter field=septic typeEnum=checkbox}}
			</td>
  	</tr>
  </table>
</form>

{{mb_include module=system template=inc_pagination change_page="changePage" 
    total=$line_count current=$start step=30}}
