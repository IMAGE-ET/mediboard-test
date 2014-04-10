<script>
  Main.add(function () {
    Control.Tabs.create("tab-usermessages", true , {
      afterChange: function(newContainer) {
        UserMessage.refreshList(newContainer.id, 0);
      }
    });
  });
</script>

<form method="get" name="list_usermessage" onsubmit="return onSubmitFormAjax(this, null, $V(this.mode))">
  <input type="hidden" name="m" value="messagerie" />
  <input type="hidden" name="a" value="ajax_list_usermessage" />
  <input type="hidden" name="user_id" value="{{$user->_id}}" />
  <input type="hidden" name="mode" value="inbox"/>
  <input type="hidden" name="page" value="0" />
</form>

<table class="main">
  <tr>
    <td colspan="2">
      <button class="button edit" onclick="UserMessage.edit('', '', UserMessage.refreshListCallback)">
        {{tr}}CUserMessage-title-create{{/tr}}
      </button>
    </td>
  </tr>
  <tr>
    <td style="vertical-align: top;" class="narrow">
      <ul id="tab-usermessages" class="control_tabs_vertical">
        <li>
          <a href="#inbox" style="white-space: nowrap;" {{if !$listInboxUnread}}class="empty"{{/if}}>
        		{{tr}}CUserMessage-inbox{{/tr}}
        		<small>({{$listInboxUnread}} / {{$listInbox}})</small>
        	</a>
        </li>
        <li>
          <a href="#archive" style="white-space: nowrap;" {{if !$listArchived}}class="empty"{{/if}}>
        		{{tr}}CUserMessage-archive{{/tr}}
        		<small>({{$listArchived}})</small>
        	</a>
        </li>
        <li>
          <a href="#sentbox" style="white-space: nowrap;" {{if !$listSent}}class="empty"{{/if}}>
        		{{tr}}CUserMessage-sentbox{{/tr}}
        		<small>({{$listSent}})</small>
        	</a>
        </li>
        <li>
          <a href="#draft" style="white-space: nowrap;" {{if !$listDraft}}class="empty"{{/if}}>
        		{{tr}}CUserMessage-draft{{/tr}}
        		<small>({{$listDraft}})</small>
        	</a>
        </li>
      </ul>
    </td>

    <td>
      <!-- INBOX -->
      <div id="inbox" style="display: none;">
	    </div>

      <!-- ARCHIVED -->
      <div id="archive" style="display: none;">
	    </div>

      <!-- SENT -->
      <div id="sentbox" style="display: none;">
	    </div>

      <!-- DRAFT -->
      <div id="draft" style="display: none;">
	    </div>
    </td>
  </tr>
</table>