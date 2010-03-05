{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage dPmedicament
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}


<script type="text/javascript">

function updateUniteDispensation(element){
  var unite_disp = element.form.unite_dispensation;
	if(!unite_disp.value){
	  $V(unite_disp, element.value);
	}
}	

</script>
{{mb_include_script module="dPmedicament" script="medicament_selector"}}

<table class="main">
  <tr>
    <td>
      <form name="newProduit" method="get" action="">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="tab" value="vw_edit_produits" />
        <input type="hidden" name="produit_prescription_id" value="0" />
        <input type="hidden" name="del" value="0" />
        <button type="button" class="new" onclick="this.form.submit();">Créer un nouveau produit</button>
      </form>
      
      <form name="delProduit" method="post" action="">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="dosql" value="do_produit_prescription_aed" />
        <input type="hidden" name="produit_prescription_id" value="" />
        <input type="hidden" name="del" value="1" />
      </form>
      
      <table class="tbl">
        <tr>
          <th class="title">Produits</th>
        </tr>
        {{foreach from=$produits item=_produit}}
          <tr {{if $_produit->_id == $produit->_id}}class="selected"{{/if}}>
            <td>
              <button style="float: right" type="button" class="trash notext" 
                      onclick="$V(document.delProduit.produit_prescription_id, '{{$_produit->_id}}');
                               document.delProduit.submit();"></button>                                                              
              <a href="?m={{$m}}&amp;tab=vw_edit_produits&amp;produit_prescription_id={{$_produit->_id}}">
                {{$_produit->_view}}
              </a>
            </td>
          </tr>
					{{foreachelse}}
				<tr>
					<td>Aucun produit</td>
				</tr>
          {{/foreach}} 
      </table>
    </td>

    <!-- Modification de la fiche ATC sélectionnée -->
    <td>
        <!-- Affichage de la fiche ATC -->
        <form name="addEditProduit" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)" class="{{$produit->_spec}}">
          <input type="hidden" name="m" value="dPmedicament" />
          <input type="hidden" name="dosql" value="do_produit_prescription_aed" />
          <input type="hidden" name="del" value="0" />
          <input type="hidden" name="produit_prescription_id" value="{{$produit->_id}}" />
          <table class="form">
            <tr>
		          {{if $produit->_id}}
              <th class="title modify" colspan="2">
					      {{mb_include module=system template=inc_object_idsante400 object=$produit}}
					      {{mb_include module=system template=inc_object_history object=$produit}}
		            Modification du produit
						  {{else}}
              <th class="title" colspan="2">
		            Création d'un produit
		          {{/if}}
		          </th>
            </tr>
						<tr>
              <th>
                {{mb_label object=$produit field="code_cip"}} 
              </th>
              <td>
              	<input type="hidden" name="code_ucd" value="" />
								<input type="hidden" name="code_cis" value="" />
                
                {{mb_field object=$produit field="code_cip" readonly="readonly" onchange="this.form.code_ucd.value = ''; this.form.code_cis.value = '';"}}
								<button type="button" class="search" onclick="MedSelector.init('0');">Recherche groupée par CIP</button>
                <script type="text/javascript">
                  MedSelector.init = function(search_by_cis){
                    this.sForm = "addEditProduit";
                    this.sView = "libelle";
                    this.sCode = "code_cip";
										this.sCodeUCD = "code_ucd";
										this.sCodeCIS = "code_cis";
                    this.sSearchByCIS = search_by_cis;
										this.sGestionProduits = "1";
                    this.sOnglet = "produit";
                    this.selfClose = false;
                    this.pop();
                  }
                </script>
              </td>
            </tr>
						<!--
						<tr>
              <th>
                {{mb_label object=$produit field="code_ucd"}} 
              </th>
              <td>
                {{mb_field object=$produit field="code_ucd" readonly="readonly" onchange="this.form.code_cip.value = ''; this.form.code_cis.value = '';"}}
								<button type="button" class="search" onclick="MedSelector.init('1');">Recherche groupée par UCD/CIS</button>
              </td>
            </tr>
						<tr>
              <th>
                {{mb_label object=$produit field="code_cis"}} 
              </th>
              <td>
                {{mb_field object=$produit field="code_cis" readonly="readonly" onchange="this.form.code_ucd.value = ''; this.form.code_cip.value = '';"}}
              </td>
            </tr>
						-->
            <tr>
              <th>
                {{mb_label object=$produit field="libelle"}} 
              </th>
              <td>
                {{mb_field object=$produit field="libelle"}}
              </td>
            </tr>
            <tr>
              <th>
                {{mb_label object=$produit field="quantite"}} 
              </th>
              <td>
                {{mb_field object=$produit field="quantite"}}
              </td>
            </tr>
						<tr>
              <th>
                {{mb_label object=$produit field="unite_prise"}} 
              </th>
              <td>
                {{mb_field object=$produit field="unite_prise" onchange=updateUniteDispensation(this);}}
              </td>
            </tr>
						<tr>
              <th>
                {{mb_label object=$produit field="unite_dispensation"}} 
              </th>
              <td>
                {{mb_field object=$produit field="unite_dispensation"}}
              </td>
            </tr>
						<tr>
              <th>
                {{mb_label object=$produit field="nb_presentation"}} 
              </th>
              <td>
                {{mb_field object=$produit field="nb_presentation"}}
              </td>
            </tr>
            <tr>
              <th>
                {{mb_label object=$produit field="voie"}} 
              </th>
              <td>
              	<select name="voie">
              		<option value="">&mdash; Sélection d'une voie</option>
	              	{{foreach from="CPrescriptionLineMedicament"|static:"voies" key=_voie item=voie}}
									  <option value="{{$_voie}}" {{if $produit->voie == $_voie}}selected="selected"{{/if}}>{{$_voie}}</option>
									{{/foreach}}
								</select>
              </td>
            </tr>
						<tr>
							<td class="button" colspan="2" class="button">
		          {{if $produit->_id}}
		            <button class="modify" type="submit" name="modify">
		              {{tr}}Save{{/tr}}
		            </button>
		            <button class="trash" type="button" name="delete" onclick="confirmDeletion(this.form,{typeName:'le produit',objName:'{{$produit->_view|smarty:nodefaults|JSAttribute}}'})">
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
</table>