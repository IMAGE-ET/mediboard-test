{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dicom
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module=dicom script=DicomSession ajax=true}}

<table class="main layout">
  <tr>
    <td id="search">
      {{mb_include template="inc_filter_sessions"}}
    </td>
  </tr>
  <tr>
    <td id="sessionsList">
      
    </td>
  </tr>
</table>
