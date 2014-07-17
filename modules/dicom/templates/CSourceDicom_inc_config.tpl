{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dicom
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module=dicom  script=dicom ajax=true}}

<table class="main">
  <tr>
    <td>
      <form name="editSourceDicom-{{$source->name}}" action="?m={{$m}}" method="post"
            onsubmit="return onSubmitFormAjax(this, { onComplete : (function() {
              if (this.up('.modal')) {
              Control.Modal.close();
              } else {
              ExchangeSource.refreshExchangeSource('{{$source->name}}', '{{$source->_wanted_type}}');
              }}).bind(this)})">

        {{mb_class object=$source}}
        {{mb_key   object=$source}}
        <input type="hidden" name="m" value="dicom" />
        <input type="hidden" name="dosql" value="do_source_dicom_aed" />
        <input type="hidden" name="del" value="0" />

        <fieldset>
          <legend>{{tr}}CSourceDicom{{/tr}}</legend>

          <table class="form">
            <tr>
              {{mb_include module=system template=CExchangeSource_inc}}
            </tr>
            <tr>
              <th>{{mb_label object=$source field="port"}}</th>
              <td>{{mb_field object=$source field="port"}}</td>
            </tr>
            <tr>
              <td class="button" colspan="2">
                {{if $source->_id}}
                  <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
                  <button class="trash" type="button" onclick="confirmDeletion(this.form,
                  {ajax: 1, typeName: '', objName: '{{$source->_view}}'},
                  {onComplete: refreshExchangeSource.curry('{{$source->name}}', '{{$source->_wanted_type}}')})">
                    {{tr}}Delete{{/tr}}
                  </button>
                {{else}}
                  <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
                {{/if}}
              </td>
            </tr>
          </table>
        </fieldset>
      </form>

      <fieldset>
        <legend>{{tr}}CSourceDicom{{/tr}}</legend>

        <table class="main form">
          <tr>
            <td class="button">
              <button type="button" class="search" onclick="Dicom.send();"
                      {{if !$source->_id}}disabled{{/if}}>
                {{tr}}utilities-source-dicom-send{{/tr}}
              </button>
            </td>
          </tr>
        </table>
      </fieldset>
    </td>
  </tr>
</table>
