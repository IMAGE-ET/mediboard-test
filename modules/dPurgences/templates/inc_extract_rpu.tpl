{{* $Id: vw_idx_rpu.tpl 7671 2009-12-19 08:42:21Z MyttO $ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 7671 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="main form">  
  <tr>
    <th class="title" colspan="2">{{tr}}extract-rpu-desc{{/tr}}</th>
  </tr>
  
  <tr>
    <th class="category">{{tr}}Action{{/tr}}</th>
    <th class="category">{{tr}}Status{{/tr}}</th>
  </tr>
  
  <tr>
    <td>
       <form name="formExtraction_rpu" action="?" method="get">
         <table class="form">
           <tr>
             <th>{{mb_label object=$extractPassages field="debut_selection"}}</th>
             <td>
               {{mb_field object=$extractPassages field="debut_selection" form="formExtraction_rpu" register="true"}} 
               <button class="tick" type="button" onclick="extractRPU(this.form)">Extraire</button>
             </td>
           </tr>
           <tr>
             <th>{{mb_label object=$extractPassages field="fin_selection"}}</th>
             <td>{{mb_field object=$extractPassages field="fin_selection" form="formExtraction_rpu" register="true"}}</td>
           </tr>
         </table>
       </form>
    </td>
    <td id="td_extract_rpu">

    </td>
  </tr>
  <tr>
    <td>
      <button class="tick" type="button" id="encrypt_rpu" onclick="encrypt('rpu')">Chiffrer</button>
    </td>
    <td id="td_encrypt_rpu">
      
    </td>
  </tr>
  <tr>
    <td>
      <button class="tick" type="button" id="transmit_rpu" onclick="transmit('rpu')">Transmission</button>
    </td>
    <td id="td_transmit_rpu">
      
    </td>
  </tr>
</table>