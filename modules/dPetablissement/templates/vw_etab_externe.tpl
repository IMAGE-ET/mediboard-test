{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPetablissement
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}
<script>
reload = function(etab_id){
  var url = new Url("dPetablissement", "ajax_etab_externe");
  url.addParam("etab_id", etab_id);
  url.requestUpdate('group_externe'); 
}
</script>

<table class="main">
  <tr>
    <td class="halfPane">
      <button class="new" onclick="reload('0')">
        Créer un établissement externe
      </button>
      <table class="tbl">
        <tr>
          <th>Liste des établissements externes</th>
        </tr>
        {{foreach from=$listEtabExternes item=curr_etab}}
        <tr {{if $curr_etab->_id == $etabExterne->_id}}class="selected"{{/if}}>
          <td>
            <a href="#" onclick="reload('{{$curr_etab->_id}}')">
              {{$curr_etab->nom}}
            </a>
          </td>
        </tr>
        {{/foreach}}
      </table>
    </td>
    
    <td class="halfPane" id="group_externe">
      {{mb_include module=etablissement template=inc_etab_externe}}
    </td>
  </tr>
</table>