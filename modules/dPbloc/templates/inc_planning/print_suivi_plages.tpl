{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<!-- Plages -->
{{foreach from=$salle->_ref_plages item=_plage}}
<hr />
<table class="form">
  <tr>
    <th class="category{{if $vueReduite}} text{{/if}}" colspan="2">
        Chir : Dr {{$_plage->_ref_chir->_view}}
        {{if $vueReduite}}
          <br />
        {{else}}
          -
        {{/if}}
        {{$_plage->debut|date_format:$dPconfig.time}} ? {{$_plage->fin|date_format:$dPconfig.time}}
    </th>
  </tr>
  <tr>
    <th class="category" colspan="2">
      {{if $_plage->anesth_id}}
        Anesth : Dr {{$_plage->_ref_anesth->_view}}
      {{else}}
        -
      {{/if}}
    </th>
  </tr>
</table>

<table class="tbl">
  {{if $_plage->_ref_operations}}
  {{include file="./inc_planning/print_suivi_operations.tpl" urgence=0 operations=$_plage->_ref_operations}}
  {{/if}}

  {{if $_plage->_unordered_operations}}
  <tr>
    <th colspan="10">Non placées</th>
  </tr>
  {{include file="./inc_planning/print_suivi_operations.tpl" urgence=0 operations=$_plage->_unordered_operations}}
  {{/if}}
</table>
{{/foreach}}

<!-- DÈplacÈes -->
{{if $salle->_ref_deplacees|@count}}
<hr />
<table class="form">
  <tr>
    <th class="category" colspan="2">
      Déplacées
    </th>
  </tr>
</table>
<table class="tbl">
  {{include file="./inc_planning/print_suivi_operations.tpl" urgence=1 operations=$salle->_ref_deplacees}}
</table>
{{/if}}

<!-- Urgences -->
{{if $salle->_ref_urgences|@count}}
<hr />
<table class="form">
  <tr>
    <th class="category" colspan="2">
      Urgences
    </th>
  </tr>        
</table>
<table class="tbl">
  {{include file="./inc_planning/print_suivi_operations.tpl" urgence=1 operations=$salle->_ref_urgences}}
</table>
{{/if}}