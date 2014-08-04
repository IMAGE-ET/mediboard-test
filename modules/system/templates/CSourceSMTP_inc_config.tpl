{{* $Id: configure_ftp.tpl 6239 2009-05-07 10:26:49Z phenxdesign $ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 6239 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module=system script=smtp ajax=true}}
{{mb_script module=system script=exchange_source ajax=true}}

{{mb_default var=light value=""}}

<script type="text/javascript">
guessDataFormEmail = function(element){
  var email = $V(element).match(/^([^@]+)@(.*)$/);
  if (!email) return;
  
  var form = element.form;
  
  if (!$V(form.elements.host))
    $V(form.elements.host, "smtp."+email[2]);
  
  if (!$V(form.elements.user))
    $V(form.elements.user, email[1]);
}
</script>

<table class="main"> 
  <tr>
    <td>
      <form name="editSourceSMTP-{{$source->name}}" action="?m={{$m}}" method="post"
            onsubmit="return onSubmitFormAjax(this, { onComplete : (function() {
              if (this.up('.modal')) {
                Control.Modal.close();
              } else {
                ExchangeSource.refreshExchangeSource('{{$source->name}}', '{{$source->_wanted_type}}');
              }}).bind(this)})">

        <input type="hidden" name="m" value="system" />
        <input type="hidden" name="dosql" value="do_source_smtp_aed" />
        <input type="hidden" name="source_smtp_id" value="{{$source->_id}}" />
        <input type="hidden" name="del" value="0" />

        <input type="hidden" name="callback" value="" />

        <fieldset>
          <legend>{{tr}}CSourceSMTP{{/tr}}</legend>

          <table class="form">
            {{mb_include module=system template=CExchangeSource_inc}}

            <tr>
              <th>{{mb_label object=$source field="email"}}</th>
              <td>{{mb_field object=$source field="email" onchange="guessDataFormEmail(this)" size="50"}}</td>
            </tr>
            <tr>
              <th>{{mb_label object=$source field="user"}}</th>
              <td>{{mb_field object=$source field="user" size="50"}}</td>
            </tr>
            <tr>
              <th>{{mb_label object=$source field="password"}}</th>
              {{assign var=placeholder value="Pas de mot de passe"}}
              {{if $source->password}}
                {{assign var=placeholder value="Mot de passe enregistré"}}
              {{/if}}
              <td>{{mb_field object=$source field="password" placeholder=$placeholder size="30"}}</td>
            </tr>
            <tr>
              <th>{{mb_label object=$source field="port"}}</th>
              <td>{{mb_field object=$source field="port"}}</td>
            </tr>
            <tr>
              <th>{{mb_label object=$source field="ssl"}}</th>
              <td>{{mb_field object=$source field="ssl"}}</td>
            </tr>
            <tr>
              <th>{{mb_label object=$source field="auth"}}</th>
              <td>{{mb_field object=$source field="auth"}}</td>
            </tr>
            <tr {{if !$can->admin}}style="display:none;"{{/if}}>
              <th>{{mb_label object=$source field="timeout"}}</th>
              <td>{{mb_field object=$source field="timeout" register=true increment=true form="editSourceSMTP-`$source->name`" size=3 step=1 min=0}}</td>
            </tr>
            <tr {{if !$can->admin}}style="display:none;"{{/if}}>
              <th>{{mb_label object=$source field="debug"}}</th>
              <td>{{mb_field object=$source field="debug"}}</td>
            </tr>

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

  <tr>
     <td>
        {{if !$light}}
          <fieldset>
            <legend>{{tr}}utilities-source-smtp{{/tr}}</legend>

            <table class="main tbl">
              <!-- Test de connexion pop -->
              <tr>
                <td class="button">
                  {{mb_include module=system template=CSourceSMTP_tools_inc _source=$source}}
                </td>
              </tr>
            </table>
          </fieldset>
        </td>
        {{/if}}
    </td>
  </tr>
</table>