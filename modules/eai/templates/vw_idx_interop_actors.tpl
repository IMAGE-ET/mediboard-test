{{*
 * View Interop Actors EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
*}}

{{mb_script module=eai script=interop_actor}}
{{mb_script module="system" script="object_selector"}}

<script type="text/javascript">
  Main.add(function () {
    tabs = Control.Tabs.create('tabs-actors', false,  {
      afterChange: function(newContainer){
        switch (newContainer.id) {
          case "CInteropReceivers" :
            InteropActor.refreshActors('CInteropReceiver');
            break;
          case "CInteropSenders" :
            InteropActor.refreshActors('CInteropSender');
            break;
        }
      }
    });

    var interop_actor_guid = Url.hashParams().interop_actor_guid;
    if (interop_actor_guid) {
    	InteropActor.refreshActor(interop_actor_guid);
    }
  });
</script>

<table class="main">
  <tr>
    <td style="width:35%;">
      <ul id="tabs-actors" class="control_tabs">
        <li>
          <a href="#CInteropReceivers">
            {{tr}}CInteropReceiver-court{{/tr}}
            (&ndash; / &ndash;)
          </a>
        </li>
        <li>
          <a href="#CInteropSenders">
            {{tr}}CInteropSender-court{{/tr}}
            (&ndash; / &ndash;)
          </a>
        </li>
      </ul>

      <hr class="control_tabs" />

      <div id="CInteropReceivers" style="display: none">
        {{mb_include template=inc_actors actor=$receiver actors=$receivers parent_class="CInteropReceiver"}}
      </div>

      <div id="CInteropSenders" style="display: none">
        {{mb_include template=inc_actors actor=$sender actors=$senders parent_class="CInteropSender"}}
      </div>
    </td>
    <td style="width:70%" rowspan="2" class="halfPane" id="actor">
    </td> 
  </tr>
</table>