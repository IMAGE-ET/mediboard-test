{{* $Id: configure.tpl 6341 2009-05-21 11:52:48Z mytto $ *}}

{{*
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision: 6341 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
  var stop = false;
  
  function purgeEchange(button){
    if(stop) {
      stop=false;
      return;
    }
    var action = $V(button.form.elements.do_purge);
    if (!action) {
      stop=true;
    }
    var url = new Url("webservices", "ajax_purge_echange");
    url.addParam("date_min", $V(button.form.elements.date_min));
    url.addParam("date_max", $V(button.form.elements.date_max));
    url.addParam("do_purge", $V(button.form.elements.do_purge) ? 1 : 0);
    url.requestUpdate("purge-echange", { onComplete:function() { 
      purgeEchange(button);
    }} );
  }
</script>


<table class="main">
  <tr>
    <td class="button">
      <form name="pureEchangeForm" action="?" method="get">
        <table class="form">
          <tr>
            <td colspan="2">
             {{tr}}CEchangeSOAP-_date_min{{/tr}} : 
             <input class="date" type="hidden" name="date_min" value="" />
              <script type="text/javascript">
                Main.add(function () {
                  Calendar.regField(getForm('pureEchangeForm').elements["date_min"]);
                });
              </script>
            </td>
          </tr>
          <tr>
            <td colspan="2">
             {{tr}}CEchangeSOAP-_date_max{{/tr}} : 
             <input class="date" type="hidden" name="date_max" value="" />
              <script type="text/javascript">
                Main.add(function () {
                  Calendar.regField(getForm('pureEchangeForm').elements["date_max"]);
                });
              </script>
            </td>
          </tr>
          <tr>
            <td>
              <button type="button" class="change" onclick="purgeEchange(this)">
                {{tr}}CEchangeSOAP-purge-search{{/tr}}
              </button>
              <label><input type="checkbox" name="do_purge" />{{tr}}Purge{{/tr}}</label>
              <label><button type="button" class="stop" onclick="stop=true">{{tr}}Stop{{/tr}}</button></label>
            </td>
            <td id="purge-echange"></td>
          </tr>
        </table>
      </form>
    </td>
  </tr>
</table>