{{*
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage dPbloc
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 *}}

<form name="editIndispo" method="post" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="m" value="dPbloc" />
  <input type="hidden" name="dosql" value="do_indispo_ressource_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="callback" value="Indispo.afterEditIndispo" />
  
  {{mb_key object=$indispo}}
  
  <table class="form">
    {{mb_include module=system template=inc_form_table_header object=$indispo}}
    
    <tr>
      <th>
        {{mb_label object=$indispo field=ressource_materielle_id}}
      </th>
      <td>
        {{mb_field object=$indispo field=ressource_materielle_id form="editIndispo" autocomplete="true,2,30,false,true,1"}}
      </td>
    </tr>
    <tr>
      <th>
        {{mb_label object=$indispo field=deb}}  
      </th>
      <td>
        {{mb_field object=$indispo field=deb form=editIndispo register=true}}
      </td>
    </tr>
    <tr>
      <th>
        {{mb_label object=$indispo field=fin}}  
      </th>
      <td>
        {{mb_field object=$indispo field=fin form=editIndispo register=true}}
      </td>
    </tr>
    <tr>
      <th>
        {{mb_label object=$indispo field=commentaire}}  
      </th>
      <td>
        {{mb_field object=$indispo field=commentaire}}
      </td>
    </tr>
    <tr>
      <td class="button" colspan="2">
        {{if $indispo->_id}}
          <button type="button" class="save" onclick="this.form.onsubmit()">{{tr}}Save{{/tr}}</button>
          <button type="button" class="trash" onclick="confirmDeletion(this.form, {objName: '{{$indispo}}', ajax: 1})">{{tr}}Delete{{/tr}}</button>
        {{else}}
          <button type="button" class="save" onclick="this.form.onsubmit()">{{tr}}Create{{/tr}}</button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>