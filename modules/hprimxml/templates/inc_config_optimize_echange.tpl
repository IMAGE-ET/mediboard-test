{{* $Id: inc_echange_hprim.tpl 7691 2009-12-22 16:22:58Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage sip
 * @version $Revision: 7691 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
  var stop = false;
  
  function optimizeEchange(button){
    if(stop) {
      stop=false;
      return;
    }
    var action = $V(button.form.elements.do_optimize);
    if (!action) {
      stop=true;
    }
    var url = new Url("hprimxml", "ajax_optimize_echange");
    url.addParam("do_optimize", $V(button.form.elements.do_optimize) ? 1 : 0);
    url.requestUpdate("optimize-echange-messages", { onComplete:function() { 
      optimizeEchange(button);
    }} );
  }
</script>


<table class="main">
  <tr>
    <td class="button">
      <form name="echangeForm" action="?" method="get">
        <table class="form">
          <tr>
            <th colspan="2" class="category">
              {{tr}}CEchangeHprim-optimize{{/tr}}
            </th>
          </tr>
          <tr>
            <td>
              <button type="button" class="change" onclick="optimizeEchange(this)">
                {{tr}}CEchangeHprim-optimize-search{{/tr}}
              </button>
              <label><input type="checkbox" name="do_optimize" />{{tr}}Optimize{{/tr}}</label>
              <label><button type="button" class="stop" onclick="stop=true">{{tr}}Stop{{/tr}}</button></label>
            </td>
            <td id="optimize-echange-messages"></td>
          </tr>
        </table>
      </form>
    </td>
  </tr>
</table>