{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dmi
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form action="?m=dmi&amp;a=vw_import&amp;dialog=1&amp;object_class={{$object_class}}" enctype="multipart/form-data" method="post">
  <input type="hidden" name="m" value="dmi" />
  <input type="hidden" name="dosql" value="{{$dosql}}" />
  <input type="hidden" name="del" value="0" />
  <h3>Import de {{tr}}{{$object_class}}{{/tr}} à partir d'un fichier CSV</h3>
  
  <div class="small-info">
  	Fichier de type CSV 
  	avec séparateur <tt>,</tt> (virgule)
  	et délimiteur <tt>"</tt> (double guillemets).
  </div>
  
  <div style="text-align: center;">
    <input type="hidden" name="MAX_FILE_SIZE" value="4096000" />
    <input type="file" name="datafile" size="40">
    <button type="submit" class="submit">Importer</button>
  </div>
</form>