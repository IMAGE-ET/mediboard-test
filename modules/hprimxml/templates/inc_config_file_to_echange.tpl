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
  
  function importEchange(button){
    if(stop) {
      stop=false;
      return;
    }
    var action = $V(button.form.elements.do_import);
    if (!action) {
      stop=true;
    }
    var url = new Url("hprimxml", "ajax_file_to_echange");
    url.addParam("path", $V(button.form.elements.path));
    url.addParam("type", $V(button.form.elements.type));
    url.addParam("limit", $V(button.form.elements.limit));
    url.addParam("do_import", $V(button.form.elements.do_import) ? 1 : 0);
    url.requestUpdate("import-echange", { onComplete:function() { 
    	importEchange(button);
    }} );
  }

  Main.add(function() {
    getForm("fileToEchangeForm")["limit"].addSpinner({min:0, max:1500, step:500});
  });
</script>


<table class="main">
  <tr>
    <td class="button">
      <form name="fileToEchangeForm" action="?" method="get">
        <table class="form">
        
          {{assign var=m value=dPfiles}}
          {{assign var=class value=CFile}}
          {{assign var="var" value="upload_directory"}}
          <tr>
            <th style="text-align:left; width:0.1%"> {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}} </th>
            <td>
              {{$dPconfig.$m.$class.$var}}/<input type="text" name="path" size="80" value="" />
            </td>
          </tr>
          
          <tr>
            <th style="text-align:left; width:0.1%"> {{tr}}CEchangeHprim-type-desc{{/tr}} </th>
            <td>
              <label><input type="radio" name="type" value="pmsi"/>PMSI</label>
              <label><input type="radio" name="type" value="actes"/>Serveur d'actes</label>
            </td>
          </tr>
          
          <tr>
            <th style="text-align:left; width:0.1%"> {{tr}}CEchangeHprim-file-limit-desc{{/tr}} </th>
            <td>
              <input type="text" name="limit" value="0" />
            </td>
          </tr>

          <tr>
            <td>
              <button type="button" class="change" onclick="importEchange(this)">
                {{tr}}CEchangeHprim-import-files{{/tr}}
              </button>
              <label><input type="checkbox" name="do_import" />{{tr}}Import{{/tr}}</label>
              <label><button type="button" class="stop" onclick="stop=true">{{tr}}Stop{{/tr}}</button></label>
            </td>
            <td id="import-echange"></td>
          </tr>
          
        </table>
      </form>
    </td>
  </tr>
</table>