<?php
/**
* Telegram Bot example for Public Transport of Napoli-Palermo-Trento-Roma (Italy)
* API by https://github.com/transitland/transitland-datastore
* Thanks, many thanks to Andrea Borruso for http://blog.spaziogis.it/2016/03/02/transiland-per-mettere-insieme-e-dare-vita-ai-dati-sui-trasporti/
* @author @Piersoft
Funzionamento
- invio location
- invio fermata più vicina come risposta

*/

include("Telegram.php");

class mainloop{
const MAX_LENGTH = 4096;
function start($telegram,$update)
{

	date_default_timezone_set('Europe/Rome');
	$today = date("Y-m-d H:i:s");
	$db = new PDO(DB_NAME);

	$text = $update["message"] ["text"];
	$chat_id = $update["message"] ["chat"]["id"];
	$user_id=$update["message"]["from"]["id"];
	$location=$update["message"]["location"];
	$reply_to_msg=$update["message"]["reply_to_message"];

	$this->shell($telegram, $db,$text,$chat_id,$user_id,$location,$reply_to_msg);
	$db = NULL;

}

//gestisce l'interfaccia utente
 function shell($telegram,$db,$text,$chat_id,$user_id,$location,$reply_to_msg)
{
	date_default_timezone_set('Europe/Rome');
	$today = date("Y-m-d H:i:s");

		if ($text == "/start" || $text == "©️info") {
		$reply = "Benvenuto. Invia la tua posizione cliccando sulla graffetta (📎) e ti indicherò le fermate più vicine nel raggio di 200 metri e relative linee ed orari, per le città di Lecce, Napoli, Palermo, Roma e Trento.";
		$reply .= "\nI dati dell'Aziende, le licenze opendata cc-by o iodl2.0, le API Transit.land, sono ricavabili su http://blog.spaziogis.it/2016/03/02/transiland-per-mettere-insieme-e-dare-vita-ai-dati-sui-trasporti/";
		$reply .= "\nProgetto sviluppato da @Piersoft. Si declina ogni responsabilità sulla veridicità dei dati.";


		$reply .= "\nWelcome. Send your location by clicking on the paperclip (📎) and will show you the nearest stops within 200 meters, and its lines and schedules, for the cities of Lecce, Naples, Palermo, Rome and Trento and many others around the world. The API data of Transit.land, licenses OpenData cc-by or iodl2.0, can be found on http://blog.spaziogis.it/2016/03/02/transiland-per-mettere-insieme-e-dare-vita-ai-dati-sui-trasporti/ \nProject developed by @Piersoft. We accept no liability for the accuracy of data";


		$content = array('chat_id' => $chat_id, 'text' => $reply,'disable_web_page_preview'=>true);
		$telegram->sendMessage($content);
/*
		$forcehide=$telegram->buildKeyBoardHide(true);
		$content = array('chat_id' => $chat_id, 'text' => "", 'reply_markup' =>$forcehide, 'reply_to_message_id' =>$bot_request_message_id);
		$bot_request_message=$telegram->sendMessage($content);
		*/
		$log=$today. ",new chat started," .$chat_id. "\n";
			$this->create_keyboard($telegram,$chat_id);
			exit;
	}elseif ($text == "/location" || $text == "🌐location") {

		$option = array(array($telegram->buildKeyboardButton("Invia la tua posizione / send your location", false, true)) //this work
											);
	// Create a permanent custom keyboard
	$keyb = $telegram->buildKeyBoard($option, $onetime=false);
	$content = array('chat_id' => $chat_id, 'reply_markup' => $keyb, 'text' => "Attiva la localizzazione sul tuo smartphone / Turn on your GPS");
	$telegram->sendMessage($content);
	//	$this->create_keyboard($telegram,$chat_id);
	exit;
	}
		elseif($location!=null)
		{

			$this->location_manager($db,$telegram,$user_id,$chat_id,$location);
			exit;

		}elseif (strpos($text,'/') !== false ){

			$content = array('chat_id' => $chat_id, 'text' => "Attendere per favore.. / Please Wait..",'disable_web_page_preview'=>true);
			$telegram->sendMessage($content);

		$text=str_replace("🏁 ","",$text);
		$text=str_replace("/","",$text);
		$text=str_replace("___","<",$text);
		$text=str_replace("__","~",$text);
		$text=str_replace("_","-",$text);

		date_default_timezone_set("Europe/Rome");
		$ora=date("H:i:s", time());
		$ora2=date("H:i:s", time()+60*60);

		$json_string = file_get_contents("https://transit.land/api/v1/onestop_id/".$text);
		$parsed_json = json_decode($json_string);
		$count = 0;
		$countl = 0;
		$namedest=$parsed_json->{'name'};
		$IdFermata="";

		foreach($parsed_json->{'routes_serving_stop'} as $data=>$csv1){
		 $count = $count+1;
		}
		//  echo $count."/n";


		  for ($i=0;$i<$count;$i++){

		$countl=0;

		$json_string1 = file_get_contents("https://transit.land/api/v1/schedule_stop_pairs?destination_onestop_id=".$text."&origin_departure_between=".$ora.",".$ora2);
		$parsed_json1 = json_decode($json_string1);


		foreach($parsed_json1->{'schedule_stop_pairs'} as $data12=>$csv11){
		 $countl = $countl+1;
		}

		$start=0;
		if ($countl == 0){
			$content = array('chat_id' => $chat_id, 'text' => "Non ci sono arrivi nella prossima ora / There are no arrivals in the next hour ",'disable_web_page_preview'=>true);
			$telegram->sendMessage($content);
				$this->create_keyboard($telegram,$chat_id);
			exit;
		}else{
		    $start=1;


		}

		//echo $countl;
  $distanza=[];
	$json_string2 = file_get_contents("https://transit.land/api/v1/onestop_id/".$parsed_json1->{'schedule_stop_pairs'}[$i]->{'origin_onestop_id'});
	$parsed_json2 = json_decode($json_string2);
	$name=$parsed_json2->{'name'};

	for ($l=0;$l<$countl;$l++)
		{
		//	if ( ($parsed_json1->{'schedule_stop_pairs'}[$l]->{'route_onestop_id'}) == $parsed_json->{'routes_serving_stop'}[$i]->{'route_onestop_id'})
		//	{
			$distanza[$l]['orari']=$parsed_json1->{'schedule_stop_pairs'}[$l]->{'destination_arrival_time'};
		//	}
		}
		sort($distanza);

		for ($l=0;$l<$countl;$l++)
		  {

		  if ( ($parsed_json1->{'schedule_stop_pairs'}[$l]->{'route_onestop_id'}) == $parsed_json->{'routes_serving_stop'}[$i]->{'route_onestop_id'}){
					$temp_c1 .="Linea: / Line:  ".$parsed_json->{'routes_serving_stop'}[$i]->{'route_name'}." arrivo: / arrive:  ";

		  //    $temp_c1 .=$parsed_json1->{'schedule_stop_pairs'}[$l]->{'destination_arrival_time'};
			$temp_c1 .=$distanza[$l]['orari']."\nproveniente da: / from:  ".$name;
		  $temp_c1 .="\n";

		      }

		}
		}

if ($start==1){
	$content = array('chat_id' => $chat_id, 'text' => "Linee in arrivo nella prossima ora a / incoming lines in the next hour at ".$namedest."\n",'disable_web_page_preview'=>true);
	$telegram->sendMessage($content);
}
	$chunks = str_split($temp_c1, self::MAX_LENGTH);
	foreach($chunks as $chunk) {
	// $forcehide=$telegram->buildForceReply(true);
	//chiedo cosa sta accadendo nel luogo
	$content = array('chat_id' => $chat_id, 'text' => $chunk, 'reply_to_message_id' =>$bot_request_message_id,'disable_web_page_preview'=>true);
	$telegram->sendMessage($content);

	}

	//$telegram->sendMessage($content);
	//	echo $temp_l1;

	//if ($temp_l1 ==="") {
	//	$content = array('chat_id' => $chat_id, 'text' => "Nessuna fermata nei paraggi", 'reply_to_message_id' =>$bot_request_message_id);
	//		$telegram->sendMessage($content);

	//}
	$today = date("Y-m-d H:i:s");

	$log=$today. ",fermate sent," .$chat_id. "\n";
	$this->create_keyboard($telegram,$chat_id);
	exit;

	}



		else{



			$forcehide=$telegram->buildKeyBoardHide(true);
			$content = array('chat_id' => $chat_id, 'text' => "Comando errato / Wrong command", 'reply_markup' =>$forcehide);
			$telegram->sendMessage($content);
			$this->create_keyboard($telegram,$chat_id);
			exit;

		}

		file_put_contents(LOG_FILE, $log, FILE_APPEND | LOCK_EX);

}

function create_keyboard($telegram, $chat_id)
 {
	 			$option = array(["🌐location","©️info"]);
				$keyb = $telegram->buildKeyBoard($option, $onetime=false);
				$content = array('chat_id' => $chat_id, 'reply_markup' => $keyb, 'text' => "[Invia la tua posizione]");
				$telegram->sendMessage($content);

 }
/*
// Crea la tastiera
 function create_keyboard($telegram, $chat_id)
	{
		$forcehide=$telegram->buildKeyBoardHide(true);
		$content = array('chat_id' => $chat_id, 'text' => "Invia la tua posizione cliccando sulla graffetta (📎) in basso\nSend your location by clicking on the paperclip (📎) down.", 'reply_markup' =>$forcehide);
		$telegram->sendMessage($content);

	}

*/

function location_manager($db,$telegram,$user_id,$chat_id,$location)
	{

			$lng=$location["longitude"];
			$lat=$location["latitude"];
      $r=200;
			$content = array('chat_id' => $chat_id, 'text' => "Attendere per favore.. / Please Wait..", 'disable_web_page_preview'=>true);
			$telegram->sendMessage($content);
			//sleep(1);
			//rispondo
			$response=$telegram->getData();

			$bot_request_message_id=$response["message"]["message_id"];
			$time=$response["message"]["date"]; //registro nel DB anche il tempo unix


			      $json_string = file_get_contents("https://transit.land/api/v1/stops?lon=".$lng."&lat=".$lat."&r=".$r);
			      $parsed_json = json_decode($json_string);
			      $count = 0;
			      $countl = [];
						  $idfermate = [];
			      foreach($parsed_json->{'stops'} as $data=>$csv1){
			         $count = $count+1;
			      }





			    //  $r10=$r/10;
			  //    echo "Fermate più vicine rispetto a ".$lat."/".$lon." in raggio di ".$r." metri con relative linee urbane ed orari arrivi\n";
			  //    $count=1;
			    $IdFermata="";
			//    echo $count;
					$option=[];
			  //  var_dump($parsed_json->{'stops'}[0]->{'name'});

			  for ($i=0;$i<$count;$i++){

			    foreach($parsed_json->{'stops'}[$i]->{'routes_serving_stop'} as $data=>$csv1){
			       $countl[$i] = $countl[$i]+1;
			      }

				//		array_push($option,$parsed_json->{'stops'}[$i]->{'onestop_id'});
						$option[$i]=$parsed_json->{'stops'}[$i]->{'onestop_id'};
						$onestop=str_replace("-","_",$parsed_json->{'stops'}[$i]->{'onestop_id'});
						$onestop=str_replace("~","__",	$onestop);
						$onestop=str_replace("<","___",	$onestop);



			      //  echo $countl[$i];
			  $temp_c1 .="\n";
			      $temp_c1 .="Fermata: / Stop: ".$parsed_json->{'stops'}[$i]->{'name'};
						$temp_c1 .="\nID Fermata: / ID Stop: /".$onestop."";

						if ($parsed_json->{'stops'}[$i]->{'tags'}->{'wheelchair_boarding'} != null) $temp_c1 .="\nAccesso in carrozzina: / Wheelchair boarding: ".$parsed_json->{'stops'}[$i]->{'tags'}->{'wheelchair_boarding'};
			      $temp_c1 .="\nVisualizzala su : / See on:\nhttp://www.openstreetmap.org/?mlat=".$parsed_json->{'stops'}[$i]->{'geometry'}->{'coordinates'}[1]."&mlon=".$parsed_json->{'stops'}[$i]->{'geometry'}->{'coordinates'}[0]."#map=19/".$parsed_json->{'stops'}[$i]->{'geometry'}->{'coordinates'}[1]."/".$parsed_json->{'stops'}[$i]->{'geometry'}->{'coordinates'}[0];

			  		$temp_c1 .="\n";

			}

 $chunks = str_split($temp_c1, self::MAX_LENGTH);
 foreach($chunks as $chunk) {
	// $forcehide=$telegram->buildForceReply(true);
		 //chiedo cosa sta accadendo nel luogo
		 $content = array('chat_id' => $chat_id, 'text' => $chunk, 'reply_to_message_id' =>$bot_request_message_id,'disable_web_page_preview'=>true);
		 $telegram->sendMessage($content);

 }


if ($count >0){

	$reply ="Per vedere queste fermate su mappa:\n";
	$reply.="To see these stops on the map: \n";
	$reply .="http://www.piersoft.it/panarotre/locator.php?lon=".$lng."&lat=".$lat."&r=500";
	$content = array('chat_id' => $chat_id, 'text' => $reply, 'reply_markup' =>$forcehide,'disable_web_page_preview'=>true);
	$telegram->sendMessage($content);

//	$forcehide=$telegram->buildKeyBoardHide(true);
//	$content = array('chat_id' => $chat_id, 'text' => "oppure clicca su ID Fermata per gli orari", 'reply_markup' =>$forcehide);
//	$telegram->sendMessage($content);

}else{
	$content = array('chat_id' => $chat_id, 'text' => "Non ci sono fermate gestite / There are stops managed", 'reply_markup' =>$forcehide,'disable_web_page_preview'=>true);
	$telegram->sendMessage($content);
}
	$today = date("Y-m-d H:i:s");

	$log=$today. ",fermatelocation sent," .$chat_id. "\n";
	$this->create_keyboard($telegram,$chat_id);
	$optionf=array([]);
	for ($i=0;$i<$count;$i++){
		array_push($optionf,["🏁 /".$option[$i]]);

	}
			$keyb = $telegram->buildKeyBoard($optionf, $onetime=false);
			$content = array('chat_id' => $chat_id, 'reply_markup' => $keyb, 'text' => "[Clicca su 🏁 della fermata / Click on the stop 🏁]");
			$telegram->sendMessage($content);

	exit;

	}


}

?>
