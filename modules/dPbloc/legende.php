<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

?>

<table class="tbl">
  <col style="width: 0.1%" />
  <tr>
    <td class="patient-not-arrived">M. PATIENT Patient</td>
    <td class="text">Patient pas encore dans l'établissement</td>
  </tr>
  <tr>
    <td class="septique">M. PATIENT Patient</td>
    <td class="text">Patient septique</td>
  </tr>   
  <tr>
    <td style="background-color:#ffa"></td>
    <td class="text">Patient entré au bloc</td>
  </tr>       
  <tr>
    <td style="background-color:#cfc"></td>
    <td class="text">Intervention en cours</td>
  </tr>
  <tr>
    <td style="background-image:url(images/icons/ray.gif); background-repeat:repeat;"></td>
    <td class="text">Intervention terminée</td>
  </tr> 
  <tr>
    <td style="background-color:#fcc"></td>
    <td class="text">Problème de timing</td>
  </tr>
  <tr>
    <td style="background-color:#ccf"></td>
    <td class="text">Intervention déplacée dans une autre salle</td>
  </tr>
</table>