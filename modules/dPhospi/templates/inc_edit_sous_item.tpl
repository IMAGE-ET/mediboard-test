{{*
 * $Id$
 *  
 * @category Hospitalisation
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

{{assign var=item_prestation value=$sous_item->_ref_item_prestation}}

<form name="editSousItem" method="post" onsubmit="return false;">
  {{mb_class object=$sous_item}}
  {{mb_key   object=$sous_item}}
  {{mb_field object=$sous_item field=item_prestation_id hidden=1}}

  <table class="form">
    {{mb_include module=system template=inc_form_table_header object=$sous_item}}

    <tr>
      <th>{{mb_label object=$sous_item field="nom"}}</th>
      <td>{{mb_field object=$sous_item field="nom"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$sous_item field="niveau"}}</th>
      <td>{{mb_field object=$sous_item field="niveau"}}</td>
    </tr>
    <tr>
      <td class="button" colspan="2">
        <button type="button" class="save" onclick="onSubmitFormAjax(this.form, function() {
          Control.Modal.close();
          refreshItems('{{$item_prestation->object_class}}', '{{$item_prestation->object_id}}', '{{$item_prestation->_id}}'); })" >
          {{if $sous_item->_id}}
            {{tr}}Save{{/tr}}
          {{else}}
            {{tr}}Create{{/tr}}
          {{/if}}
        </button>
      </td>
    </tr>
  </table>
</form>