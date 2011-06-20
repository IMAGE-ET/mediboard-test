{{*
 * View Interop Sender EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
*}}

<tr>  
  <th>{{mb_label object=$actor field="user_id"}}</th>
  <td>
    {{mb_field object=$actor field="user_id" hidden=true}}
    {{if $actor->user_id}}
      <input type="text" size="30" readonly="readonly" ondblclick="ObjectSelector.init()" name="_user_view" value="{{$actor->_ref_user->_view|stripslashes}}" />
    {{else}}
      <input type="text" size="30" readonly="readonly" ondblclick="ObjectSelector.init()" name="_user_view" value="" />
    {{/if}}
      <button type="button" onclick="ObjectSelector.init()" class="search">{{tr}}Search{{/tr}}</button>   
      
      <input type="hidden" name="_selector_class_name" value="CUser" />          
      <script type="text/javascript">
        ObjectSelector.init = function(){
          this.sForm     = "edit{{$actor->_guid}}";
          this.sId       = "user_id";
          this.sView     = "_user_view";
          this.sClass    = "_selector_class_name";
          this.onlyclass = "true";
         
          this.pop();
        } 
       </script>
  </td>
</tr>
