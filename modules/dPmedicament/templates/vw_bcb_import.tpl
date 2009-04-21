{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPmedicament
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form action="?m=dPmedicament&amp;a=vw_bcb_import&amp;dialog=1" enctype="multipart/form-data" method="post">
  <input type="hidden" name="m" value="dPmedicament" />
  <input type="hidden" name="dosql" value="do_bcb_import" />
  <input type="hidden" name="del" value="0" />
  <h3>Import d'un fichier CSV</h3>
  <div class="big-info">
  	Fichier de type CSV 
  	avec séparateur <tt>,</tt> (virgule)
  	et délimiteur <tt>"</tt> (double guillemets).
  	<br />
  	La structure des colonnes est, dans l'ordre :
<pre>CIP (obligatoire)
PrixHopital
PrixVille
DatePrixHopital
DatePrixVille
Commentaire
CodeInterne</pre>
  </div>
  
  <div style="text-align: center;">
    <input type="hidden" name="MAX_FILE_SIZE" value="4096000" />
    <input type="file" name="datafile" size="40">
    <button type="submit" class="submit">Importer</button>
  </div>
</form>