{{*
 * $Id$
 *  
 * @package    Mediboard
 * @subpackage system
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 * @link       http://www.mediboard.org
*}}

{{if $is_last}}
  <script type="text/javascript">
    Main.add(function() {
      var form = getForm("edit-configuration");
      form["c[{{$_feature}}]"][1].addSpinner({{$_prop|@json}});
    });
  </script>
  <input type="text" class="{{$_prop.string}}" name="c[{{$_feature}}]" string="num" value="{{$value}}" {{if $is_inherited}} disabled {{/if}} size="4" />
{{else}}
  {{$value}}
{{/if}}