{{* $Id: configure.tpl 8820 2010-05-03 13:18:20Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage dPpmsi
 * @version $Revision: 8820 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
  Main.add(Control.Tabs.create.curry('tabs-pmsi', true));
</script>

{{if @$sejour->_id}}
  {{assign var=patient value=$sejour->_ref_patient}}
  <table class="form">
    <tr>
      <th class="title">
        <a style="float: right" title="Modifier le séjour" href="?m=dPplanningOp&amp;tab=vw_edit_sejour&amp;sejour_id={{$sejour->_id}}">
          <img src="images/icons/planning.png" alt="Planifier" />
        </a>
        <a style="float: right" title="Modifier les diagnostics" href="?m=dPpmsi&amp;tab=labo_groupage&amp;sejour_id={{$sejour->_id}}">
          <img src="images/icons/edit.png" alt="Planifier" />
        </a>
        <button style="float: right" type="button" class="button print" onclick="printDossierComplet('{{$sejour->_id}}');">Dossier complet</button>
         
        {{$sejour->_view}}
      </th>
    </tr>
    
    {{if ($dPconfig.dPpmsi.systeme_facturation == "siemens") && $patient->_ref_IPP}}
    <tr>
      <th id="IPP"> {{mb_include module=dPpmsi template=inc_ipp_form}} </th>
    </tr>
    {{/if}}
  
    {{if ($dPconfig.dPpmsi.systeme_facturation == "siemens")}}
    <tr>
      <td id="Numdos{{$sejour->sejour_id}}" class="text">
        {{mb_include module=dPpmsi template=inc_numdos_form}}
      </td>
    </tr>
    {{/if}}    
  </table>
  
  <ul id="tabs-pmsi" class="control_tabs">
    <li><a href="#PMSI">{{tr}}PMSI{{/tr}}</a></li>
    <li><a href="#ServeurActes" {{if !$sejour->_ref_operations}}class="empty"{{/if}}>Actes</a></li>
  </ul>
  
  <hr class="control_tabs" />
  
  <div id="PMSI" style="display: none;">
    {{mb_include template=inc_vw_pmsi}}
  </div>
  
  <div id="ServeurActes" style="display: none;">
    {{mb_include template=inc_vw_serveur_actes}}
  </div>
  
{{else}}
  <div class="small-info">Veuillez sélectionner un séjour dans la liste des séjours sur la gauche.</div>
{{/if}}