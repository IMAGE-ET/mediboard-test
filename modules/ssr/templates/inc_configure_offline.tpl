{{* $Id: configure.tpl 7993 2010-02-03 16:55:27Z MyttO $ *}}

{{*
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision: 7993 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}


<table class="tbl">
  <tr>
    <td>
    	<a class="button search" href="?m={{$m}}&amp;a=offline_plannings_equipements&amp;dialog=1">
    		Plannings Equipements
			</a>
      <a class="button search" href="?m={{$m}}&amp;a=offline_plannings_techniciens&amp;dialog=1">
        Plannings Rééducateurs
      </a>
      <a class="button search" href="?m={{$m}}&amp;a=offline_repartition&amp;dialog=1">
        Répartition des patients
      </a>
      <a class="button search" href="?m={{$m}}&amp;a=offline_sejours&amp;dialog=1">
        Séjours
      </a>
		</td>
  </tr>
  
</table>