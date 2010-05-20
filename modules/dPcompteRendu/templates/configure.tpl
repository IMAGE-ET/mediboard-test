{{* $Id: configure.tpl 8217 2010-03-05 10:49:05Z phenxdesign $ *}}

{{*
 * @package Mediboard
 * @subpackage dPcompteRendu
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="editConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="dosql" value="do_configure" />
  <input type="hidden" name="m" value="system" />

  <table class="form">
    {{assign var="class" value="CCompteRendu"}}
    {{assign var="var" value="pdf_thumbnails"}}
		<tr>
			<th class="category" colspan="2">
				{{tr}}Configuration d'impression{{/tr}}
			</th>
		</tr>
		
		{{if $dompdf_installed}}
      {{mb_include module=system template=inc_config_bool}}
      {{assign var="var" value="same_print"}}
      {{mb_include module=system template=inc_config_bool}}
	  {{else}}
		  <tr>
		  	<td colspan="2">
		  		<div class="small-error">La librairie DOMPDF n'est pas installée</div>
		  	</td>
		  </tr>
		{{/if}}

    <tr>
      <td class="button" colspan="2">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
  
  {{*include file="benchmark_pdf.tpl"*}}
</form>
