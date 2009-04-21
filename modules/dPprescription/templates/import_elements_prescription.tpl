{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<h2>Import de catalogue d'éléments de prescriptions</h2>

<div class="big-info">
	Merci de fournir un document XML valide, au regard du schéma suivant :
	<ul><li><a href="{{$schemaPath}}">Schéma d'import</a></li></ul>
</div>

<form action="" method="post" enctype="multipart/form-data">
  <input type="hidden" name="MAX_FILE_SIZE" value="1024000" />
  <input type="file" name="docPath" size="40">

  <select name="group_id">
    <option value="no_group">Non associés</option>
    {{foreach from=$groups item=_group}}
      <option value="{{$_group->_id}}">de {{$_group->_view}}</option>
    {{/foreach}}
  </select>
  
  <button type="submit" class="submit">Importer</button>
</form>

