{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage dPcabinet
 * @version $Revision: 11962 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="editConfig-CConsultAnesth" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="m" value="system" />
  <input type="hidden" name="dosql" value="do_configure" />
  
  <table class="form">

    {{assign var="class" value="CConsultAnesth"}}
        
    <tr>
      <th class="category" colspan="2">{{tr}}{{$class}}{{/tr}}</th>
      
      <td rowspan="100">
        <div class="big-info">
          <b>Format des champs auto :</b>
          <ul>
            <li><code>%N</code> - Nom praticien interv</li>
            <li><code>%P</code> - Prénom praticien interv</li>
            <li><code>%S</code> - Initiales praticien interv</li>
            <li><code>%L</code> - Libellé intervention</li>
            <li><code>%I</code> - Jour intervention</li>
            <li><code>%i</code> - Heure intervention</li>
            <li><code>%E</code> - Jour d'entrée</li>
            <li><code>%e</code> - Heure d'entrée</li>
            <li><code>%T</code> - Type de séjour (A, O, E...)</li>
          </ul>
        </div>
      </td>
    </tr>

    {{mb_include module=system template=inc_config_enum var=feuille_anesthesie values=print_fiche|print_fiche1}}
    {{mb_include module=system template=inc_config_str  var=format_auto_motif}}
    {{mb_include module=system template=inc_config_str  var=format_auto_rques}}
    {{mb_include module=system template=inc_config_bool var=show_mallampati}}
    {{mb_include module=system template=inc_config_bool var=view_premedication}}
    {{mb_include module=system template=inc_config_bool var=show_facteurs_risque}}
    
    <tr>
      <td class="button" colspan="2">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>