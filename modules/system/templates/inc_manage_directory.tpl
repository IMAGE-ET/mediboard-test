{{*
 * $Id$
 *
 * @category system
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}
<script>
  Main.add(function(){
    Control.Tabs.create("message-tab-cda", true);
    var tree = new TreeView("treeDirectory");
  });
</script>

<table class="tbl">
  <tr>
    <th>
      {{tr}}Directory{{/tr}}
    </th>
  </tr>
  <tr>
    <td id="treeDirectory">
      {{foreach from=$root item=_root name=foreachroot}}
        <ul>
          <li>
            <a href="#1"
                onclick="ExchangeSource.changeDirectory('{{$source_guid}}', '{{$_root.path}}')">
              {{if $smarty.foreach.foreachroot.first}}
                <img src="modules/system/images/homeIcon.png"/>
              {{else}}
                {{$_root.name}}
              {{/if}}
            </a>
      {{/foreach}}
      <ul>
      {{foreach from=$directory item=_directory}}
        <li>
          <a href="#1"
            onclick="ExchangeSource.changeDirectory('{{$source_guid}}', '{{$current_directory}}{{$_directory}}')">
              {{$_directory|utf8_decode}}
          </a>
        </li>
      {{/foreach}}
        </ul>
      {{foreach from=$root item=_root name=foreachroot}}
         </li>
        </ul>
      {{/foreach}}
    </td>
  </tr>
</table>