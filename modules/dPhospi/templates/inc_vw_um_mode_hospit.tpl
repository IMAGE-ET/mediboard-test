{{*
 * $Id$
 *  
 * @category Hospi
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org*}}


<select name="type_autorisation_mode_hospi">
  <option value="">{{tr}}Choose{{/tr}}</option>
  {{foreach from=$um->_mode_hospitalisation item=_um_mode_hospit}}
    <option value="{{$_um_mode_hospit}}" {{if $uf->type_autorisation_mode_hospi == $_um_mode_hospit}}selected{{/if}} >{{$_um_mode_hospit}}</option>
  {{/foreach}}
</select>
