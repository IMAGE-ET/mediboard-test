{{*
 * $Id$
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}
{{assign var=form_name value="editExchange_`$exchange->_id`"}}
<form name="{{$form_name}}" method="post" onsubmit="return onSubmitFormAjax(this, function () {
                                                      ExchangeDataFormat.refreshExchange('{{$exchange->_guid}}');
                                                      Control.Modal.close();
                                                      })">
  {{mb_key   object=$exchange}}
  {{mb_class object=$exchange}}
  {{mb_field object=$exchange field="reprocess" hidden=true}}
  <table class="form">
    <tr>
      <th class="title modify text" colspan="2">
        {{mb_include module=system template=inc_object_idsante400 object=$exchange}}
        {{mb_include module=system template=inc_object_history object=$exchange}}
        {{tr}}{{$exchange->_class}}-title-modify{{/tr}} '{{$exchange->_view}}'
      </th>
    </tr>
    <tr>
      <th>{{mb_label object=$exchange field="date_production"}}</th>
      <td>{{mb_field object=$exchange field="date_production" register=true form=$form_name}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$exchange field="date_echange"}}</th>
      <td>
        {{mb_field object=$exchange field="date_echange" register=true form=$form_name}}
        <button type="button" class="cancel notext" onclick="$V(this.form.date_echange, '');$V(this.form.date_echange_da, '')">
          {{tr}}cancel{{/tr}}
        </button>
      </td>
    </tr>
    <tr>
      <th>{{mb_label object=$exchange field="message_valide"}}</th>
      <td>{{mb_field object=$exchange field="message_valide"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$exchange field="acquittement_valide"}}</th>
      <td>{{mb_field object=$exchange field="acquittement_valide"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$exchange field="reprocess"}}({{mb_value object=$exchange field="reprocess"}})</th>
      <td><button type="button" class="erase oneclick" onclick="$V(this.form.reprocess, '0')">{{tr}}Erase{{/tr}}</button></td>
    </tr>
    <tr>
      <td class="button" colspan="2">
        <button type="button" class="cancel" onclick="Control.Modal.close();">{{tr}}Cancel{{/tr}}</button>
        <button type="submit" class="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>