{{* $Id: configure.tpl 7993 2010-02-03 16:55:27Z MyttO $ *}}

{{*
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision: 7993 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="EditConfig-gui" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="dosql" value="do_configure" />
  <input type="hidden" name="m" value="system" />

  <table class="form">
    <tr>
      <th class="title" colspan="2">Configuration des onglets
    </tr>

		{{assign var=class value=occupation_surveillance}}
    {{mb_include module=system template=inc_config_category}}
		{{mb_include module=system template=inc_config_str var=faible}}
    {{mb_include module=system template=inc_config_str var=eleve}}

    {{assign var=class value=occupation_technicien}}
    {{mb_include module=system template=inc_config_category}}
 	  {{mb_include module=system template=inc_config_str var=faible}}
    {{mb_include module=system template=inc_config_str var=eleve}}
    
    {{assign var=class value=repartition}}
    {{mb_include module=system template=inc_config_category}}
    {{mb_include module=system template=inc_config_bool var=show_tabs}}
    
    {{assign var=class value=recusation}}
    {{mb_include module=system template=inc_config_category}}
    {{mb_include module=system template=inc_config_bool var=use_recuse}}
		
    <tr>
      <td class="button" colspan="100">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>
