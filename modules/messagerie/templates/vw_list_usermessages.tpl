<script type="text/javascript">

UserMessage.refresh = window.location.reload;

Main.add(function () {
  Control.Tabs.create("tab-usermessages", true);
});

</script>

<table class="main">
  <tr>
    <td colspan="2">
      <a class="button new" href="#nothing" onclick="UserMessage.create()">
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
            <a style="display: inline;" href="#nothing" onclick="UserMessage.edit({{$_mail->_id}})">
              {{tr}}CUserMessage.read{{/tr}}
            </a>
            /
            <a style="display: inline;" href="#nothing" onclick="UserMessage.create({{$_mail->_ref_user_from->_id}}, 'Re: {{$_mail->subject}}')">
              {{tr}}CUserMessage.answer{{/tr}}
            </a>
	        </td>
	      </tr>
	      {{/foreach}}
	    </table>

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
            <a style="display: inline;" href="#nothing" onclick="UserMessage.edit({{$_mail->_id}})">
              {{tr}}CUserMessage.read{{/tr}}
            </a>
            /
            <a style="display: inline;" href="#nothing" onclick="UserMessage.create({{$_mail->_ref_user_from->_id}}, 'Re: {{$_mail->subject}}')">
              {{tr}}CUserMessage.answer{{/tr}}
            </a>
          </td>
	      </tr>
	      {{/foreach}}
	    </table>

	    <table class="main tbl" id="sentbox" style="display: none;">
	      <tr>
	        <th class="title" colspan="10">{{tr}}CUserMessage-sentbox{{/tr}}</th>
	      </tr>

	      <tr>
	        <th>{{mb_title class=CUserMessage field=to}}</th>
	        <th>{{mb_title class=CUserMessage field=subject}}</th>
	        <th>{{mb_title class=CUserMessage field=date_sent format=relative}}</th>
	        <th>{{mb_title class=CUserMessage field=date_read format=relative}}</th>
	        <th>{{tr}}Action{{/tr}}</th>
	      </tr>

	      {{foreach from=$listSent item=_mail}}
	      <tr {{if !$_mail->date_read}}style="font-weight: bold;"{{/if}}>
	        <td class="text">{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_mail->_ref_user_to}}</td>
	        <td class="text">{{$_mail->subject}}</td>
	        <td>{{mb_value object=$_mail field=date_sent format=relative}}</td>
	        <td>{{mb_value object=$_mail field=date_read format=relative}}</td>
          <td>
            <a style="display: inline;" href="#nothing" onclick="UserMessage.edit({{$_mail->_id}})">
              {{tr}}CUserMessage.read{{/tr}}
            </a>
            /
            <a style="display: inline;" href="#nothing" onclick="UserMessage.create({{$_mail->_ref_user_to->_id}}, 'Re: {{$_mail->subject}}')">
              {{tr}}CUserMessage.answer{{/tr}}
            </a>
          </td>
	      </tr>
	      {{/foreach}}
	    </table>

	    <table class="main tbl" id="draft" style="display: none;">
	      <tr>
	        <th class="title" colspan="10">{{tr}}CUserMessage-draft{{/tr}}</th>
	      </tr>

	      <tr>
	        <th>{{mb_title class=CUserMessage field=to}}</th>
	        <th>{{mb_title class=CUserMessage field=subject}}</th>
	        <th>{{mb_title class=CUserMessage field=date_sent}}</th>
	        <th>{{tr}}Action{{/tr}}</th>
	      </tr>

	      {{foreach from=$listDraft item=_mail}}
	      <tr>
	        <td class="text">{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_mail->_ref_user_to}}</td>
	        <td class="text">{{$_mail->subject}}</td>
	        <td>{{mb_value object=$_mail field=date_sent format=relative}}</td>
	        <td>
            <a style="display: inline;" href="#nothing" onclick="UserMessage.edit({{$_mail->_id}})">
              {{tr}}CUserMessage.read{{/tr}}
            </a>
	        </td>
	      </tr>
	      {{/foreach}}
	    </table>
    </td>
  </tr>
</table>