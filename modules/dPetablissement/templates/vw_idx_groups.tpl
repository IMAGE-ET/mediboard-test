{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPetablissement
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script>
reload = function(group_id){
  var url = new Url("dPetablissement", "ajax_vw_groups");
  url.addParam("group_id", group_id);
  url.requestUpdate('group_etab'); 
}
</script>

<table class="main">
  <tr>
    <td class="halfPane">
      {{if $can->edit}}
      <button onclick="reload('0')" class="new">
        Créer un établissement
      </button>
      {{/if}}
      <table class="tbl">
        <tr>
          <th>Liste des établissements</th>
          <th>Fonctions associées</th>
        </tr>
        {{foreach from=$groups item=_group}}
        <tr {{if $_group->_id == $group->_id}} class="selected" {{/if}}>
          <td>
            {{if $can->edit}}
            <a href=#" onclick="reload('{{$_group->_id}}')">
              {{$_group->text}}
            </a>
            {{else}}
            {{$_group->text}}
            {{/if}}
          </td>
          <td>
            {{if $can->edit}}
            <a href=#" onclick="reload('{{$_group->_id}}')">
              {{$_group->_ref_functions|@count}}
            </a>
            {{else}}
              {{$_group->_ref_functions|@count}}
            </a>
            {{/if}}
          </td>
        </tr>
        {{/foreach}}
      </table>
    </td>
    {{if $can->edit}}
    <td class="halfPane" id="group_etab">
      {{mb_include module=dPetablissement template=inc_vw_groups}}
    </td>
    {{/if}}
  </tr>
</table>