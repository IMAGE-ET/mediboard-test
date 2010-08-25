{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPsalleOp
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=active_list_type value=null}}
{{assign var=types value="CDailyCheckList"|static:types}}

{{foreach from=$operation_check_lists item=_check_list key=_type}}
  {{if $_check_list->_readonly && !$active_list_type}}
    {{assign var=active_list_type value=$types.$_type}}
  {{/if}}
{{/foreach}}

<script type="text/javascript">
var checkListTypes = ["normal", "endoscopie"];

function showCheckListType(element, type) {
  checkListTypes.each(function(t){
    if (t != type)
      element.select('tr.'+t).invoke("hide");
  });
  
  element.select('tr.'+type).invoke("show");
}

Main.add(function(){
  showCheckListType($("checkList-container"), "{{$active_list_type}}" || "normal");
});
</script>

<table class="main form" id="checkList-container">
  <col style="width: 33%" />
  <col style="width: 33%" />
  <col style="width: 33%" />
  
  <tr>
    <th colspan="10" class="title">
      <button class="down" onclick="$('check-lists').toggle(); $(this).toggleClassName('down').toggleClassName('up')">
        Check list
      </button>
      
      <select onchange="showCheckListType($(this).up('table'), $V(this))">
        <option value="normal" {{if $active_list_type == "normal"}} selected="selected" {{/if}}>Sécurité du patient au bloc opératoire</option>
        <option value="endoscopie" {{if $active_list_type == "endoscopie"}} selected="selected" {{/if}}>Sécurité du patient en endoscopie digestive</option>
      </select>
    </th>
  </tr>
  
  <tr class="normal" style="display: none;">
    <td class="button" id="preanesth-title">
      <h3 style="margin: 4px;">
        <img src="images/icons/{{$operation_check_lists.preanesth->_readonly|ternary:"tick":"cross"}}.png" />
        Avant induction anesthésique
      </h3>
      Temps de pause avant anesthésie
    </td>
    <td class="button" id="preop-title">
      <h3 style="margin: 4px;">
        <img src="images/icons/{{$operation_check_lists.preop->_readonly|ternary:"tick":"cross"}}.png" />
        Avant intervention chirurgicale
      </h3>
      Temps de pause avant incision
    </td>
    <td class="button" id="postop-title">
      <h3 style="margin: 4px;">
        <img src="images/icons/{{$operation_check_lists.postop->_readonly|ternary:"tick":"cross"}}.png" />
        Après intervention
      </h3>
      Pause avant sortie de salle d'intervention
    </td>
  </tr>
    
  <tr class="endoscopie" style="display: none;">
    <td class="button" id="preendoscopie-title" colspan="2">
      <h3 style="margin: 4px;">
        <img src="images/icons/{{$operation_check_lists.preendoscopie->_readonly|ternary:"tick":"cross"}}.png" />
        Avant l'endoscopie
      </h3>
      Avec ou sans anesthésie
    </td>
    <td class="button" id="postendoscopie-title">
      <h3 style="margin: 4px;">
        <img src="images/icons/{{$operation_check_lists.postendoscopie->_readonly|ternary:"tick":"cross"}}.png" />
        Après l'endoscopie
      </h3>
    </td>
  </tr>
  
  <tbody id="check-lists" style="display: none;">
    <tr class="normal" style="display: none;">
      <td style="padding:0;">
        <div id="preanesth">
        {{assign var=check_list value=$operation_check_lists.preanesth}}
        {{mb_include module=dPsalleOp template=inc_edit_check_list 
             check_item_categories=$operation_check_item_categories.preanesth
             personnel=$listValidateurs}}
        </div>
      </td>
      <td style="padding:0;">
        <div id="preop">
        {{assign var=check_list value=$operation_check_lists.preop}}
        {{mb_include module=dPsalleOp template=inc_edit_check_list 
             check_item_categories=$operation_check_item_categories.preop
             personnel=$listValidateurs}}
        </div>
      </td>
      <td style="padding:0;">
        <div id="postop">
        {{assign var=check_list value=$operation_check_lists.postop}}
        {{mb_include module=dPsalleOp template=inc_edit_check_list 
             check_item_categories=$operation_check_item_categories.postop
             personnel=$listValidateurs}}
        </div>
      </td>
    </tr>
    
    <tr class="endoscopie" style="display: none;">
      <td style="padding:0;" colspan="2">
        <div id="preendoscopie">
        {{assign var=check_list value=$operation_check_lists.preendoscopie}}
        {{mb_include module=dPsalleOp template=inc_edit_check_list 
             check_item_categories=$operation_check_item_categories.preendoscopie
             personnel=$listValidateurs}}
        </div>
      </td>
      <td style="padding:0;">
        <div id="postendoscopie">
        {{assign var=check_list value=$operation_check_lists.postendoscopie}}
        {{mb_include module=dPsalleOp template=inc_edit_check_list 
             check_item_categories=$operation_check_item_categories.postendoscopie
             personnel=$listValidateurs}}
        </div>
      </td>
    </tr>
    
    <tr>
      <td colspan="3" class="button">
        <hr />
        La check-list a pour but de vérifier que les différents points critiques 
        ont été pris en compte et que les mesures adéquates ont été prises.<br />
        La réponse "oui" à un item valide sa vérification croisée au sein de l'équipe. 
        Si cette vérification n'a pu être réalisée, la réponse "non" doit être cochée.<br />
        Abréviations utilisées : C/L : Check-list - N/A : Non Applicable - N/R : Non Recommandé
      </td>
    </tr>
  </tbody>
</table>
