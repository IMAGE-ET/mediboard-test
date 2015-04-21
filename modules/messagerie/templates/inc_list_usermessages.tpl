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
  Main.add(function() {
    UserMessage.refreshCounts();
  });
</script>

<div id="user_messages_actions" style="margin: 5px;">
  {{mb_include module=messagerie template=inc_usermessages_actions}}
</div>

<table class="tbl">
  <tr>
    <th class="narrow"></th>
    <th class="narrow">{{tr}}Date{{/tr}}</th>
    <th class="narrow">
      {{if $mode == "draft" || $mode == "sentbox"}}
        {{tr}}CUserMessageDest-to_user_id{{/tr}}
      {{else}}
        {{tr}}CUserMessageDest-from_user_id{{/tr}}
      {{/if}}
    </th>
    <th>{{tr}}CUserMessage-subject{{/tr}}</th>
    <th>{{tr}}CUserMessage-_abstract{{/tr}}</th>
  </tr>
  <tr>
    <td colspan="5">
      {{mb_include module=system template=inc_pagination total=$total current=$page change_page="UserMessage.refreshListPage" step=$app->user_prefs.nbMailList}}
    </td>
  </tr>
  {{foreach from=$usermessages item=_usermessage}}
    {{assign var=usermessage_id value=$_usermessage->_id}}

    {{if $mode != 'draft'}}
      {{assign var=onclick value="UserMessage.view('$usermessage_id');"}}
    {{else}}
      {{assign var=onclick value="UserMessage.edit('$usermessage_id', null, '$inputMode', UserMessage.refreshListCallback);"}}
    {{/if}}
    <tr class="alternate message{{if !$_usermessage->_ref_dest_user->datetime_read && $mode == 'inbox'}} unread{{/if}}">
      <td class="narrow">
        <input type="checkbox" value="{{$_usermessage->_ref_dest_user->_id}}"/>
      </td>
      <td onclick="{{$onclick}}">
        {{if $_usermessage->_ref_dest_user->_id}}
          {{$_usermessage->_ref_dest_user->_datetime_sent}}
        {{elseif $mode == 'sentbox'}}
          {{assign var=dest_user value=$_usermessage->_ref_destinataires|@reset}}
          {{$dest_user->_datetime_sent}}
        {{/if}}
      </td>
      <td onclick="{{$onclick}}">
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
      <td onclick="{{$onclick}}">
        {{if $_usermessage->_ref_dest_user->_id && $_usermessage->_mode == 'in' && $_usermessage->_ref_dest_user->starred}}
          <i style="float: right; color: #ffa306; margin : 2px;" class=" fa fa-star"></i>
        {{/if}}

        <a href="#">{{$_usermessage->subject}}</a>
      </td>
      <td onclick="{{$onclick}}">
        {{mb_value object=$_usermessage field=_abstract}}
      </td>
    </tr>
  {{foreachelse}}
    <tr>
      <td colspan="6" class="empty">{{tr}}CUserMessage.none{{/tr}}</td>
    </tr>
  {{/foreach}}
  <tr>
    <td colspan="5">
      {{mb_include module=system template=inc_pagination total=$total current=$page change_page="UserMessage.refreshListPage" step=$app->user_prefs.nbMailList}}
    </td>
  </tr>
</table>
