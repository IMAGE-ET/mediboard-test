{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

submitItem = function(checked, oForm){
  // Ajout de l'element dans le groupe
	if(checked){
	  $V(oForm.del, '0');
	  oForm.callback.disabled = null;
		submitFormAjax(oForm, 'systemMsg');
	} 
	// Suppression de l'element du groupe
	else {
	  $V(oForm.del, '1');
    oForm.callback.disabled = "disabled";
		submitFormAjax(oForm, 'systemMsg', { onComplete: function(){
		  $V(oForm.prescription_category_group_item_id, '');
		  $V(oForm.del, '0');
		} } );
	}
}

</script>

<table class="main">
  <tr>
    <td class="halfPane">
      <a href="?m={{$m}}&amp;tab={{$tab}}&amp;prescription_category_group_id=0" class="button new">
        Créer un groupe de catégories
      </a>
      <table class="tbl">
      	<tr>
      		<th>Liste des groupes</th>
				</tr>
        {{foreach from=$cat_groups item=_cat_group}}
          <tr {{if $_cat_group->_id == $cat_group->_id}}class="selected"{{/if}}>
            <td>
              <a href="?m={{$m}}&amp;tab={{$tab}}&amp;prescription_category_group_id={{$_cat_group->_id}}">
                {{$_cat_group->libelle}}
              </a>
            </td>
          </tr>
        {{/foreach}}
      </table>
    </td>
    <td class="halfPane">
      <form name="group" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_prescription_category_group_aed" />
      <input type="hidden" name="prescription_category_group_id" value="{{$cat_group->_id}}" />
			<input type="hidden" name="group_id" value="{{$g}}" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          <th class="category" colspan="2">
          {{if $cat_group->_id}}
            <div class="idsante400" id="CPrescriptionCategoryGroup-{{$cat_group->_id}}"></div>
            <a style="float:right;" href="#" onclick="view_log('CPrescriptionCategoryGroup',{{$cat_group->_id}})">
              <img src="images/icons/history.gif" alt="historique" />
            </a>
            Modification du groupe &lsquo;{{$cat_group->libelle}}&rsquo;
          {{else}}
            Création d'une catégorie
          {{/if}}
          </th>
        </tr>
        <tr>
          <th>{{mb_label object=$cat_group field="libelle"}}</th>
          <td>{{mb_field object=$cat_group field="libelle"}}</td>
        </tr>
        <tr>
          <td class="button" colspan="2">
          {{if $cat_group->_id}}
            <button class="modify" type="submit" name="modify">
              {{tr}}Modify{{/tr}}
            </button>
            <button class="trash" type="button" name="delete" 
						        onclick="confirmDeletion(this.form,{typeName:'la catégorie',objName:'{{$cat_group->libelle|smarty:nodefaults|JSAttribute}}'})">
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
			<table class="form">
				<tr>
					<th class="category">Sélection des catégories</th>
			  </tr>
				{{if $cat_group->_id}}
						<tr>
							<td colspan="2">
								<table class="form">
								{{foreach from=$categories item=categories_by_chap key=name name="foreach_cat"}}
			             {{if $categories_by_chap|@count}}
			               <tr>
											{{if $name != "medicaments"}}
			                 <td>
			                   <strong>{{tr}}CCategoryPrescription.chapitre.{{$name}}{{/tr}}</strong>  
			                 </td>
											 {{else}}
											 <td>
											 	<strong>Medicaments</strong>
												</td>
											 {{/if}}
			                 {{foreach from=$categories_by_chap item=categorie}}
											   {{if $name == "medicaments"}}
	                         <script type="text/javascript">
	                            function updateId{{$categorie}} (id) {
	                              var oForm = document.forms["editGroupItem-{{$categorie}}"];
	                              $V(oForm.prescription_category_group_item_id, id);
	                            }
	                         </script>
											     <td style="white-space: nowrap; float: left; width: 10em;">
                             <form name="editGroupItem-{{$categorie}}" method="post" action="?">
                                <input type="hidden" name="m" value="dPprescription" />
                                <input type="hidden" name="dosql" value="do_prescription_category_group_item_aed" />
																<input type="hidden" name="prescription_category_group_id" value="{{$cat_group->_id}}" />
                                <input type="hidden" name="prescription_category_group_item_id" 
																      value="{{if array_key_exists($categorie,$cats)}}{{$cats.$categorie}}{{/if}}" />
                                <input type="hidden" name="del" value="0" /> 
                                <input type="hidden" name="type_produit" value="{{$categorie}}" />
																<input type="hidden" name="category_prescription_id" value="" />
																 <input type="hidden" name="callback" value="updateId{{$categorie}}" />
																
                                <label title="{{tr}}CPrescription._chapitres.{{$categorie}}{{/tr}}">
																<input type="checkbox" value="{{$categorie}}" onchange="submitItem(this.checked, this.form);" {{if array_key_exists($categorie,$cats)}}checked="checked"{{/if}} /> {{tr}}CPrescription._chapitres.{{$categorie}}{{/tr}}
																</label>
													   </form>
													 </td>
                         {{else}}
												 <td style="white-space: nowrap; float: left; width: 10em;">
												 {{assign var=categorie_id value=$categorie->_id}}
			                       <script type="text/javascript">
		                            function updateId{{$categorie->_id}} (id) {
		                              var oForm = document.forms["editGroupItem-{{$categorie_id}}"];
		                              $V(oForm.prescription_category_group_item_id, id);
		                            }
		                         </script>
													   <form name="editGroupItem-{{$categorie->_id}}" method="post" action="?">
															  <input type="hidden" name="m" value="dPprescription" />
															  <input type="hidden" name="dosql" value="do_prescription_category_group_item_aed" />
															   <input type="hidden" name="prescription_category_group_item_id" 
                                      value="{{if array_key_exists($categorie_id,$cats)}}{{$cats.$categorie_id}}{{/if}}" />
																<input type="hidden" name="prescription_category_group_id" value="{{$cat_group->_id}}" />
															  <input type="hidden" name="del" value="0" /> 
														    <input type="hidden" name="category_prescription_id" value="{{$categorie->_id}}" />
																<input type="hidden" name="type_produit" value="" />
                                <input type="hidden" name="callback" value="updateId{{$categorie->_id}}" />
																
					                      <label title="{{$categorie->_view}}">
					                        <input type="checkbox" value="{{$categorie->_id}}" onchange="submitItem(this.checked, this.form);" {{if array_key_exists($categorie_id,$cats)}}checked="checked"{{/if}} /> 
																	{{$categorie->_view}}
					                      </label>
														 </form>
				                   </td>
												 {{/if}}
			                 {{/foreach}}
			               </tr>
			             {{/if}}
			           {{/foreach}}
						 </table>
					  </td>
					</tr>
				{{/if}}
      </table>
    </td>
  </tr>
</table>