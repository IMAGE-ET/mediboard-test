<?php
// ByteRun.com encode file decoder

// Open and read the content of the encoded file into a variable
$file = file_get_contents('BCBDci.php');

// Strip php tags
$file = str_replace('<?php', "", $file);
$file = str_replace('<?', "", $file);   // Make sure to get rid of short tags....
$file = str_replace('?>', "", $file);
$file = str_replace('$_F=__FILE__;', "", $file); // This stays the same in byterun.com encode files

// Create $_F variable
$_F=__FILE__; // Always in the byterun.com encode files

// Get $_X
preg_match('/\$_X=\'.*?\';/', $file, $match);

// Strip $_X='*'; from the string
$file = str_replace($match[0], "", $file);

//Create the $_X variable with the contents
$_X = str_replace('$_X=\'', "", $match[0]);
$_X = str_replace('\';', "", $_X);

// Change the Eval function
$file = str_replace('eval', 'echo ', $file);

// Function to eval the new string
function deval()
 {
  global $file, $_F, $_X;

  ob_start();
  eval($file);
  $contents = ob_get_contents();
  ob_end_clean();
  return($contents);
 }

// Run the code thru once
$file = deval();

// We know that $_X=base64_decode($_X); is always there so lets go ahead and run that
$_X = base64_decode($_X);

// Strip it from the string
$file = str_replace('$_X=base64_decode($_X);', "", $file);

// Get the contents for $_X=strtr(*);
preg_match('/\$_X=strtr.*?\'\);/', $file, $match2);

// Strip it
$file = str_replace($match2[0], "", $file);

// Run it
eval($match2[0]);

$_R=ereg_replace('__FILE__',"'".$_F."'",$_X);

$_R = ltrim($_R, '?><?php');
$_R = rtrim($_R, '?>');

echo $_R;
?>