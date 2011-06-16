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
      <strong>{{$ccam.CODE|emphasize:$_codes_ccam}}</strong>
      <br />
      <small>{{$ccam.LIBELLELONG|emphasize:$_codes_ccam}}</small>
    </li>
  {{/foreach}}
</ul>