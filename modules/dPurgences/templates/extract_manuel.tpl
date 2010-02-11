{{* $Id: vw_idx_rpu.tpl 7671 2009-12-19 08:42:21Z MyttO $ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 7671 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
  var extract_passages_id;
  
  function extract(form) {
    if (!checkForm(form)) {
      return;
    }
    var url = new Url("dPurgences", "ajax_extract_passages");
    url.addParam("debut_selection", $V(form.debut_selection));
    url.addParam("fin_selection", $V(form.fin_selection));
    url.requestUpdate('td_extract', { onComplete: function(){
      if (!$('td_extract').select('.error, .warning').length) {
         $('encrypt').disabled = false;
      }
     }});
  }
  
  function encrypt() {
    var url = new Url("dPurgences", "ajax_encrypt_passages");
    url.addParam("extract_passages_id", extract_passages_id);
    url.requestUpdate('td_encrypt', { onComplete: function(){
      if (!$('td_encrypt').select('.error, .warning').length) {
         $('transmit').disabled = false;
      }
     }});
  }
  
  function transmit() {
    var url = new Url("dPurgences", "ajax_transmit_passages");
    url.addParam("extract_passages_id", extract_passages_id);
    url.requestUpdate('td_transmit');
  }
  
  Main.add(function () {
    $('encrypt').disabled = true;
    $('transmit').disabled = true;
  });
  
</script>

<table class="main form">  
  <tr>
    <th class="category">Action</th>
    <th class="category">Status</th>
  </tr>
  
  <tr>
    <td>
       <form name="formExtraction" action="?" method="get">
         <table class="form">
           <tr>
             <th>{{mb_label object=$extractPassages field="debut_selection"}}</th>
             <td>
               {{mb_field object=$extractPassages field="debut_selection" form="formExtraction" register="true"}} 
               <button class="tick" type="button" onclick="extract(this.form)">Extraire</button>
             </td>
           </tr>
           <tr>
             <th>{{mb_label object=$extractPassages field="fin_selection"}}</th>
             <td>{{mb_field object=$extractPassages field="fin_selection" form="formExtraction" register="true"}}</td>
           </tr>
         </table>
       </form>
    </td>
    <td id="td_extract">

    </td>
  </tr>
  <tr>
    <td>
      <button class="tick" type="button" id="encrypt" onclick="encrypt()">Chiffrer</button>
    </td>
    <td id="td_encrypt">
      
    </td>
  </tr>
  <tr>
    <td>
      <button class="tick" type="button" id="transmit" onclick="transmit()">Transmission</button>
    </td>
    <td id="td_transmit">
      
    </td>
  </tr>
</table>