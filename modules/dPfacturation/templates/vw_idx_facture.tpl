{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPfacturation
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include_script module="system" script="object_selector"}}

<table class="main">
  <tr>
    <td class="halfPane" rowspan="3">
      <a class="button new" href="?m=dPfacturation&amp;tab=vw_idx_facture&amp;facture_id=0">
        Créer une nouvelle facture
      </a>
      <table class="tbl">
        <tr>
          <th class="title" colspan="5">Factures</th>
        </tr>
        <tr>
          <th>Date</th>
          <th>Nombre d'élément(s)</th>
 		  <th>Montant</th>
        </tr>
        {{foreach from=$listFacture item=curr_facture}}
        <tr {{if $curr_facture->_id == $facture->_id}}class="selected"{{/if}}>
          <td>
           <a href="?m=dPfacturation&amp;tab=vw_idx_facture&amp;facture_id={{$curr_facture->_id}}" title="Modifier la facture">
              {{mb_value object=$curr_facture field="date"}}
            </a>
          </td>
          <td>{{$curr_facture->_ref_items|@count}}</td>
           <td>{{mb_value object=$curr_facture field="_total"}}</td>
        </tr>
        {{/foreach}}
      </table>
  	</td>
  	<td class="halfPane">
      {{if $can->edit}}
      <form name="editfacture" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_facture_aed" />
      <input type="hidden" name="facture_id" value="{{$facture->_id}}" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="_selector_class_name" value="CSejour" />
      
      
      <table class="form">
        <tr>
          {{if $facture->_id}}
          <th class="title modify" colspan="2">
     	 	Modification de la facture {{$facture->_view}}
          </th>
          {{else}}
          <th class="title" colspan="2">
      		Création d'une facture
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
        	  	<button type="button" onclick="ObjectSelector.init()" class="search">Rechercher</button>       	  	
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
            <button class="submit" type="submit">Valider</button>
            {{if $facture->_id}}
              <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'la facture',objName:'{{$facture->_view|smarty:nodefaults|JSAttribute}}'})">Supprimer</button>
            {{/if}}
          </td>
        </tr>        
      </table>
      </form>
      {{/if}}
    </td>
  </tr>
  <tr>
    <td class="halfPane">
         {{if $facture->_id}}
         <button class="new" type="button" onclick="window.location='?m=dPfacturation&amp;tab=vw_idx_factureitem&amp;facture_item_id=0&amp;facture_id={{$facture->_id}}'">
           Créer un nouveau élément de la facture
         </button>        
         {{include file="list_element.tpl"}}
         {{/if}}
    </td>
  </tr>
 </table>

 
