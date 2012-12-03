{{* $Id: inc_edit_user.tpl 8378 2010-03-18 15:15:48Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage admin
 * @version $Revision: 8378 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="form">  
  <tr>
    <th class="title" colspan="2">
      {{tr}}config-exchange-source{{/tr}}
    </th>
  </tr>
  <tr>
    <th class="category">{{tr}}CExchangeSource.smtp-desc{{/tr}}</th>
    <th class="category">{{tr}}CExchangeSource.pop-desc{{/tr}}</th>
  </tr>
  <tr>
    <td style="width:50%;"> {{mb_include module=system template=inc_config_exchange_source source=$smtp_source}} </td>
    <td style="width:50%;"> {{mb_include module=system template=inc_config_exchange_source source=$pop_source}} </td>
  </tr>
</table>
		