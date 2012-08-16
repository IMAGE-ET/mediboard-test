{{*
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage dPbloc
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 *}}
 
<form name="editRessourceMaterielle" method="post" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="m" value="dPbloc" />
  <input type="hidden" name="dosql" value="do_ressource_materielle_aed"/>
  <input type="hidden" name="callback" value="Ressource.afterEditRessource" />
  <input type="hidden" name="del" value="0" />
  
  {{mb_key object=$ressource_materielle}}
  {{mb_field object=$ressource_materielle field=group_id hidden=true}}
  
  <table class="form">
    {{mb_include module=system template=inc_form_table_header object=$ressource_materielle}}
    <tr>
      <th>
        {{mb_label object=$ressource_materielle field=libelle}}
      </th>
      <td>
        {{mb_field object=$ressource_materielle field=libelle}}
      </td>
    </tr>
    <tr>
      <th>
        {{mb_label object=$ressource_materielle field=type_ressource_id}}
      </th>
      <td>
        {{mb_field object=$ressource_materielle field=type_ressource_id form="editRessourceMaterielle" autocomplete="true,2,30,false,true,1"}}
      </td>
    </tr>
    <tr>
      <th>
        {{mb_label object=$ressource_materielle field=deb_activite}}
      </th>
      <td>
        {{mb_field object=$ressource_materielle field=deb_activite form=editRessourceMaterielle register=true}}
      </td>
    </tr>
    <tr>
      <th>
        {{mb_label object=$ressource_materielle field=fin_activite}}
      </th>
      <td>
        {{mb_field object=$ressource_materielle field=fin_activite form=editRessourceMaterielle register=true}}
      </td>
    </tr>
    <tr>
      <td colspan="2" class="button">
        {{if $ressource_materielle->_id}}
          <button type="button" class="save" onclick="this.form.onsubmit()">{{tr}}Save{{/tr}}</button>
          <button type="button" class="trash" onclick="confirmDeletion(this.form, {objName: 'ressource', ajax: true})">{{tr}}Delete{{/tr}}</button>
        {{else}}
          <button type="button" class="save" onclick="this.form.onsubmit()">{{tr}}Create{{/tr}}</button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>