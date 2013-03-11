{{*
  * View of sectorisation rules
  *  
  * @category PlanningOp
  * @package  Mediboard
  * @author   SARL OpenXtrem <dev@openxtrem.com>
  * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
  * @version  SVN: $Id:$ 
  * @link     http://www.mediboard.org
*}}


<script>
  editSRF = function(id_rule) {
    var url = new Url("planningOp", "ajax_edit_rule_sectorisation");
    url.addParam("rule_id", id_rule);
    url.requestModal();
  }
</script>


{{if !$active}}
<div class="small-warning">
  Les règles de sectorisation ne sont pas actives, pour les activer, Cliquez sur l'onglet Configurer/ Séjour
</div>
{{/if}}


<button class="new" onclick="editSRF('')">{{tr}}Add{{/tr}}</button>
<table class="tbl">
  <tr>
    <th>Actions</th>
    <th>Etablissement</th>
    <th>Function</th>
    <th>praticien</th>
    <th>duree min</th>
    <th>duree max</th>
    <th>date début</th>
    <th>date fin</th>
    <th>type d'admission</th>
    <th>type de prise en charge</th>
    <th>Direction</th>

  </tr>
  {{foreach from=$regles item=_regle}}
    <tr>
      <td><button class="edit" onclick="editSRF('{{$_regle->_id}}')">
        {{tr}}Edit{{/tr}}
      </button></td>
      <td>{{$_regle->_ref_group}}</td>
      <td>{{mb_include module="mediusers" template="inc_vw_function" function=$_regle->_ref_function}}</td>
      <td>{{mb_include module="mediusers" template="inc_vw_mediuser" mediuser=$_regle->_ref_praticien}}</td>
      <td>{{mb_value object=$_regle field=duree_min}}</td>
      <td>{{mb_value object=$_regle field=duree_max}}</td>
      <td>{{mb_value object=$_regle field=date_min}}</td>
      <td>{{mb_value object=$_regle field=date_max}}</td>
      <td>{{if $_regle->type_admission}}{{tr}}CSejour._type_admission.{{$_regle->type_admission}}{{/tr}}{{/if}}</td>
      <td>{{if $_regle->type_pec}}{{tr}}CSejour.type_pec.{{$_regle->type_pec}}{{/tr}}{{/if}}</td>
      <td><strong> >> {{$_regle->_ref_service}}</strong></td>
    </tr>
  {{foreachelse}}
    <tr>
      <td class="empty" colspan="11">{{tr}}CRegleSectorisation.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>