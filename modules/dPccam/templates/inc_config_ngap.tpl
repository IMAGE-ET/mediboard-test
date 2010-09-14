{{* $Id: configure.tpl 9306 2010-06-28 08:29:45Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage dPccam
 * @version $Revision: 9306 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<h2>Import de la base de codes NGAP</h2>
<table class="tbl">
  <tr>
    <th>{{tr}}Action{{/tr}}</th>
    <th>{{tr}}Status{{/tr}}</th>
  </tr>
  
  <tr>
    <td><button class="tick" onclick="startNGAP()" >Importer la base de codes NGAP</button></td>
    <td id="ngap"></td>
  </tr>
</table>
