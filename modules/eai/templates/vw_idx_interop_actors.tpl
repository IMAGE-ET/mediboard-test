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
    var interop_actor_guid = Url.hashParams().interop_actor_guid;
    if (interop_actor_guid) {
    	InteropActor.refreshActor(interop_actor_guid);
    }
  });
</script>

<table class="main">
  <tr>
    <td style="width:30%; height: 1%" id="{{$receiver->_class_name}}s">
      {{mb_include template=inc_actors actor=$receiver actors=$receivers}}
    </td>
    <td style="width:70%" rowspan="2" class="halfPane" id="actor">
    </td> 
  </tr>
  
  <tr>
    <td style="width:30%" id="{{$sender->_class_name}}s">
      {{mb_include template=inc_actors actor=$sender actors=$senders}}
    </td>
  </tr>
</table>