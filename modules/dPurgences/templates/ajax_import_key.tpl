{{* $Id: configure.tpl 20011 2013-07-23 10:51:17Z flaviencrochard $ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 20011 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $keydata}}
<script type="text/javascript">
  window.opener.RPU_Sender.updateKey("{{$fingerprint}}");
</script>
{{/if}}

<h2>Import de la clé publique InVS</h2>

<form method="post" action="?m=dPurgences&{{$actionType}}={{$action}}&amp;dialog=1&amp;" name="import" enctype="multipart/form-data">
  <input type="hidden" name="m" value="dPurgences" />
  <input type="hidden" name="{{$actionType}}" value="{{$action}}" />
  
  <input type="hidden" name="MAX_FILE_SIZE" value="4096000" />
  <input type="file" name="import" />
  
  <button type="submit" class="submit">{{tr}}Save{{/tr}}</button>
</form>

