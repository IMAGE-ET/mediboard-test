{{* $Id: configure.tpl 10842 2010-12-08 21:57:35Z MyttO $ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: 10842 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=class value=CCategoryPrescription}}

<form name="EditConfig-{{$class}}" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="dosql" value="do_configure" />
  <input type="hidden" name="m" value="system" />

	<table class="form">

    <!-- Affichage du header et de la description des chapitres lors de l'impression des ordonnances -->
    {{mb_include module=system template=inc_config_bool var=show_header}}
    {{mb_include module=system template=inc_config_bool var=show_description}}
    {{mb_include module=system template=inc_config_bool var=show_only_cible}}
    <tr> 
      <td colspan="2">
        <table class="form">
          <tr>
          <th class="category">Chapitre</th>
          <th class="category">Impression ordonnance</th>
          <th class="category">Unité de prise</th>
					<th class="category">Jusqu'à la fin du séjour</th>
          </tr>
          {{include file="inc_configure_chapitre.tpl" var=dmi}}
          {{include file="inc_configure_chapitre.tpl" var=anapath}}
          {{include file="inc_configure_chapitre.tpl" var=biologie}}
          {{include file="inc_configure_chapitre.tpl" var=imagerie}}
          {{include file="inc_configure_chapitre.tpl" var=consult}}
          {{include file="inc_configure_chapitre.tpl" var=kine}}
          {{include file="inc_configure_chapitre.tpl" var=soin}}
          {{include file="inc_configure_chapitre.tpl" var=dm}}
          {{include file="inc_configure_chapitre.tpl" var=ds}}
        </table>
      </td>
    </tr>

	  <tr>
	    <td class="button" colspan="2">
	      <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
	    </td>
	  </tr>
	</table>
</form>
