{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPsalleOp
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="main form">
  <col style="width: 33%" />
  <col style="width: 33%" />
  <col style="width: 33%" />
  
  <tr>
    <th colspan="10" class="title">
       <button class="down" onclick="$('check-lists').toggle(); $(this).toggleClassName('down').toggleClassName('up')">
      Check list Sécurité du patient au bloc opératoire
    </th>
  </tr>
  
  <tr>
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
  <tr id="check-lists" style="display: none;">
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
</table>
