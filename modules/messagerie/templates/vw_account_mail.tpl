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
  <select style="width: 50px;" name="action">
    <option value="">{{tr}}CUserMail-option-More{{/tr}}</option>
    <option value="AllMarkAsRead" onclick="messagerie.markallAsRead('{{$account->_id}}')">{{tr}}CUserMail-option-allmarkasread{{/tr}}</option>
  </select>
</div>

<table class="main">
  <tr>
    <td class="narrow" style="width:10%;">
      <ul class="control_tabs_vertical" id="type_message">
        <li><a href="#inbox" {{if !$nbTotal}}class="empty"{{/if}}>{{tr}}CUserMessage-inbox{{/tr}} <small>({{$nbUnseen}}/{{$nbTotal}})</small></a></li>
        <li><a href="#archived" {{if !$nbArchived}}class="empty"{{/if}}><img src="modules/{{$m}}/images/mail_archive.png"  alt="" style="height: 15px; float:left;"/>{{tr}}CUserMessage-archive{{/tr}} <small>({{$nbArchived}})</small></a></li>
        <li><a href="#favorited" {{if !$nbFavorite}}class="empty"{{/if}}><img src="modules/{{$m}}/images/favorites-1.png"  alt="" style="height: 15px; float:left;"/>{{tr}}CUserMessage-favorite{{/tr}} <small>({{$nbFavorite}})</small></a></li>
        <li><a href="#sent" {{if !$nbSent}}class="empty"{{/if}}>{{tr}}CUserMessage-sentbox{{/tr}} <small>({{$nbSent}})</small></a></li>
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
