<script type="text/javascript">

UserMessage.refresh = window.location.reload;

Main.add(function () {
  Control.Tabs.create("tab-usermessages", true);
});

</script>

<table class="main">
  <tr>
    <td colspan="2">
      <a class="button edit" href="#nothing" onclick="UserMessage.create()">
        {{tr}}CUserMessage-title-create{{/tr}}
      </a>
    </td>
  </tr>
  <tr>
    <td style="vertical-align: top;" class="narrow">
      <ul id="tab-usermessages" class="control_tabs_vertical">
        <li>
          {{assign var=count value=$listInbox|@count}}
          <a href="#inbox" style="white-space: nowrap;" {{if !$count}}class="empty"{{/if}}>
        		{{tr}}CUserMessage-inbox{{/tr}}
        		<small>({{$count}})</small>
        	</a>
        </li>
        <li>
          {{assign var=count value=$listArchived|@count}}
          <a href="#archive" style="white-space: nowrap;" {{if !$count}}class="empty"{{/if}}>
        		{{tr}}CUserMessage-archive{{/tr}}
        		<small>({{$count}})</small>
        	</a>
        </li>
        <li>
          {{assign var=count value=$listSent|@count}}
          <a href="#sentbox" style="white-space: nowrap;" {{if !$count}}class="empty"{{/if}}>
        		{{tr}}CUserMessage-sentbox{{/tr}}
        		<small>({{$count}})</small>
        	</a>
        </li>
        <li>
          {{assign var=count value=$listDraft|@count}}
          <a href="#draft" style="white-space: nowrap;" {{if !$count}}class="empty"{{/if}}>
        		{{tr}}CUserMessage-draft{{/tr}}
        		<small>({{$count}})</small>
        	</a>
        </li>
      </ul>
    </td>

    <td>
      <!-- INBOX -->
      <table class="main tbl" id="inbox" style="display: none;">
	      <tr>
	        <th class="title" colspan="10">{{tr}}CUserMessage-inbox{{/tr}}</th>
	      </tr>

	      <tr>
	        <th>{{mb_title class=CUserMessage field=from}}</th>
	        <th>{{mb_title class=CUserMessage field=subject}}</th>
	        <th>{{mb_title class=CUserMessage field=date_sent}}</th>
	        <th>{{mb_title class=CUserMessage field=date_read}}</th>
	        <th>{{tr}}Action{{/tr}}</th>
	      </tr>
	      {{foreach from=$listInbox item=_mail}}

	      <tr {{if !$_mail->date_read}}style="font-weight: bold;"{{/if}}>
	        <td class="text">{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_mail->_ref_user_from}}</td>
	        <td class="text">{{$_mail->subject}}</td>
	        <td>{{mb_value object=$_mail field=date_sent format=relative}}</td>
	        <td>{{mb_value object=$_mail field=date_read format=relative}}</td>
	        <td>
            <button class="search" onclick="UserMessage.edit({{$_mail->_id}})" >{{tr}}CUserMessage.read{{/tr}}</button>
            <button class="mail" onclick="UserMessage.create({{$_mail->_ref_user_from->_id}}, 'Re: {{$_mail->_clean_subject}}')">{{tr}}CUserMessage.answer{{/tr}}</button>
            <form name="archive_usermessage_{{$_mail->_id}}" method="post">
              <input type="hidden" name="m" value="{{$m}}"/>
              <input type="hidden" name="dosql" value="do_usermessage_aed"/>
              <input type="hidden" name="archived" value="1"/>
              <input type="hidden" name="usermessage_id" value="{{$_mail->_id}}"/>
              <button type="submit" class="archive">{{tr}}Archive{{/tr}}</button>
            </form>
	        </td>
	      </tr>
        {{foreachelse}}
          <tr><td class="empty" colspan="5">{{tr}}CUserMessage.none{{/tr}}</td></tr>
	      {{/foreach}}
	    </table>

      <!-- ARCHIVED -->
      <table class="main tbl" id="archive" style="display: none;">
	      <tr>
	        <th class="title" colspan="10">{{tr}}CUserMessage-archive{{/tr}}</th>
	      </tr>

	      <tr>
	        <th>{{mb_title class=CUserMessage field=from}}</th>
	        <th>{{mb_title class=CUserMessage field=subject}}</th>
	        <th>{{mb_title class=CUserMessage field=date_sent format=relative}}</th>
	        <th>{{tr}}Action{{/tr}}</th>
	      </tr>

	      {{foreach from=$listArchived item=_mail}}
	      <tr {{if !$_mail->date_read}}style="font-weight: bold;"{{/if}}>
	        <td class="text">{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_mail->_ref_user_from}}</td>
	        <td class="text">{{$_mail->subject}}</td>
	        <td>{{mb_value object=$_mail field=date_sent format=relative}}</td>
          <td>
            <button class="search" onclick="UserMessage.edit({{$_mail->_id}})" >{{tr}}CUserMessage.read{{/tr}}</button>
            <button class="mail" onclick="UserMessage.create({{$_mail->_ref_user_from->_id}}, 'Re: {{$_mail->_clean_subject}}')">{{tr}}CUserMessage.answer{{/tr}}</button>
          </td>
	      </tr>
        {{foreachelse}}
          <tr><td class="empty" colspan="4">{{tr}}CUserMessage.none{{/tr}}</td></tr>
	      {{/foreach}}
	    </table>

      <!-- SENT -->
      <table class="main tbl" id="sentbox" style="display: none;">
	      <tr>
	        <th class="title" colspan="10">{{tr}}CUserMessage-sentbox{{/tr}}</th>
	      </tr>

	      <tr>
	        <th>{{mb_label class=CUserMessage field=to}}</th>
	        <th>{{mb_title class=CUserMessage field=subject}}</th>
	        <th>{{mb_title class=CUserMessage field=date_sent format=relative}}</th>
	        <th>{{mb_title class=CUserMessage field=date_read format=relative}}</th>
	        <th>{{tr}}Action{{/tr}}</th>
	      </tr>

	      {{foreach from=$listSent item=_mail}}
	      <tr {{if !$_mail->date_read}}style="font-weight: bold;"{{/if}}>
	        <td class="text">
            {{foreach from=$_mail->_ref_users_to item=_to}}
              {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_to}}
            {{/foreach}}
          </td>
	        <td class="text">{{$_mail->subject}}</td>
	        <td>{{mb_value object=$_mail field=date_sent format=relative}}</td>
	        <td>{{mb_value object=$_mail field=date_read format=relative}}</td>
          <td>
            <button class="search" onclick="UserMessage.edit({{$_mail->_id}})" >{{tr}}CUserMessage.read{{/tr}}</button>
          </td>
	      </tr>
        {{foreachelse}}
          <tr><td class="empty" colspan="5">{{tr}}CUserMessage.none{{/tr}}</td></tr>
	      {{/foreach}}
	    </table>

      <!-- DRAFT -->
      <table class="main tbl" id="draft" style="display: none;">
	      <tr>
	        <th class="title" colspan="10">{{tr}}CUserMessage-draft{{/tr}}</th>
	      </tr>

	      <tr>
	        <th>{{mb_label class=CUserMessage field=to}}</th>
	        <th>{{mb_label class=CUserMessage field=subject}}</th>
	        <th>{{mb_label class=CUserMessage field=date_sent}}</th>
	        <th>{{tr}}Action{{/tr}}</th>
	      </tr>

	      {{foreach from=$listDraft item=_mail}}
	      <tr>
          <td class="text">
            {{foreach from=$_mail->_ref_users_to item=_to}}
              {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_to}}
            {{/foreach}}
          </td>
	        <td class="text">{{$_mail->subject}}</td>
	        <td>{{mb_value object=$_mail field=date_sent format=relative}}</td>
	        <td>
            <button class="edit" onclick="UserMessage.edit({{$_mail->_id}})" >{{tr}}CUserMessage.edit{{/tr}}</button>
	        </td>
	      </tr>
        {{foreachelse}}
          <tr><td class="empty" colspan="4">{{tr}}CUserMessage.none{{/tr}}</td></tr>
	      {{/foreach}}
	    </table>
    </td>
  </tr>
</table>