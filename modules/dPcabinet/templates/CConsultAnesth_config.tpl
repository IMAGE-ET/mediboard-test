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
            <li><tt>%N</tt> - Nom praticien interv</li>
            <li><tt>%P</tt> - Prénom praticien interv</li>
            <li><tt>%S</tt> - Initiales praticien interv</li>
            <li><tt>%L</tt> - Libellé intervention</li>
            <li><tt>%I</tt> - Jour intervention</li>
            <li><tt>%i</tt> - Heure intervention</li>
            <li><tt>%E</tt> - Jour d'entrée</li>
            <li><tt>%e</tt> - Heure d'entrée</li>
            <li><tt>%T</tt> - Type de séjour (A, O, E...)</li>
          </ul>
        </div>
      </td>
    </tr>
    
    <tr>
      {{assign var="var" value="feuille_anesthesie"}}
      
      <th>
        <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
          {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
        </label>  
      </th>
      <td>
        <select class="str" name="{{$m}}[{{$class}}][{{$var}}]">
          <option value="print_fiche"  {{if "print_fiche" == $conf.$m.$class.$var}} selected="selected" {{/if}}>{{tr}}config-{{$m}}-{{$class}}-{{$var}}-print_fiche{{/tr}}</option>
          <option value="print_fiche1" {{if "print_fiche1" == $conf.$m.$class.$var}} selected="selected" {{/if}}>{{tr}}config-{{$m}}-{{$class}}-{{$var}}-print_fiche1{{/tr}}</option>
        </select>
      </td>
    </tr>
    {{assign var="var" value="format_auto_motif"}}
    <tr>
      <th>
        <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
          {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
        </label>  
      </th>
      <td>
        <input type="text" name="{{$m}}[{{$class}}][{{$var}}]" value="{{$conf.$m.$class.$var}}" />
      </td>
    </tr>
    
    {{assign var="var" value="format_auto_rques"}}
    <tr>
      <th>
        <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
          {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
        </label>  
      </th>
      <td>
        <input type="text" name="{{$m}}[{{$class}}][{{$var}}]" value="{{$conf.$m.$class.$var}}" />
      </td>
    </tr>
    
    {{mb_include module=system template=inc_config_bool var=show_mallampati}}
    
    
    {{mb_include module=system template=inc_config_bool var=view_premedication}}
    {{mb_include module=system template=inc_config_bool var=show_facteurs_risque}}
    {{mb_include module=system template=inc_config_bool var=multiple_dossiers_anesth}}
    
    <tr>
      <td class="button" colspan="2">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>