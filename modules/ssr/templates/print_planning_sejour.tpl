{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include_script module=ssr script=planification}}
{{mb_include_script module="ssr" script="planning"}}
<script type="text/javascript">

Main.add(function(){
  Planification.refreshSejour("{{$sejour_id}}", false, 800, true);
});



</script>


<div id="planning-sejour"></div>
