{{*
 * $Id$
 *  
 * @category search
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org*}}
{{mb_script module="search" script="Search"}}
<table class="main">
  <tbody>
  <tr>
    <td class="halfPane">
      {{$before|@mbTrace}}
    </td>
    <td class="halfPane">
      {{$after|@mbTrace}}
    </td>
  </tr>
  <tr>
    <td class="button" colspan="6">
      <button class="close" onclick="Control.Modal.close();">{{tr}}Close{{/tr}}</button>
    </td>
  </tr>
  </tbody>
</table>