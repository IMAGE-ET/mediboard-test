{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="tbl">
<tr>
  <th>Libelle</th>
  <th>Quantite: administration</th>
</tr>
{{foreach from=$dossier key=date item=lines_by_cat}}
  <tr>
    <th colspan="2">{{$date|date_format:"%d/%m/%Y"}}</th>
  </tr>
  {{foreach from=$lines_by_cat key=chap item=lines}}
  <tr>
    <td colspan="2">
      <strong>
        {{if $chap == "medicament"}}
          Médicament
        {{elseif $chap == "perfusion"}}
          Perfusions
        {{else}}
          {{tr}}CCategoryPrescription.chapitre.{{$chap}}{{/tr}}
        {{/if}}
      </strong>
    </td>
  </tr>
  {{foreach from=$lines key=line_id item=administrations}}
    {{assign var=line value=$list_lines.$chap.$line_id}}     
     <tr>
       <td>
         {{if $line->_class_name == "CPrescriptionLineMedicament"}}
           {{$line->_ucd_view}} 
           <span style="opacity: 0.7; font-size: 0.8em;">
           {{if $line->_forme_galenique}}({{$line->_forme_galenique}}){{/if}}
           </span>
         {{elseif $line->_class_name == "CPerfusionLine"}}
           {{$line->_ucd_view}}           
           {{if $line->_class_name == "CPerfusionLine"}}
             (Perfusion: {{$line->_ref_perfusion->_view}})
           {{/if}}
         {{else}}
           {{$line->_view}}
         {{/if}}
       </td>
       <td class="text">  
         {{foreach from=$administrations key=quantite item=_administrations_by_quantite}}
           {{$quantite}} 
           {{if $line->_class_name == "CPrescriptionLineMedicament"}}
             {{if $line->_ref_produit_prescription->_id}}
               {{$line->_ref_produit_prescription->unite_prise}}
             {{else}}
               {{$line->_ref_produit->libelle_unite_presentation}}
             {{/if}}
           {{elseif $line->_class_name != "CPerfusionLine"}}
             {{$line->_unite_prise}}
           {{else}}
             {{$line->_unite_administration}}
           {{/if}}: 
           {{foreach from=$_administrations_by_quantite item=_administration name="foreach_adm"}}
           {{$_administration->dateTime|date_format:$dPconfig.time}}
					 par {{$_administration->_ref_administrateur->_view}}
					 {{if !$smarty.foreach.foreach_adm.last}},{{/if}}
					 
           {{/foreach}}
           <br />
         {{/foreach}}
       </td>  
       </tr>
    {{/foreach}}
  {{/foreach}}
{{/foreach}}
</table>