{{*
 * Import config XML EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
*}}

<script type="text/javascript">

uploadCallback = function(message) {
  $("systemMsg").insert(message);
  window.opener.InteropActor.callbackConfigsFormats();
}

</script>

<form method="post" action="?m={{$m}}&amp;{{$actionType}}={{$action}}&amp;dialog=1&amp;" name="formImportConfigXML" enctype="multipart/form-data">
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="{{$actionType}}" value="{{$action}}" />
  <input type="hidden" name="actor_guid" value="{{$actor_guid}}" />
  <input type="hidden" name="format_config_guid" value="{{$format_config_guid}}" />
  <input type="hidden" name="MAX_FILE_SIZE" value="4096000" />
  
  <input type="file" name="import" />
  <button type="submit" class="submit">{{tr}}Save{{/tr}}</button>
</form>