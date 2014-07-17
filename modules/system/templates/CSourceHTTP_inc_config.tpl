{{* $Id: configure_ftp.tpl 6239 2009-05-07 10:26:49Z phenxdesign $ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 6239 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="main"> 
  <tr>
    <td>
      <form name="editSourceHTTP-{{$source->name}}" action="?m={{$m}}" method="post"
            onsubmit="return onSubmitFormAjax(this, { onComplete : (function() {
              if (this.up('.modal')) {
              Control.Modal.close();
              } else {
              ExchangeSource.refreshExchangeSource('{{$source->name}}', '{{$source->_wanted_type}}');
              }}).bind(this)})">

        <input type="hidden" name="m" value="system" />
        <input type="hidden" name="dosql" value="do_source_http_aed" />
        <input type="hidden" name="source_http_id" value="{{$source->_id}}" />
        <input type="hidden" name="del" value="0" />

        <fieldset>
          <legend>{{tr}}CSourceHTTP{{/tr}}</legend>

          <table class="main form">
          
            {{mb_include module=system template=CExchangeSource_inc}}

            <tr>
              <td class="button" colspan="2">
                {{if $source->_id}}
                  <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
                  <button class="trash" type="button" onclick="confirmDeletion(this.form,
                    { ajax: 1, typeName: '', objName: '{{$source->_view}}'},
                    { onComplete: (function() {
                    if (this.up('.modal')) {
                      Control.Modal.close();
                    } else {
                      ExchangeSource.refreshExchangeSource('{{$source->name}}', '{{$source->_wanted_type}}');
                    }}).bind(this.form)})">

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
    </td>
  </tr>
</table>