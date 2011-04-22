{{*
 * @package Mediboard
 * @subpackage dPfacturation
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}


<script type="text/javascript">
submitFacture = function(form) {
  onSubmitFormAjax(form, {
    onComplete: function() {
			showListFacture();
    }
  });
  return false;
}
</script>

<!--   <td class="halfPane"> -->
      {{if $can->edit}}
<form name="editfacture" action="?m={{$m}}" method="post" onsubmit="return submitFacture(this)">
  <input type="hidden" name="m" value="dPfacturation" />
  <input type="hidden" name="dosql" value="do_facture_aed" />
  <input type="hidden" name="facture_id" value="{{$facture->_id}}" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="callback" value="showFacture" />
  <input type="hidden" name="_selector_class_name" value="CSejour" />
  
  
  <table class="form">
    <tr>
      {{if $facture->_id}}
      <th class="title modify" colspan="2">
 	 	{{tr}}CFacture-title-modify{{/tr}} {{$facture->_view}}
      </th>
      {{else}}
      <th class="title" colspan="2">
  		{{tr}}CFacture-title-create{{/tr}}
      </th>
      {{/if}}
    </tr>
    <tr>
      <th>{{mb_label object=$facture field="date"}}</th>
      <td>{{mb_field object=$facture field="date" form="editfacture" register=true}}</td>
    </tr>
    <tr>	
      	<th>{{mb_label object=$facture field="sejour_id"}}</th>
        <td>
        	{{mb_field object=$facture field="sejour_id" hidden=true}}
         {{if $facture->sejour_id}}
	        <input type="text" size="30" readonly="readonly" ondblclick="ObjectSelector.init()" name="_sejour_view" value="{{$facture->_ref_sejour->_view|stripslashes}}" />
	        {{else}}
	        <input type="text" size="30" readonly="readonly" ondblclick="ObjectSelector.init()" name="_sejour_view" value="" />
	        {{/if}}
    	  	<button type="button" onclick="ObjectSelector.init()" class="search">{{tr}}Search{{/tr}}</button>       	  	
    	    <script type="text/javascript">
              ObjectSelector.init = function(){
                this.sForm     = "editfacture";
                this.sId       = "sejour_id";
                this.sView     = "_sejour_view";
                this.sClass    = "_selector_class_name";
                this.onlyclass = "true";
               
                this.pop();
              } 
             </script>
    	 </td>
    </tr>
    <tr>
      <td class="button" colspan="2">
        <button class="submit" type="submit">{{tr}}Validate{{/tr}}</button>
        {{if $facture->_id}}
          <button class="trash" type="button" onclick="confirmDeletion(this.form,{ajax:true, typeName:'la facture',objName:'{{$facture->_view|smarty:nodefaults|JSAttribute}}'})">Supprimer</button>
        {{/if}}
      </td>
    </tr>        
  </table>
</form>
      {{/if}}
  <!-- </td>  -->   
{{if $facture->_id}}
<table class="main">
  <tr>
    <td class="halfPane">
         <button class="new" type="button" onclick="showElementFacture('0',{{$facture->_id}})">
           {{tr}}CFacture-creer-element{{/tr}}
         </button>        
         {{include file="inc_list_element.tpl"}}   
    </td>
  </tr>
  <tr>  
     <td style="width: 40%" id="vw_element">
    </td>
  </tr>
</table>
{{/if}}