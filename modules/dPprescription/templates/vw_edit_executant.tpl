{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

// Initialisation des onglets
Main.add( function(){
  menuTabs = Control.Tabs.create('executant_tab', true);
} );

function removeFunction(function_category_id){
  var oForm = document.delFunction;
  oForm.function_category_prescription_id.value = function_category_id;
  oForm.submit();
}

</script>
<table>
  <tr>    
    <td>
     <strong> Catégorie :</strong>
      <form name="selCat" method="get" action="?">
        <input type="hidden" name="tab" value="vw_edit_executant" />
        <input type="hidden" name="m" value="dPprescription" />
        <input type="hidden" name="executant_id" value="" />
        <select name="category_id" onchange="this.form.submit()">
         <option value="">&mdash; Sélection d'une catégorie</option>
         {{foreach from=$categories key=chapitre item=categories}}
         {{if $categories}}
         <optgroup label="{{tr}}CCategoryPrescription.chapitre.{{$chapitre}}{{/tr}}">
           {{foreach from=$categories item=_category}}
           <option value="{{$_category->_id}}" {{if $category_id == $_category->_id}}selected="selected"{{/if}}>{{$_category->nom}}</option>
           {{/foreach}}
         </optgroup>
         {{/if}}
         {{/foreach}}
       </select>
      </form>
    </td>
  </tr>
</table>

<ul id="executant_tab" class="control_tabs">
  <li><a href="#executant">Exécutants</a></li>
  <li><a href="#fonction">Fonctions</a></li>
</ul>
<hr class="control_tabs" />

<table class="main" id="executant" style="display: none">
  {{if $category_id}}
  <tr>
    <td>
      <a href="?m={{$m}}&amp;tab={{$tab}}&amp;executant_prescription_line_id=0" class="buttonnew">
			  Créer un exécutant
			</a>
    </td>
  </tr>
  <tr>
    <td class="halfPane">
		  <table class="tbl">
	      <tr>
	        <th colspan="2">Executants de la catégorie {{$category->_view}}</th>
	      </tr>
	      <tr>
	        <th>Nom</th>
	        <th>Description</th>
	      </tr>
	      {{foreach from=$executants item=_executant}}
	        <tr>
	          <td>
	            <a href="?m={{$m}}&amp;tab={{$tab}}&amp;executant_prescription_line_id={{$_executant->_id}}">
	              {{$_executant->nom}}
	            </a>
	          </td>
	          <td>
	            {{$_executant->description}}
	          </td>
	        </tr>
	      {{/foreach}}
       </table>
		 </td>
		 <td class="halfPane">
		     <form name="group" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
		     <input type="hidden" name="dosql" value="do_executant_prescription_line_aed" />
		    <input type="hidden" name="executant_prescription_line_id" value="{{$executant_prescription_line->_id}}" />
		     <input type="hidden" name="category_prescription_id" value="{{$category_id}}" />
		     <input type="hidden" name="del" value="0" />
		     <table class="form">
		       <tr>
		         <th class="category" colspan="2">
		         {{if $executant_prescription_line->_id}}
		           <div class="idsante400" id="CExecutantPrescriptionLine-{{$executant_prescription_line->_id}}"></div>
		           <a style="float:right;" href="#" onclick="view_log('CExecutantPrescriptionLine',{{$executant_prescription_line->_id}})">
		             <img src="images/icons/history.gif" alt="historique" />
		           </a>
		           Modification de l'executant &lsquo;{{$executant_prescription_line->nom}}&rsquo;
		         {{else}}
		           Création d'un exécutant
		         {{/if}}
		         </th>
		       </tr>
		       <tr>
		         <th>{{mb_label object=$executant_prescription_line field="nom"}}</th>
		         <td>{{mb_field object=$executant_prescription_line field="nom"}}</td>
		       </tr>
		       <tr>
		         <th>{{mb_label object=$executant_prescription_line field="description"}}</th>
		         <td>{{mb_field object=$executant_prescription_line field="description"}}</td>
		       </tr>
		       <tr>
		         <td class="button" colspan="2">
		         {{if $executant_prescription_line->_id}}
		           <button class="modify" type="submit" name="modify">
		             {{tr}}Modify{{/tr}}
		           </button>
		           <button class="trash" type="button" name="delete" onclick="confirmDeletion(this.form,{typeName:'l\'executant',objName:'{{$executant_prescription_line->nom|smarty:nodefaults|JSAttribute}}'})">
		             {{tr}}Delete{{/tr}}
		           </button>
		         {{else}}
		           <button class="new" type="submit" name="create">
		             {{tr}}Create{{/tr}}
		           </button>
		         {{/if}}
		         </td>
		       </tr>
		     </table>
		   </form> 
    </td>
  </tr>
  {{else}}
  <tr>
    <td>
      <div class="big-info">
        Veuillez sélectionner une catégorie
      </div>
    </td>
  </tr>
  {{/if}}     
</table>

<table class="form" id="fonction" style="display: none">
  {{if $category_id}}
  <tr>
    <th class="category">Ajouter une fonction</th>
    <th class="category">Liste des fonctions</th>
  </tr>
  <tr>
    <td style="width: 20%;">
      <form name="addFunction" action="" method="post">
        <input type="hidden" name="dosql" value="do_function_category_prescription_aed" />
        <input type="hidden" name="m" value="dPprescription" />
        <input type="hidden" name="function_category_prescription_id" value="" />
        <input type="hidden" name="category_prescription_id" value="{{$category_id}}" />
      	<select name="function_id">
		      {{foreach from=$functions item=function}}
			 		  <option value="{{$function->_id}}">{{$function->_view}}</option>
		      {{/foreach}}
        </select>
        <button type="button" class="submit" onclick="this.form.submit();">Ajouter la fonction</button>
      </form>
    </td>
    <td>
	    <form name="delFunction" action="" method="post">
	      <input type="hidden" name="dosql" value="do_function_category_prescription_aed" />
	      <input type="hidden" name="m" value="dPprescription" />
	      <input type="hidden" name="del" value="1" />
	      <input type="hidden" name="function_category_prescription_id" value="" />
	    </form>  
      <table class="tbl">
        {{foreach from=$associations item=_association}} 
          <tr>
            <td>
              {{$_association->_ref_function->_view}}
            </td>
            <td>
              <button type="button" class="cancel notext" onclick="removeFunction('{{$_association->_id}}')"></button>
            </td>
          </tr>
        {{/foreach}}
      </table>
    </td>
  </tr>
  {{else}}
  <tr>
    <td>
      <div class="big-info">
        Veuillez sélectionner une catégorie
      </div>
    </td>
  </tr>
  {{/if}}
</table>