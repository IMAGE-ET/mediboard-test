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
    var tabs = Control.Tabs.create("type_message", false , {
      afterChange: function(newContainer) {
        messagerie.refreshList('{{$account->_id}}',newContainer.id);
      }
    });
  });
</script>

<div>
  <button class="button change" onclick="messagerie.getLastMessages('{{$account->_id}}');">{{tr}}CUserMAil-button-getNewMails{{/tr}}</button>
  <button class="button tick" onclick="messagerie.markallAsRead('{{$account->_id}}')">{{tr}}CUserMail-option-allmarkasread{{/tr}}</button>
</div>
{{tr}}CUserMail-last-check{{/tr}} : {{$account->last_update|date_format:"%A %d %B %Y %H:%M"}}

<table class="main">
  <tr>
    <td class="narrow" style="width:10%;">
      <ul class="control_tabs_vertical" id="type_message">
        <li><a href="#inbox" {{if !$nbTotal}}class="empty"{{/if}}>{{tr}}CUserMessage-inbox{{/tr}} <br/><small>({{$nbUnseen}}/{{$nbTotal}})</small></a></li>
        <li><a href="#archived" {{if !$nbArchived}}class="empty"{{/if}}><img src="modules/{{$m}}/images/mail_archive.png"  alt="" style="height: 15px; float:left;"/>{{tr}}CUserMessage-archive{{/tr}} <br/><small>({{$nbArchived}})</small></a></li>
        <li><a href="#favorited" {{if !$nbFavorite}}class="empty"{{/if}}><img src="modules/{{$m}}/images/favorites-1.png"  alt="" style="height: 15px; float:left;"/>{{tr}}CUserMessage-favorite{{/tr}} <br/><small>({{$nbFavorite}})</small></a></li>
        <li><a href="#sent" {{if !$nbSent}}class="empty"{{/if}}>{{tr}}CUserMessage-sentbox{{/tr}} <br/><small>({{$nbSent}})</small></a></li>
      </ul>
    </td>
    <td>
      <table id="inbox"     class="tbl" style="display: none;"></table>
      <table id="archived"  class="tbl" style="display: none;"></table>
      <table id="favorited"  class="tbl" style="display: none;"></table>
      <table id="sent"      class="tbl" style="display: none;"></table>
    </td>
  </tr>
</table>
