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
var checkListTypes = ["normal", "endoscopie", "endoscopie-bronchique", "radio"];

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
        Check list Sécurité du Patient
      </button>
      
      <select onchange="showCheckListType($(this).up('table'), $V(this))" style="max-width: 18em;">
        {{foreach from="CDailyCheckList"|static:_HAS_lists key=_type item=_label}}
          <option value="{{$_type}}" {{if $active_list_type == $_type}} selected {{/if}}>{{$_label}}</option>
        {{/foreach}}
      </select>
      
      <img height="20" src="images/pictures/logo-has-small.png" />
      
      <button class="print" onclick="(new Url('dPsalleOp', 'print_check_list_operation')).addParam('operation_id', '{{$selOp->_id}}').popup(800, 600, 'check_list')">
        {{tr}}Print{{/tr}}
      </button>
      
      {{mb_include module=forms template=inc_widget_ex_class_register object=$selOp event_name=checklist cssStyle="display: inline-block; font-size: 0.8em;"}}
    </th>
  </tr>
  
  <tr class="normal" style="display: none;">
    <td class="button" id="preanesth-title">
      <h3 style="margin: 2px;">
        <img src="images/icons/{{$operation_check_lists.preanesth->_readonly|ternary:"tick":"cross"}}.png" />
        Avant induction anesthésique
      </h3>
      <small>Temps de pause avant anesthésie</small>
    </td>
    <td class="button" id="preop-title">
      <h3 style="margin: 2px;">
        <img src="images/icons/{{$operation_check_lists.preop->_readonly|ternary:"tick":"cross"}}.png" />
        Avant intervention chirurgicale
      </h3>
      <small>Temps de pause avant incision</small>
    </td>
    <td class="button" id="postop-title">
      <h3 style="margin: 2px;">
        <img src="images/icons/{{$operation_check_lists.postop->_readonly|ternary:"tick":"cross"}}.png" />
        Après intervention
      </h3>
      <small>Pause avant sortie de salle d'intervention</small>
    </td>
  </tr>
    
  <tr class="endoscopie" style="display: none;">
    <td class="button" id="preendoscopie-title" colspan="2">
      <h3 style="margin: 2px;">
        <img src="images/icons/{{$operation_check_lists.preendoscopie->_readonly|ternary:"tick":"cross"}}.png" />
        Avant l'endoscopie digestive
      </h3>
      <small>Avec ou sans anesthésie</small>
    </td>
    <td class="button" id="postendoscopie-title">
      <h3 style="margin: 2px;">
        <img src="images/icons/{{$operation_check_lists.postendoscopie->_readonly|ternary:"tick":"cross"}}.png" />
        Après l'endoscopie digestive
      </h3>
    </td>
  </tr>
    
  <tr class="endoscopie-bronchique" style="display: none;">
    <td class="button" id="preendoscopie_bronchique-title" colspan="2">
      <h3 style="margin: 2px;">
        <img src="images/icons/{{$operation_check_lists.preendoscopie_bronchique->_readonly|ternary:"tick":"cross"}}.png" />
        Avant l'endoscopie bronchique
      </h3>
      <small>Avec ou sans anesthésie</small>
    </td>
    <td class="button" id="postendoscopie_bronchique-title">
      <h3 style="margin: 2px;">
        <img src="images/icons/{{$operation_check_lists.postendoscopie_bronchique->_readonly|ternary:"tick":"cross"}}.png" />
        Après l'endoscopie bronchique
      </h3>
    </td>
  </tr>
  
  <tr class="radio" style="display: none;">
    <td class="button" id="preanesth_radio-title">
      <h3 style="margin: 2px;">
        <img src="images/icons/{{$operation_check_lists.preanesth_radio->_readonly|ternary:"tick":"cross"}}.png" />
        Avant anesthésie ou sédation
      </h3>
    </td>
    <td class="button" id="preop_radio-title">
      <h3 style="margin: 2px;">
        <img src="images/icons/{{$operation_check_lists.preop_radio->_readonly|ternary:"tick":"cross"}}.png" />
        Avant intervention
      </h3>
    </td>
    <td class="button" id="postop_radio-title">
      <h3 style="margin: 2px;">
        <img src="images/icons/{{$operation_check_lists.postop_radio->_readonly|ternary:"tick":"cross"}}.png" />
        Après intervention
      </h3>
    </td>
  </tr>
  
  <tbody id="check-lists" style="display: none;">
    <tr class="normal" style="display: none;">
      <td style="padding:1px;">
        <div id="check_list_preanesth_">
        {{assign var=check_list value=$operation_check_lists.preanesth}}
        {{mb_include module=salleOp template=inc_edit_check_list 
             check_item_categories=$operation_check_item_categories.preanesth
             personnel=$listValidateurs}}
        </div>
      </td>
      <td style="padding:1px;">
        <div id="check_list_preop_">
        {{assign var=check_list value=$operation_check_lists.preop}}
        {{mb_include module=salleOp template=inc_edit_check_list 
             check_item_categories=$operation_check_item_categories.preop
             personnel=$listValidateurs}}
        </div>
      </td>
      <td style="padding:1px;">
        <div id="check_list_postop_">
        {{assign var=check_list value=$operation_check_lists.postop}}
        {{mb_include module=salleOp template=inc_edit_check_list 
             check_item_categories=$operation_check_item_categories.postop
             personnel=$listValidateurs}}
        </div>
      </td>
    </tr>
    
    <tr class="endoscopie" style="display: none;">
      <td style="padding:0;" colspan="2">
        <div id="check_list_preendoscopie_">
        {{assign var=check_list value=$operation_check_lists.preendoscopie}}
        {{mb_include module=salleOp template=inc_edit_check_list 
             check_item_categories=$operation_check_item_categories.preendoscopie
             personnel=$listValidateurs}}
        </div>
      </td>
      <td style="padding:0;">
        <div id="check_list_postendoscopie_">
        {{assign var=check_list value=$operation_check_lists.postendoscopie}}
        {{mb_include module=salleOp template=inc_edit_check_list 
             check_item_categories=$operation_check_item_categories.postendoscopie
             personnel=$listValidateurs}}
        </div>
      </td>
    </tr>
    
    <tr class="endoscopie-bronchique" style="display: none;">
      <td style="padding:0;" colspan="2">
        <div id="check_list_preendoscopie_bronchique_">
        {{assign var=check_list value=$operation_check_lists.preendoscopie_bronchique}}
        {{mb_include module=salleOp template=inc_edit_check_list 
             check_item_categories=$operation_check_item_categories.preendoscopie_bronchique
             personnel=$listValidateurs}}
        </div>
      </td>
      <td style="padding:0;">
        <div id="check_list_postendoscopie_bronchique_">
        {{assign var=check_list value=$operation_check_lists.postendoscopie_bronchique}}
        {{mb_include module=salleOp template=inc_edit_check_list 
             check_item_categories=$operation_check_item_categories.postendoscopie_bronchique
             personnel=$listValidateurs}}
        </div>
      </td>
    </tr>
    
    <tr class="radio" style="display: none;">
      <td style="padding:1px;">
        <div id="check_list_preanesth_radio_">
        {{assign var=check_list value=$operation_check_lists.preanesth_radio}}
        {{mb_include module=salleOp template=inc_edit_check_list 
             check_item_categories=$operation_check_item_categories.preanesth_radio
             personnel=$listValidateurs}}
        </div>
      </td>
      <td style="padding:1px;">
        <div id="check_list_preop_radio_">
        {{assign var=check_list value=$operation_check_lists.preop_radio}}
        {{mb_include module=salleOp template=inc_edit_check_list 
             check_item_categories=$operation_check_item_categories.preop_radio
             personnel=$listValidateurs}}
        </div>
      </td>
      <td style="padding:1px;">
        <div id="check_list_postop_radio_">
        {{assign var=check_list value=$operation_check_lists.postop_radio}}
        {{mb_include module=salleOp template=inc_edit_check_list 
             check_item_categories=$operation_check_item_categories.postop_radio
             personnel=$listValidateurs}}
        </div>
      </td>
    </tr>
    
    <tr>
      <td colspan="3" class="button text">
        <hr />
        Le rôle du coordonnateur check-list sous la responsabilité du(es) chirurgien(s) et anesthésiste(s) responsables 
        de l'intervention est de ne cocher les items de la check list  que (1) si la vérification a bien été effectuée,  
        (2) si elle a été faite oralement en présence des membres de l'équipe concernée et (3) si les non conformités (marquées d'un *) 
        ont fait l'objet d'une concertation en équipe et d'une décision qui doit le cas échéant être rapportée dans l'encart spécifique.
      </td>
    </tr>
  </tbody>
</table>
