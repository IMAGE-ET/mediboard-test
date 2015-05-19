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
    <th class="title" colspan="2">{{tr}}extract-activite-desc{{/tr}}</th>
  </tr>
   
  <tr>
    <th class="category narrow">{{tr}}Action{{/tr}}</th>
    <th class="category">{{tr}}Status{{/tr}}</th>
  </tr>
  
  <tr>
    <td>
       <form name="formExtraction_activite" action="?" method="get">
         <table class="form">
           <tr>
             <th>{{mb_label object=$extractPassages field="debut_selection"}}</th>
             <td>
               {{mb_field object=$extractPassages field="debut_selection" form="formExtraction_activite" register="true"}}
               <button class="tick" type="button" onclick="extractActivite(this.form)">Extraire</button>
             </td>
           </tr>
         </table>
       </form>
    </td>
    <td id="td_extract_activite">

    </td>
  </tr>
  <tr>
    <td>
      <button class="tick" type="button" id="encrypt_activite" onclick="encryptActivite()">Chiffrer</button>
    </td>
    <td id="td_encrypt_activite">
      
    </td>
  </tr>
  <tr>
    <td>
      <button class="tick" type="button" id="transmit_activite" onclick="transmitActivite()">Transmission</button>
    </td>
    <td id="td_transmit_activite">
      
    </td>
  </tr>
</table>