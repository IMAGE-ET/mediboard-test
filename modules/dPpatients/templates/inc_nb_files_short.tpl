{{*
 * $Id$
 *  
 * @category 
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

{{mb_default var=nb_files value=0}}
{{mb_default var=nb_docs value=0}}


(<span style="{{if $nb_files}}color:black; font-weight: bold;{{/if}}}">{{$nb_files}}</span>, <span style="{{if $nb_docs}}color:black; font-weight: bold;{{/if}}">{{$nb_docs}}</span>)