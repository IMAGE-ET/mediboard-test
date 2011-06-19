{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage admin
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="form">
  <tr>
    <th class="category" colspan="2">Identité</th>
    <th class="category" colspan="2">Coordonnées</th>
  </tr>

  <tr>
    <th>{{mb_label object=$user field=user_username}}</th>
    <td>{{mb_value object=$user field=user_username}}</td>
    <th>{{mb_label object=$user field=user_address1}}</th>
    <td>{{mb_value object=$user field=user_address1}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$user field=user_first_name}}</th>
    <td>{{mb_value object=$user field=user_first_name}}</td>
    <th>{{mb_label object=$user field=user_zip}}</th>
    <td>{{mb_value object=$user field=user_zip}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$user field=user_type}}</th>
    <td>
      {{assign var="type" value=$user->user_type}}
      {{$utypes.$type}}
    </td>
    <th>{{mb_label object=$user field=user_phone}}</th>
    <td>{{mb_value object=$user field=user_phone}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$user field=user_email}}</th>
    <td>{{mb_value object=$user field=user_email}}</td>
    <th>{{mb_label object=$user field=user_phone}}</th>
    <td>{{mb_value object=$user field=user_phone}}</td>
  </tr>
  
</table>