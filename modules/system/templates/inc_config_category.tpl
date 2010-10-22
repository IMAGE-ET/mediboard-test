{{* $Id: inc_config_bool.tpl 10085 2010-09-16 09:20:46Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 10085 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_default var=thcolspan value=2}}

<tr>
  <th colspan="{{$thcolspan}}" class="category">
  	{{if $class.0 == "C"}} 
      {{tr}}{{$class}}{{/tr}}
  	{{else}}
      {{tr}}config-{{$m}}-{{$class}}{{/tr}}
  	{{/if}}
  </th>
</tr>  
