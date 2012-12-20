{{*
 * View domains EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
*}}

{{mb_script module=eai script=domain}}

<script type="text/javascript">

window.checkedMerge = [];
checkOnlyTwoSelected = function(checkbox) {
  checkedMerge = checkedMerge.without(checkbox);
  
  if (checkbox.checked)
    checkedMerge.push(checkbox);
  
  if (checkedMerge.length > 2)
    checkedMerge.shift().checked = false;
}
    
Main.add(function () {
  Domain.showDomain('{{$domain->_id}}');
});

</script>

<table class="main">
  <tr>
    <td style="width: 60%">
      <a href="#" onclick="Domain.showDomain(0)" class="button new">
        {{tr}}CDomain-title-create{{/tr}}
      </a>
      
      <a href="#" onclick="Domain.createDomainWithIdexTag()" class="button new">
        {{tr}}CDomain-title-create-with-idex-tag{{/tr}}
      </a>
    </td>
  </tr>
  <tr>
    <td id="vw_list_domains">      
      {{mb_include template=inc_list_domains}}
    </td>
    <td style="width: 40%" id="vw_domain">
      {{if $domain->_id}}
        {{mb_include template=inc_edit_domain}}
      {{/if}}
    </td>
  </tr>
</table>