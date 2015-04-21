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
    messagerie.refreshList('{{$account->_id}}', '{{$selected_folder}}');
  });
</script>

<div>
  <button class="button oneclick" onclick="messagerie.getLastMessages('{{$account->_id}}');">
    <i class="msgicon fa fa-refresh"></i>
    {{tr}}CUserMail-button-getNewMails{{/tr}}
  </button>
  <button class="button singleclick" onclick="messagerie.markallAsRead('{{$account->_id}}')">
    <i class="msgicon fa fa-eye"></i>
    {{tr}}CUserMail-option-allmarkasread{{/tr}}
  </button>
</div>
{{tr}}CUserMail-last-check{{/tr}} : {{$account->last_update|date_format:"%A %d %B %Y %H:%M"}}

<div id="externalMessages" style="position: relative;">
  <section style="position: absolute; width: 15%; left: 0px;">
    <ul class="list-folders" style="list-style-type: none; position: relative; margin-top: 10px; margin-right: 10px;">
      {{foreach from=$folders key=_folder item=_count}}
        <li style="margin-bottom: 5px;">
          <div class="folder{{if $_folder == $selected_folder}} selected{{/if}}" data-folder="{{$_folder}}" onclick="messagerie.selectFolder('{{$account->_id}}', '{{$_folder}}');" style="font-size: 1.1em; height: 20px; padding-bottom: 2px; padding-top: 2px; padding-left: 5px; margin: 5px; cursor: pointer;">
            <span style="float:left; margin-right: 5px;">
              <i class="msgicon folder-icon fa fa-folder{{if $selected_folder == $_folder}}-open{{/if}}"></i>
            </span>

            <span class="count circled"{{if $_count == 0}} style="display: none;"{{/if}}>
             {{$_count}}
            </span>

            <span>
              {{tr}}CUserMail-title-{{$_folder}}{{/tr}}
            </span>
          </div>
        </li>
      {{/foreach}}
    </ul>
  </section>

  <section style="position: absolute; height: 80%; width: 85%; left: 15%; top: 20%;">
    <div id="list-messages">

    </div>
  </section>
</div>
