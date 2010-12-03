{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7948 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<input type="hidden" name="show_cancelled_services" value="{{$show_cancelled_services|ternary:1:0}}" onchange="this.form.onsubmit();">
<input type="checkbox" name="_show_cancelled_services" value="1" {{if $show_cancelled_services}} checked="checked" {{/if}} 
  onclick="$V(this.form.show_cancelled_services, $V(this)?1:0);"
>
<label for="_show_cancelled_services">
  Services inactifs
</label>
