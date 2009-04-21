{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<button type="button" class="change notext" onclick="EquivSelector.init('{{$line->_id}}','{{$line->_ref_produit->code_cip}}');">
  Equivalents
</button>
<script type="text/javascript">
  if(EquivSelector.oUrl) {
    EquivSelector.close();
  }
  EquivSelector.init = function(line_id, code_cip){
    this.sForm = "searchProd";
    this.sView = "produit";
    this.sCodeCIP = code_cip
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
