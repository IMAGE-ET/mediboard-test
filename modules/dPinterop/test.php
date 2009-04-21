<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPinterop
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$mbPoso = new CBcbPosologie();

$ClassePoso=$mbPoso->distObj;
//$ClassePoso=new BCBPosologie();
$Result=$ClassePoso->ChargementDetail("3159333",1);
mbTrace($Result);
if($Result > 0)
{
                $Quantite1=$ClassePoso->ChargementGetData(1);
                echo "Quantite1=".$Quantite1."<BR>";
                $Quantite2=$ClassePoso->ChargementGetData(2);
                echo "Quantite2=".$Quantite2."<BR>";
                $Code_Unite_De_Prise=$ClassePoso->ChargementGetData(3);
                echo "Code_Unite_De_Prise=".$Code_Unite_De_Prise."<BR>";
                $Code_Unite_De_Prise2=$ClassePoso->ChargementGetData(4);
                echo "Code_Unite_De_Prise2=".$Code_Unite_De_Prise2."<BR>";
                $P_Kg=$ClassePoso->ChargementGetData(5);
                echo "P_Kg=".$P_Kg."<BR>";
                $Adequation_UP1_UP2=$ClassePoso->ChargementGetData(6);
                echo "Adequation_UP1_UP2=".$Adequation_UP1_UP2."<BR>";
                $Combien1=$ClassePoso->ChargementGetData(7);
                echo "Combien1=".$Combien1."<BR>";
                $Combien2=$ClassePoso->ChargementGetData(8);
                echo "Combien2=".$Combien2."<BR>";
                $Tous_Les=$ClassePoso->ChargementGetData(9);
                echo "Tous_Les=".$Tous_Les."<BR>";
                $Code_Duree1=$ClassePoso->ChargementGetData(10);
                echo "Code_Duree1=".$Code_Duree1."<BR>";
                $Code_Moment=$ClassePoso->ChargementGetData(11);
                echo "Code_Moment=".$Code_Moment."<BR>";
                $Pendant1=$ClassePoso->ChargementGetData(12);
                echo "Pendant1=".$Pendant1."<BR>";
                $Pendant2=$ClassePoso->ChargementGetData(13);
                echo "Pendant2=".$Pendant2."<BR>";
                $Code_Duree2=$ClassePoso->ChargementGetData(14);
                echo "Code_Duree2=".$Code_Duree2."<BR>";
                $Maximum=$ClassePoso->ChargementGetData(15);
                echo "Maximum=".$Maximum."<BR>";
                $Maximum_Pds=$ClassePoso->ChargementGetData(16);
                echo "Maximum_Pds=".$Maximum_Pds."<BR>";
                $Code_Duree3=$ClassePoso->ChargementGetData(17);
                echo "Code_Duree3=".$Code_Duree3."<BR>";
                $Code_Prise1=$ClassePoso->ChargementGetData(18);
                echo "Code_Prise1=".$Code_Prise1."<BR>";
                $Code_Prise2=$ClassePoso->ChargementGetData(19);
                echo "Code_Prise2=".$Code_Prise2."<BR>";
                $Nombre_Unites=$ClassePoso->ChargementGetData(20);
                echo "Nombre_Unites=".$Nombre_Unites."<BR>";
                $Code_Posologie=$ClassePoso->ChargementGetData(21);
                echo "Code_Posologie=".$Code_Posologie."<BR>";
                $CodeCIP=$ClassePoso->ChargementGetData(22);
                echo "CodeCIP=".$CodeCIP."<BR>";
                $Terrain=$ClassePoso->ChargementGetData(23);
                echo "Terrain=".$Terrain."<BR>";
                $Code_Par=$ClassePoso->ChargementGetData(24);
                echo "Code_Par=".$Code_Par."<BR>";
                $Commentaire=$ClassePoso->ChargementGetData(25);
                echo "Commentaire=".nl2br($Commentaire)."<BR>";
               
                                  
}
 echo "<BR>***********************************************************************<BR>";
 echo " recalcul Posologie           :<BR>";
 echo $ClassePoso->DecodageDetail( 2*$Quantite1 ,
                   4*$Quantite2 ,
                    $Code_Unite_De_Prise ,
                     $Code_Unite_De_Prise2 ,
                      $P_Kg ,
                       $Adequation_UP1_UP2,
                        $Combien1 ,
                         5*$Combien2 ,
                          $Tous_Les,
                           $Code_Duree1 ,
                            $Code_Moment ,
                            $Pendant1 ,
                              $Pendant2 ,
                               $Code_Duree2 ,
                                $Maximum,
                                 $Maximum_Pds ,
                                  $Code_Duree3 ,
                                   $Code_Prise1 ,
                                    $Code_Prise2 ,
                                     $Nombre_Unites ,
                                      $Code_Posologie ,
                                       $CodeCIP ,
                                        $Terrain ,
                                         $Code_Par ,
                                          $Commentaire );


?>