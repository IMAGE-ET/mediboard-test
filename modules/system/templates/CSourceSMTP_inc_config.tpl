{{* $Id: configure_ftp.tpl 6239 2009-05-07 10:26:49Z phenxdesign $ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 6239 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

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
         onsubmit="return onSubmitFormAjax(this, { 
           onComplete: refreshExchangeSource.curry('{{$source->name}}', '{{$source->_wanted_type}}') } )">
        <input type="hidden" name="m" value="system" />
        <input type="hidden" name="dosql" value="do_source_smtp_aed" />
        <input type="hidden" name="source_smtp_id" value="{{$source->_id}}" />
        <input type="hidden" name="del" value="0" /> 
           
        <table class="form">        
          <tr>
            <th class="category" colspan="2">
              {{tr}}config-source-smtp{{/tr}}
            </th>
          </tr>
          {{mb_include module=system template=CExchangeSource_inc}}
          <tr>
            <th>{{mb_label object=$source field="email"}}</th>
            <td>{{mb_field object=$source field="email" onchange="guessDataFormEmail(this)"}}</td>
          </tr>
          <tr>
            <th>{{mb_label object=$source field="user"}}</th>
            <td>{{mb_field object=$source field="user"}}</td>
          </tr>
          <tr>
            <th>{{mb_label object=$source field="password"}}</th>
            <td>{{mb_field object=$source field="password"}}</td>
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
          <tr>
            <th>{{mb_label object=$source field="timeout"}}</th>
            <td>{{mb_field object=$source field="timeout"}}</td>
          </tr>
          <tr>
            <th>{{mb_label object=$source field="debug"}}</th>
            <td>{{mb_field object=$source field="debug"}}</td>
          </tr>
          
          <tr>
            <td class="button" colspan="2">
              {{if $source->_id}}
                <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
                <button type="button" class="trash" onclick="confirmDeletion(this.form, {ajax:1, typeName:'',
                  objName:'{{$source->_view|smarty:nodefaults|JSAttribute}}'}, 
                  {onComplete: refreshExchangeSource.curry('{{$source->name}}', '{{$source->_wanted_type}}')})">
                  {{tr}}Delete{{/tr}}
                </button>
              {{else}}  
                <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
              {{/if}}
            </td>
          </tr>
        </table>
      </form>
    </td>
    <td class="greedyPane">
      <script type="text/javascript">
        SMTP = {
          connexion: function (exchange_source_name) {
            var url = new Url("system", "ajax_connexion_smtp");
            url.addParam("exchange_source_name", exchange_source_name);
            url.addParam("type_action", "connexion");
            url.requestUpdate("utilities-source-smtp-connexion-" + exchange_source_name);
          },
          
          envoi: function (exchange_source_name) {
            var url = new Url("system", "ajax_connexion_smtp");
            url.addParam("exchange_source_name", exchange_source_name);
            url.addParam("type_action", "envoi");
            url.requestUpdate("utilities-source-smtp-envoi-" + exchange_source_name);
          }
        }
      </script>
      <table class="tbl">
        <tr>
          <th class="category" colspan="100">
            {{tr}}utilities-source-smtp{{/tr}}
          </th>
        </tr>
        
        <!-- Test d'envoi SMTP -->
        <tr>
          <td>
            <button type="button" class="search" onclick="SMTP.connexion('{{$source->name}}');">
              {{tr}}utilities-source-smtp-connexion{{/tr}}
            </button>
          </td>
        </tr>
        <tr>
          <td id="utilities-source-smtp-connexion-{{$source->name}}" class="text"></td>
        </tr>
        
        <!-- Liste des fichiers -->
        <tr>
          <td>
            <button type="button" class="search" onclick="SMTP.envoi('{{$source->name}}');">
              {{tr}}utilities-source-smtp-envoi{{/tr}}
            </button> 
          </td>
        </tr>
        <tr>
          <td id="utilities-source-smtp-envoi-{{$source->name}}" class="text"></td>
        </tr>
      </table>
    </td>
  </tr>
</table>