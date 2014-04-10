{{*
 * $Id$
 *  
 * @category Messagerie
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<script>
  Control.Tabs.setTabCount('{{$mode}}', {{if $mode == "inbox"}}'{{$unread}}', '{{$total}}'{{else}} '{{$total}}'{{/if}} );
</script>


{{mb_include module=system template=inc_pagination total=$total current=$page change_page="UserMessage.refreshListPage" step=$app->user_prefs.nbMailList}}


<table class="tbl">
  <tr>
    <th class="narrow"></th>
    <th class="narrow">{{if $mode == "draft" || $mode == "sentbox"}}À{{else}}De{{/if}}</th>
    <th>Subject</th>
    <th class="narrow">{{if $mode == "draft" || $mode == "sentbox"}}Envoyé{{else}}Reçu{{/if}}</th>
    <th class="narrow">Lecture</th>
    <th class="narrow"></th>
  </tr>
  {{foreach from=$usermessages item=_usermessage}}
    <tr>
      <td>
        {{if $_usermessage->_mode == "in"}}  <!-- reception -->
          <a href="#" style="display: inline" onclick="UserMessage.editAction('{{$_usermessage->_ref_dest_user->_id}}', 'star', '{{if $_usermessage->_ref_dest_user->starred}}0{{else}}1{{/if}}')" title="{{tr}}CUserMessageDest-title-to_star-{{$_usermessage->_ref_dest_user->starred}}{{/tr}}">
            <img src="modules/messagerie/images/favorites-{{$_usermessage->_ref_dest_user->starred}}.png" alt="" style="height: 20px;" />
          </a>
            <a href="#" style="display: inline" onclick="UserMessage.editAction('{{$_usermessage->_ref_dest_user->_id}}', 'archive', '{{if $_usermessage->_ref_dest_user->archived}}0{{else}}1{{/if}}')" title="{{tr}}CUserMessageDest-title-to_archive-{{$_usermessage->_ref_dest_user->archived}}{{/tr}}">
              <img src="modules/messagerie/images/{{if $_usermessage->_ref_dest_user->archived}}mail_archive_cancel{{else}}mail_archive{{/if}}.png" alt="" style="height: 20px;" />
            </a>
        {{/if}}
      </td>
      <td>
        {{if $_usermessage->_mode == "out"}}  <!-- envoi -->
          {{foreach from=$_usermessage->_ref_destinataires item=_dest}}
            {{if $_dest->_id}}
              {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_dest->_ref_user_to}}<br/>
            {{/if}}
          {{/foreach}}
        {{else}}    <!-- reception -->
          {{if $_usermessage->_ref_dest_user->_id && $_usermessage->_ref_dest_user->_ref_user_from->_id}}
            {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_usermessage->_ref_dest_user->_ref_user_from}}
          {{/if}}
        {{/if}}
      </td>
      <td {{if !$_usermessage->_ref_dest_user->datetime_read && $mode == "inbox"}}style="font-weight: bold;" {{/if}}>
        <a href="#" onclick="UserMessage.edit('{{$_usermessage->_id}}', null, UserMessage.refreshListCallback);">{{$_usermessage->subject}}</a>
      </td>
      <td>
        {{if $_usermessage->_ref_dest_user->_id}}
          {{mb_value object=$_usermessage->_ref_dest_user field=datetime_sent format=relative}}
        {{/if}}
      </td>
      <td>
        {{if $_usermessage->_ref_dest_user->_id}}
          {{mb_value object=$_usermessage->_ref_dest_user field=datetime_read format=relative}}
        {{/if}}
      </td>
      <td>
        Actions
      </td>
    </tr>
  {{foreachelse}}
    <tr>
      <td colspan="6" class="empty">{{tr}}CUserMessage.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>

{{mb_include module=system template=inc_pagination total=$total current=$page change_page="UserMessage.refreshListPage" step=$app->user_prefs.nbMailList}}
