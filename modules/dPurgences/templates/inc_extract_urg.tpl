{{* $Id: vw_idx_urg.tpl 7671 2009-12-19 08:42:21Z MyttO $ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 7671 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="main form"> 
  <tr>
    <th class="title" colspan="2">{{tr}}extract-urg-desc{{/tr}}</th>
  </tr>
   
  <tr>
    <th class="category">Action</th>
    <th class="category">Status</th>
  </tr>
  
  <tr>
    <td>
       <form name="formExtraction_urg" action="?" method="get">
         <table class="form">
           <tr>
             <th>{{mb_label object=$extractPassages field="debut_selection"}}</th>
             <td>
               {{mb_field object=$extractPassages field="debut_selection" form="formExtraction_urg" register="true"}} 
               <button class="tick" type="button" onclick="extractURG(this.form)">Extraire</button>
             </td>
           </tr>
           <tr>
             <th>{{mb_label object=$extractPassages field="fin_selection"}}</th>
             <td>{{mb_field object=$extractPassages field="fin_selection" form="formExtraction_urg" register="true"}}</td>
           </tr>
         </table>
       </form>
    </td>
    <td id="td_extract_urg">

    </td>
  </tr>
  <tr>
    <td>
      <button class="tick" type="button" id="encrypt_urg" onclick="encrypt('urg')">Chiffrer</button>
    </td>
    <td id="td_encrypt_urg">
      
    </td>
  </tr>
  <tr>
    <td>
      <button class="tick" type="button" id="transmit_urg" onclick="transmit('urg')">Transmission</button>
    </td>
    <td id="td_transmit_urg">
      
    </td>
  </tr>
</table>