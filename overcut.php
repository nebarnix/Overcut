<?php

set_time_limit(300);

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

$now = new DateTime();
$name = $now->getTimestamp();           // Unix Timestamp -- Since PHP 5.3;
$ext = "gco";
if (isset($_FILES['image']['name']))
   {
   $typeok = TRUE;
   switch($_FILES['image']['type'])
   {
      //case "image/gif": $src = imagecreatefromgif($saveto); break;
      //case "image/jpeg": // Both regular and progressive jpegs
      //case "image/pjpeg": $src = imagecreatefromjpeg($saveto); break;
      //case "image/png": $src = imagecreatefrompng($saveto); break;
      
      case "application/octet-stream": break;
      default: $typeok = FALSE; break;
       }
   if ($typeok)
      {  
      //print("Type ". $_FILES['image']['type'] ." OK\n");
      }
   else
      {
      print("Type ". $_FILES['image']['type'] ." bad\n");
      exit();
      }
   }
else
   {   
   print("No name set?");
   exit();
   }

$handle = fopen($_FILES['image']['tmp_name'], "r");
if ($handle) 
   {
   while (($line = fgets($handle)) !== false) 
      {
      //if($line
      // process the line read.
      //print('LINE: ' . $line);
      //print(strpos($line,'G2 '));
      //print("<br>");
      
      if(preg_match("/G[2|3] /i", $line))
         {
         print("You have G2 and/or G3 codes.\nThis script is too stupid to handle arcs and curves for now.\nPlease flatten ALL bezier curves.\nInkscape->extensions->modify path->flatten beziers\n");
         fclose($handle);
         exit();
         }   
      }
   fclose($handle);
   } 
else 
   {
   // error opening the file.
   print("File IO problem?");
   exit();
   }   
 
//header("Content-Disposition: attachment; filename=".$_FILES['image']['name'].".gco");
$overCutDist = $_POST['overcut'];
$currentPosX = 0;
$currentPosY = 0;
$trackDistance=0;
$cutterDown = 0;
$pointsX = array();
$pointsY = array();

print(";Created using Nebarnix's overcut script Ver 1.0<br>");
print(";http://nebarnix.com 2017<br>");
print(";Overcutting $overCutDist"."mm<br><br>");   
$handle = fopen($_FILES['image']['tmp_name'], "r");
if ($handle) 
   {
   while (($line = fgets($handle)) !== false)
      {
      $supressOutput = 0;
      // process the line read.
      if(preg_match("/G1 Z10 /i", $line)) //We just started a cut, record a few lines 
         {
         $savelines = 1; //Start saving G1 lines
         $trackDistance=0;
         $cutterDown = 1;
         //print("tracking!<br>");
         }
      else if(preg_match("/G1 Z0 /i", $line)) //We just stopped a cut, insert the few lines you saved at first
         {
         //do stuff
         if($cutterDown == 1)
            {
            //print("Done Cutting!<br>");
            }
         $cutterDown = 0;
         $savelines = 0;
         for($i = 0; $i < count($pointsX); $i++)
            {
            print("G1 X".number_format($pointsX[$i],4)." Y".number_format($pointsY[$i],4)." ;overcut line!<br>");
            }
         unset($pointsX);
         unset($pointsY);
         $pointsX = array();
         $pointsY = array();
         }
      else if(preg_match("/G4 P0\W+/i", $line))
         {
         $supressOutput = 1;   //don't output this crappy waste of gcode
         }
      else if(preg_match("/G[0|1]\W+X(-?\d+.?\d+) Y(-?\d+.?\d+)/i", $line,$matches))
         {         
         $prevPosX = $currentPosX;
         $prevPosY = $currentPosY;
         $currentPosX = $matches[1];
         $currentPosY = $matches[2];
         $distance = sqrt(pow($prevPosX-$currentPosX,2) + pow($prevPosY-$currentPosY,2));
         
         if($savelines == 1)
            {
            $distanceToGoal = $overCutDist - $trackDistance; 
            $trackDistance+=$distance;
            if($trackDistance >= $overCutDist) // > 1mm
               {
               $savelines = 0;
               //print("tracking done! $trackDistance<br>");
               //generate Gcode to move the remaining distance to the point we want
               //TODO
               //print(";$prevPosY,$currentPosY, $prevPosX,$currentPosX,$distance,$distanceToGoal<br>");
               
               $pointsX[] = $prevPosX - ($distanceToGoal*($prevPosX-$currentPosX))/$distance;
               $pointsY[] = $prevPosY - ($distanceToGoal*($prevPosY-$currentPosY))/$distance;
               }
            else
               {
               $pointsX[] = $currentPosX;
               $pointsY[] = $currentPosY;
               }
            }
         //print("$distance,$trackDistance<br>");
         }
      
      if($supressOutput == 0)
         print("<b>$line</b><br>"); //spit the line back out    
      }      
   fclose($handle);
   } 
else 
   {
   // error opening the file.
   print("File IO problem?");
   exit();
   } 
?>
