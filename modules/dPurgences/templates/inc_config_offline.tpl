{{* $Id: configure.tpl 8652 2010-04-22 07:57:27Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 8652 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<h2>Mode offline</h2>

<table class="tbl">
  <tr>
    <td>
      <a class="button search" href="?m={{$m}}&a=print_main_courante&offline=1&dialog=1&_aio=1">
        Main courante
      </a>
      <a class="button search" href="?m=hospi&a=vw_bilan_service&token_cat=all&dialog=1&mode_urgences=1&offline=1">
        Bilan
      </a>
    </td>
  </tr>
</table>