{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $line->substitute_for_id}}
  {{assign var=line_equivalent_id value=$line->substitute_for_id}}
{{else}}
  {{assign var=line_equivalent_id value=$line->_id}}
{{/if}}
<button type="button" class="change notext" onclick="EquivSelector.init('{{$line_equivalent_id}}');">
  Equivalents
</button>

<script type="text/javascript">
  if(EquivSelector.oUrl) {
    EquivSelector.close();
  }
  EquivSelector.init = function(line_id){
    this.sForm = "searchProd";
    this.sView = "produit";
    this.sLine = line_id;
    {{if $prescription->type == "sejour"}}
    this.sInLivret = "1";
    {{else}}
    this.sInLivret = "0";
    {{/if}}
    this.selfClose = false;
    this.pop();
  }
  EquivSelector.set = function(code, line_id){
    Prescription.addEquivalent(code, line_id,'{{$mode_pharma}}','{{$mode_protocole}}');
  }
</script>
