{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPccam
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<ul>
  {{foreach from=$result item=ccam}}
    <li>
      <strong>{{$ccam.CODE}}</strong>
      <br />
      <small>{{$ccam.LIBELLELONG|truncate:35:"...":false}}</small>
    </li>
  {{/foreach}}
</ul>