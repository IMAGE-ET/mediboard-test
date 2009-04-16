{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage bloodSalvage
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<!-- Plages -->
{{foreach from=$praticien->_ref_plages item=_plage}}
<hr />

<table class="form">
  <tr>
    <th class="category{{if $vueReduite}} text{{/if}}" colspan="2">
      <a href="?m=dPbloc&amp;tab=vw_edit_interventions&amp;plageop_id={{$_plage->_id}}" title="Administrer la plage">
        {{$_plage->_ref_salle->_view}}
        {{if $vueReduite}}
          <br />
        {{else}}
          -
        {{/if}}
        {{$_plage->debut|date_format:$dPconfig.datetime}} à 
        {{$_plage->fin|date_format:$dPconfig.time}}
      </a>
    </th>
  </tr>
</table>

<table class="tbl">
  {{if $_plage->_ref_operations|@count}}
  {{include file="../../dPsalleOp/templates/inc_liste_operations.tpl" urgence=0 operations=$_plage->_ref_operations}}
  {{/if}}
  
  {{if $_plage->_unordered_operations|@count}}
  <tr>
    <th colspan="10">Non placées</th>
  </tr>
  {{include file="../../dPsalleOp/templates/inc_liste_operations.tpl" urgence=0 operations=$_plage->_unordered_operations}}
  {{/if}}
</table>
{{/foreach}}

<!-- Déplacées -->
{{if $praticien->_ref_deplacees|@count}}
<hr />

<table class="form">
  <tr>
    <th class="category" colspan="2">
      Déplacées
    </th>
  </tr>
</table>

<table class="tbl">
  {{include file="../../bloodSalvage/templates/inc_liste_operations.tpl" urgence=1 operations=$praticien->_ref_deplacees}}
</table>
{{/if}}

<!-- Urgences -->
{{if $praticien->_ref_urgences|@count}}
<hr />

<table class="form">
  <tr>
    <th class="category" colspan="2">
      Urgences
    </th>
  </tr>        
</table>

<table class="tbl">
  {{include file="../../bloodSalvage/templates/inc_liste_operations.tpl" urgence=1 operations=$praticien->_ref_urgences}}
</table>
{{/if}}