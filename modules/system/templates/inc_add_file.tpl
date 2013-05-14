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
    window.onunload = window.opener.ExchangeSource.showFiles($V("source_guid"), $V("current_directory"));
  });


</script>
<h2>Ajout d'un nouveau fichier.</h2>

<br/>

<form method="post" name="import" enctype="multipart/form-data">
  <input type="hidden" name="m" value="system" />
  <input type="hidden" id="source_guid" name="source_guid" value="{{$source_guid}}" />
  <input type="hidden" id="current_directory" name="current_directory" value="{{$current_directory}}" />

  <input type="hidden" name="MAX_FILE_SIZE" value="4096000" />
  <input type="file" name="import" />

  <button type="submit" class="submit">{{tr}}Save{{/tr}}</button>
</form>