{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPetablissement
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module=patients script=autocomplete}}

<script type="text/javascript">
Group = {
  edit: function(group_id) {
    var url = new Url('etablissement', 'ajax_vw_groups');
    url.addParam('group_id', group_id);
    url.requestUpdate('group_etab');
    
    var row = $('row-CGroups-'+group_id);
    if (row) {
      row.addUniqueClassName('selected');
    }  
  }
};

Main.add(Group.edit.curry('{{$group_id}}'));
</script>

<table class="main">
  <tr>
    <td class="halfPane">
      {{if $can->edit}}
      <button onclick="Group.edit('0')" class="new">
        {{tr}}CGroups-title-create{{/tr}}
      </button>
      {{/if}}
      
      <table class="tbl">
        <tr>
          <th>Liste des établissements</th>
          <th>Fonctions associées</th>
        </tr>
        {{foreach from=$groups item=_group}}
        <tr id="row-{{$_group->_guid}}">
          <td>
            {{if $can->edit}}
            <a href="#{{$_group->_guid}}" onclick="Group.edit('{{$_group->_id}}')">
              {{$_group->text}}
            </a>
            {{else}}
              {{$_group->text}}
            {{/if}}
          </td>
          <td>
            {{$_group->_ref_functions|@count}}
          </td>
        </tr>
        {{/foreach}}
      </table>
    </td>
    {{if $can->edit}}
    <td class="halfPane" id="group_etab">
    </td>
    {{/if}}
  </tr>
</table>
