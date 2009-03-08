<script type="text/javascript">
function writeMbMail(to, subject) {
  var url = new Url();
  url.setModuleAction("messagerie", "write_mbmail");
  url.addParam("mbmail_id", 0);
  if(to) {
    url.addParam("to", to);
  }
  if (subject) {
    url.addParam("subject", subject);
  }
  url.popup(500, 500, "MbMail");
}

Main.add(function () {
  Control.Tabs.create("tab-mbmails", false);
});

</script>

<table class="main">
  <tr>
    <td style="width: 0.1%; vertical-align: top;">
      <ul id="tab-mbmails" class="control_tabs_vertical">
        <li>
          {{assign var=count value=$listInbox|@count}}
          <a href="#inbox" style="white-space: nowrap;" {{if !$count}}class="empty"{{/if}}>
        		{{tr}}CMbMail-inbox{{/tr}}
        		<small>({{$count}})</small>
        	</a>
        </li>
        <li>
          {{assign var=count value=$listArchived|@count}}
          <a href="#archive" style="white-space: nowrap;" {{if !$count}}class="empty"{{/if}}>
        		{{tr}}CMbMail-archive{{/tr}}
        		<small>({{$count}})</small>
        	</a>
        </li>
        <li>
          {{assign var=count value=$listSent|@count}}
          <a href="#sentbox" style="white-space: nowrap;" {{if !$count}}class="empty"{{/if}}>
        		{{tr}}CMbMail-sentbox{{/tr}}
        		<small>({{$count}})</small>
        	</a>
        </li>
        <li>
          {{assign var=count value=$listDraft|@count}}
          <a href="#draft" style="white-space: nowrap;" {{if !$count}}class="empty"{{/if}}>
        		{{tr}}CMbMail-draft{{/tr}}
        		<small>({{$count}})</small>
        	</a>
        </li>
      </ul>
    </td>
    
    <td>
	    <table class="main tbl" id="inbox" style="display: none;">
	      <tr>
	        <th class="title" colspan="4">{{tr}}CMbMail-inbox{{/tr}}</th>
	      </tr>

	      <tr>
	        <th>{{mb_title class=CMbMail field=from}}</th>
	        <th>{{mb_title class=CMbMail field=subject}}</th>
	        <th>{{mb_title class=CMbMail field=date_sent}}</th>
	        <th>{{tr}}Action{{/tr}}</th>
	      </tr>
	      {{foreach from=$listInbox item=_mail}}

	      <tr>
	        <td>{{$_mail->_ref_user_from}}</td>
	        <td class="text"><a href="?m=messagerie&amp;tab=write_mbmail&amp;mbmail_id={{$_mail->_id}}">{{$_mail->subject}}<a/></td>
	        <td>{{mb_value object=$_mail field=date_sent}}</td>
	        <td>
	          <div style="float: right">
	            <a href="#nothing" onclick="writeMbMail({{$_mail->_ref_user_from->_id}}, 'Reponse')">
                <img src="images/icons/mbmail.png" alt="message" title="Envoyer un message" />
              </a>
	          </div>
	          Forward / Archive
	        </td>
	      </tr>
	      {{/foreach}}
	    </table>

	    <table class="main tbl" id="archive" style="display: none;">
	      <tr>
	        <th class="title" colspan="4">{{tr}}CMbMail-archive{{/tr}}</th>
	      </tr>

	      <tr>
	        <th>{{mb_title class=CMbMail field=from}}</th>
	        <th>{{mb_title class=CMbMail field=subject}}</th>
	        <th>{{mb_title class=CMbMail field=date_sent}}</th>
	        <th>{{tr}}Action{{/tr}}</th>
	      </tr>

	      {{foreach from=$listArchived item=_mail}}
	      <tr>
	        <td>{{$_mail->_ref_user_from}}</td>
	        <td class="text"><a href="?m=messagerie&amp;tab=write_mbmail&amp;mbmail_id={{$_mail->_id}}">{{$_mail->subject}}<a/></td>
	        <td>{{mb_value object=$_mail field=date_sent}}</td>
	        <td>
	          <div style="float: right">
	            <a href="#nothing" onclick="writeMbMail({{$_mail->_ref_user_from->_id}}, 'Reponse')">
                <img src="images/icons/mbmail.png" alt="message" title="Envoyer un message" />
              </a>
	          </div>
	          Forward</td>
	      </tr>
	      {{/foreach}}
	    </table>

	    <table class="main tbl" id="sentbox" style="display: none;">
	      <tr>
	        <th class="title" colspan="4">{{tr}}CMbMail-sentbox{{/tr}}</th>
	      </tr>

	      <tr>
	        <th>{{mb_title class=CMbMail field=to}}</th>
	        <th>{{mb_title class=CMbMail field=subject}}</th>
	        <th>{{mb_title class=CMbMail field=date_sent}}</th>
	        <th>{{tr}}Action{{/tr}}</th>
	      </tr>

	      {{foreach from=$listSent item=_mail}}
	      <tr>
	        <td>{{$_mail->_ref_user_to}}</td>
	        <td class="text"><a href="?m=messagerie&amp;tab=write_mbmail&amp;mbmail_id={{$_mail->_id}}">{{$_mail->subject}}<a/></td>
	        <td>{{mb_value object=$_mail field=date_sent}}</td>
	        <td>Forward</td>
	      </tr>
	      {{/foreach}}
	    </table>

	    <table class="main tbl" id="draft" style="display: none;">
	      <tr>
	        <th class="title" colspan="4">{{tr}}CMbMail-draft{{/tr}}</th>
	      </tr>

	      <tr>
	        <th>{{mb_title class=CMbMail field=to}}</th>
	        <th>{{mb_title class=CMbMail field=subject}}</th>
	        <th>{{mb_title class=CMbMail field=date_sent}}</th>
	        <th>{{tr}}Action{{/tr}}</th>
	      </tr>

	      {{foreach from=$listDraft item=_mail}}
	      <tr>
	        <td>{{$_mail->_ref_user_to}}</td>
	        <td class="text"><a href="?m=messagerie&amp;tab=write_mbmail&amp;mbmail_id={{$_mail->_id}}">{{$_mail->subject}}<a/></td>
	        <td>{{mb_value object=$_mail field=date_sent}}</td>
	        <td>Edit / Send / Delete</td>
	      </tr>
	      {{/foreach}}
	    </table>
    </td>
  </tr>
</table>