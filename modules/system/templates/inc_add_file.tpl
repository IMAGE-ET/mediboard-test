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
<iframe name="upload-file-exchange" id="upload-file-exchange" style="width: 1px; height: 1px;"></iframe>
<div class="small-info">
  <div>{{tr}}config-dPfiles-upload_max_filesize{{/tr}} : <strong>{{$max_size}}</strong></div>
</div>
<h2>Ajout d'un nouveau fichier.</h2>

<br/>
<form method="post" name="import" enctype="multipart/form-data" target="upload-file-exchange">
  <input type="hidden" name="m" value="system" />
  <input type="hidden" name="dosql" value="do_exchange_file_aed" />
  <input type="hidden" id="source_guid" name="source_guid" value="{{$source_guid}}" />
  <input type="hidden" id="current_directory" name="current_directory" value="{{$current_directory}}" />

  <input type="hidden" name="MAX_FILE_SIZE" value="4096000" />
  <input type="file" name="import[0]" onchange="ExchangeSource.addInputFile(this); this.onchange=''"/>

  <br/><button type="submit" class="submit">{{tr}}Save{{/tr}}</button>
</form>