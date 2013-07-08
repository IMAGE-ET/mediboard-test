{{*
 * $Id$
 *  
 * @category CDA
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}


<table class="tbl">
  <tr>
    <th class="title" colspan="4">{{tr}}Informations carte{{/tr}}</th>
  </tr>
  <tr>
    <th>{{tr}}firstName{{/tr}}</th>
    <th>{{tr}}birthDate{{/tr}}</th>
    <th>{{tr}}nir{{/tr}}</th>
    <th>{{tr}}insc{{/tr}}</th>
  </tr>
  {{foreach from=$list_person item=_person}}
  <tr>
    <td>{{$_person->prenom}}</td>
    <td>{{$_person->date}}</td>
    <td>{{$_person->nirCertifie}}</td>
    <td>{{$_person->insc}}</td>
  </tr>
  {{foreachelse}}
    <tr>
      <td>{{tr}}No result{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>
