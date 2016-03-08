 <?php

//Wrapper delle fonti #emergenzeprato e preparazione dati di interesse per i vari bot
//questa classe deve essere istanziata nei vari JOB che vogliono usare i dati
//by MT


class getdata {

  public function get_fermateba($lat,$lon,$r)
  {


          $json_string = file_get_contents("https://transit.land/api/v1/stops?lon=".$lon."&lat=".$lat."&r=".$r);
          $parsed_json = json_decode($json_string);
          $count = 0;
          $countl = [];
          foreach($parsed_json as $data=>$csv1){
             $count = $count+1;
          }
        //  $r10=$r/10;
          echo "<strong>Fermate più vicine rispetto a ".$lat."/".$lon." in raggio di ".$r." metri con relative linee urbane ed orari arrivi</strong><br><br>\n";
      //    $count=1;
        $IdFermata="";
    //    echo $count;

      //  var_dump($parsed_json->{'stops'}[0]->{'name'});

      for ($i=0;$i<$count;$i++){

        foreach($parsed_json->{'stops'}[$i]->{'routes_serving_stop'} as $data=>$csv1){
           $countl[$i] = $countl[$i]+1;
          }
          //  echo $countl[$i];
      $temp_c1 .="\n<br>";
          $temp_c1 .="<b>Fermata: ".$parsed_json->{'stops'}[$i]->{'name'}."</b>\n<br>Id Fermata: ".$parsed_json->{'stops'}[$i]->{'onestop_id'};
          $temp_c1 .="\n<br>Visualizzala su :\nhttp://www.openstreetmap.org/?mlat=".$parsed_json->{'stops'}[$i]->{'geometry'}->{'coordinates'}[1]."&mlon=".$parsed_json->{'stops'}[$i]->{'geometry'}->{'coordinates'}[0]."#map=19/".$parsed_json->{'stops'}[$i]->{'geometry'}->{'coordinates'}[1]."/".$parsed_json->{'stops'}[$i]->{'geometry'}->{'coordinates'}[0];
        $temp_c1 .="\n<br>";

          for ($l=0;$l<$countl[$i];$l++)
            {


          $temp_c1 .="\n<br>";


                $IdFermata=$parsed_json->{'stops'}[$i]->{'routes_serving_stop'}[$l]->{'route_onestop_id'};
          //      echo $IdFermata;
                $json_string1 = file_get_contents("http://transit.land/api/v1/schedule_stop_pairs?route_onestop_id=".$IdFermata);
                $parsed_json1 = json_decode($json_string1);

                $countf = 0 ;

                $h = "1";// Hour for time zone goes here e.g. +7 or -4, just remove the + or -
                $hm = $h * 60;
                $ms = $hm * 60;
                date_default_timezone_set('UTC');

                  $time =$parsed_json1->{'schedule_stop_pairs'}[$i]->{'origin_arrival_time'}; //registro nel DB anche il tempo unix

                  $temp_c1 .="\n<br><strong>Linea: ".$parsed_json->{'stops'}[$i]->{'routes_serving_stop'}[$l]->{'route_name'}." arrivo: ".$time."</strong>";

            $temp_c1 .="\n<br>";



            }



    }
   return $temp_c1;

  }

  public function get_lineeba()
  {


      $json_string = file_get_contents("http://bari.opendata.planetek.it/OrariBus/v2.1/OpenDataService.svc/REST/rete/Linee");
      $parsed_json = json_decode($json_string);
      $count = 0;
      $countl = [];
      foreach($parsed_json as $data=>$csv1){
         $count = $count+1;
      }
  //    $count=1;
    $IdLinea="";
    //  echo $count;
  for ($i=0;$i<$count;$i++){
    foreach($parsed_json[$i]->{'Id Linea'} as $data=>$csv1){
       $countl[$i] = $countl[$i]+1;
      }
  $temp_c1 .="Percorso: ".$parsed_json[$i]->{'DescrizioneLinea'}."\n<br>Id Linea: ".$parsed_json[$i]->{'IdLinea'};
  $temp_c1 .="\n\n<br><br>";


  $IdLinea=$parsed_json[$i]->{'IdLinea'};
  $json_string1 = file_get_contents("http://bari.opendata.planetek.it/OrariBus/v2.1/OpenDataService.svc/REST/rete/FermateLinea/".$IdLinea);
  $parsed_json1 = json_decode($json_string1);
  for ($f=0;$f<$countl;$f++){
  $temp_c1 .="Direzione: ".$parsed_json1[$f]->{'Direzione'}."\n<br>Id Fermata: ".$parsed_json1[$f]->{'IdFermata'};
}
}
   return $temp_c1;

  }

  public function get_parcheggi()
  {


      $json_string = file_get_contents("http://bari.opendata.planetek.it/parcheggi/1.0/Parcheggi.svc/REST/parcheggi");
      $parsed_json = json_decode($json_string);
      $count = 0;
    	foreach($parsed_json as $data=>$csv1){
    	   $count = $count+1;
    	}
    //  echo $count;
for ($i=0;$i<=1;$i++){
      $temp_c1 .= "Nome parcheggio: ".$parsed_json[$i]->{'NomeParcheggio'}.",\nPosti liberi: ".$parsed_json[$i]->{'DatiVariabili'}->{'NumPostiLiberi'};
    //  var_dump($parsed_json);
$temp_c1 .="\n<br>";
/*
      $time=$parsed_json[$i]->{'DatiVariabili'}->{'OraRicezioneAggiornamento'}; //registro nel DB anche il tempo unix
      $time=str_replace("/Date(","",$time);
      $time=str_replace("+0200)/","",$time);

      $timec=gmdate("d-m-Y\TH:i:s\Z", $time+($ms));
      $timec=str_replace("T"," ",$timec);
      $timec=str_replace("Z"," ",$timec);
      */
      $lat.="\n".$parsed_json[$i]->{'PosizioneGeografica'}->{'Latitudine'}; //registro nel DB anche il tempo unix
      $lon.=$parsed_json[$i]->{'PosizioneGeografica'}->{'Longitudine'}; //registro nel DB anche il tempo unix
      $lat =str_replace(",",".",$lat);
      $lon =str_replace(",",".",$lon);
      $coordinate=$lat.",".$lon;
//      $temp_c4 = "\n".$parsed_json->{'forecast'}->{'txt_forecast'}->{'forecastday'}[3]->{'title'}.", ".$parsed_json->{'forecast'}->{'txt_forecast'}->{'forecastday'}[3]->{'fcttext_metric'};
//      $temp_c5 = "\n".$parsed_json->{'forecast'}->{'txt_forecast'}->{'forecastday'}[4]->{'title'}.", ".$parsed_json->{'forecast'}->{'txt_forecast'}->{'forecastday'}[4]->{'fcttext_metric'};
//      $temp_c6 = "\n".$parsed_json->{'forecast'}->{'txt_forecast'}->{'forecastday'}[5]->{'title'}.", ".$parsed_json->{'forecast'}->{'txt_forecast'}->{'forecastday'}[5]->{'fcttext_metric'};

}
   return $temp_c1.$coordinate;

  }

	//monitoraggio temperatura
	public function get_forecast($where)
	{

		switch ($where) {

			 //Lecce centro
			 case "Lecce":
			$json_string = file_get_contents("http://api.wunderground.com/api/b3f95b06a21229ff/forecast/lang:IT/q/pws:IPUGLIAL7.json");
			$parsed_json = json_decode($json_string);
			$temp_c1 = $parsed_json->{'forecast'}->{'txt_forecast'}->{'forecastday'}[0]->{'title'}.", ".$parsed_json->{'forecast'}->{'txt_forecast'}->{'forecastday'}[0]->{'fcttext_metric'};
			$temp_c2 = "\n".$parsed_json->{'forecast'}->{'txt_forecast'}->{'forecastday'}[1]->{'title'}.", ".$parsed_json->{'forecast'}->{'txt_forecast'}->{'forecastday'}[1]->{'fcttext_metric'};
			$temp_c3 = "\n".$parsed_json->{'forecast'}->{'txt_forecast'}->{'forecastday'}[2]->{'title'}.", ".$parsed_json->{'forecast'}->{'txt_forecast'}->{'forecastday'}[2]->{'fcttext_metric'};
			$temp_c4 = "\n".$parsed_json->{'forecast'}->{'txt_forecast'}->{'forecastday'}[3]->{'title'}.", ".$parsed_json->{'forecast'}->{'txt_forecast'}->{'forecastday'}[3]->{'fcttext_metric'};
			$temp_c5 = "\n".$parsed_json->{'forecast'}->{'txt_forecast'}->{'forecastday'}[4]->{'title'}.", ".$parsed_json->{'forecast'}->{'txt_forecast'}->{'forecastday'}[4]->{'fcttext_metric'};
			$temp_c6 = "\n".$parsed_json->{'forecast'}->{'txt_forecast'}->{'forecastday'}[5]->{'title'}.", ".$parsed_json->{'forecast'}->{'txt_forecast'}->{'forecastday'}[5]->{'fcttext_metric'};

		break;
		case "Lecceoggi":
	 $json_string = file_get_contents("http://api.wunderground.com/api/b3f95b06a21229ff/forecast/lang:IT/q/pws:IPUGLIAL7.json");
	 $parsed_json = json_decode($json_string);
	 $temp_c1 = $parsed_json->{'forecast'}->{'txt_forecast'}->{'forecastday'}[0]->{'title'}.", ".$parsed_json->{'forecast'}->{'txt_forecast'}->{'forecastday'}[0]->{'fcttext_metric'};
	 $temp_c2 = "\n".$parsed_json->{'forecast'}->{'txt_forecast'}->{'forecastday'}[1]->{'title'}.", ".$parsed_json->{'forecast'}->{'txt_forecast'}->{'forecastday'}[1]->{'fcttext_metric'};

	break;

	}
	 return $temp_c1.$temp_c2.$temp_c3.$temp_c4.$temp_c5.$temp_c6;

	}

  //scraping dal sito web della PPC Lecce
	public function get_allertameteo($where)
	{

		switch ($where) {

	case "Lecceoggi":

	$html = file_get_contents('http://ppc-lecce.3plab.it/');
	//$html = iconv('ASCII', 'UTF-8//IGNORE', $html);
$html=utf8_decode($html);

  $html = sprintf('<html><head><title></title></head><body>%s</body></html>', $html);
	$html = sprintf('<html><head><title></title></head><body>%s</body></html>', $html);
	$html = sprintf('<html><head><title></title></head><body>%s</body></html>', $html);

	$html =str_replace("Consulta il","<!--",$html);
	$html =str_replace("Commenti disabilitati","-->",$html);
	$html =str_replace("Estratto, per la Zona di Allerta del Comune, del Messaggio di Allerta","",$html);
	$html =str_replace("larea","l&#39;area",$html);
	$html =str_replace("Articoli meno recenti","",$html);
	$html =str_replace("←","",$html);

	$doc = new DOMDocument;
	$doc->loadHTML($html);

	$xpa    = new DOMXPath($doc);


	$divs   = $xpa->query('//div[starts-with(@id, "post")]');
	$allerta="";

	foreach($divs as $div) {
	    $allerta .= "\n".$div->nodeValue;

	}
  //$allerta .=preg_replace('/\s+?(\S+)?$/', '', substr($allerta, 0, 400))."....\n";

	break;

	}
	 return $allerta;

	}


	public function get_sosta($lat,$lon)
	{



  //  $lat=40.3550;
  //  $lon=18.1816;
$url='/usr/www/piersoft/sostalecce/index.php '.$lat.' '.$lon;

//exec ('/usr/bin/php -f /usr/www/piersoft/sostalecce/index.php?lat=40.355&lon=18.1816');

//exec ('/usr/bin/php -f '.$url);
//$url1="http://www.piersoft.it/sostalecce/index.php?lat=".$lat."&lon=".$lon;
//header("location: ".$url1);

echo ($lat." ".$lon."\n");
     $content = '';

    if ($fp = fopen("/usr/www/piersoft/sostalecce/testo.txt", "r")) {
       $content = '';
       // keep reading until there's nothing left
       while ($line = fread($fp, 1024)) {
          $content .= $line;
       }
  //  echo $content;

    } else {
      echo "errore";

    }

    return $content;

    }




public function get_events()
    {

	$eventi="";

	date_default_timezone_set('Europe/Rome');
	date_default_timezone_set("UTC");
	$today=time();
	// un google sheet fa il parsing del dataset presente su dati.comune.lecce.it
	$csv = array_map('str_getcsv', file("https://docs.google.com/spreadsheets/d/14Bvk3Pc37xg-1ijTFvs_3qwLhsrbDVuikEqlXnxlwE8/pub?gid=70729341&single=true&output=csv"));
//	$i=1;

	$count = 0;
	foreach($csv as $data=>$csv1){
	   $count = $count+1;
	}

	for ($i=0;$i<$count-2;$i++){
//echo $csv[$i][7]."/n".$csv[$i][8];
	$html =str_replace("/","-",$csv[$i][7]);
	$html =str_replace(",",".",$csv[$i][6]);
		$html =str_replace("|",".",$csv[$i][6]);

	$from = strtotime($html);
	$html1 =str_replace("/","-",$csv[$i][8]);
	$to = strtotime($html1);


	if ($today >= $from && $today <= $to) {
	$eventi .="\n";
	$eventi .="Titolo: ".$csv[$i][4]."\n";
	$eventi .="Tipologia: ".$csv[$i][5]."\n";
	$eventi .="Organizzatore: ".$csv[$i][3]."\n";
	$eventi .="Email contatto: ".$csv[$i][2]."\n";
	//$eventi .="Dettagli: ".$csv[$i][6]."\n";
	$eventi .="Dettagli: ".preg_replace('/\s+?(\S+)?$/', '', substr($csv[$i][6], 0, 400))."....\n";
	$eventi .="Luogo: ".$csv[$i][10]."\n";
	$eventi .="Pagamento: ".$csv[$i][9]."\n";
	$eventi .="Inizio: ".$csv[$i][7]."\n";
	$eventi .="Fine: ".$csv[$i][8]."\n";
	if ($csv[$i][18] !="") $eventi .="Puoi visualizzarlo su :\nhttp://www.openstreetmap.org/?mlat=".$csv[$i][18]."&mlon=".$csv[$i][19]."#map=19/".$csv[$i][18]."/".$csv[$i][19];
	$eventi .="\n";
	}
	}


/*
$i=3; // test
$eventi .="Titolo: ".$csv[$i][4]."\n";
$eventi .="Tipologia: ".$csv[$i][5]."\n";
$eventi .="Organizzatore: ".$csv[$i][3]."\n";
$eventi .="Email contatto: ".$csv[$i][2]."\n";
$eventi .="Dettagli: ".$csv[$i][6]."\n";
$eventi .="Luogo: ".$csv[$i][10]."\n";
$eventi .="Pagamento: ".$csv[$i][9]."\n";
$eventi .="Inizio: ".$csv[$i][7]."\n";
$eventi .="Fine: ".$csv[$i][8]."\n";
if ($csv[$i][18] !="") $eventi .="Puoi visualizzarlo su :\nhttp://www.openstreetmap.org/?mlat=".$csv[$i][18]."&mlon=".$csv[$i][19]."#map=19/".$csv[$i][18]."/".$csv[$i][19];
$eventi .="\n";

*/
	//	echo $eventi;
	 return $eventi;

	}



	public function get_dae($where)
	{
	$homepage="";


	// un google sheet fa il parsing del dataset presente su dati.comune.lecce.it
	$csv = array_map('str_getcsv', file("https://docs.google.com/spreadsheets/d/1dAPW1JSr3bQMFNBM3TF7kFGa95KZT5oY-72QjKipbeQ/export?format=csv&gid=1704313359&single=true"));
//	$homepage  =$csv[0][0];
//	$homepage .="\n";

	$count = 0;
	foreach($csv as $data=>$csv1){
		 $count = $count+1;
	}
	for ($i=1;$i<$count;$i++){

	$homepage .="\n";
	$homepage .=$csv[$i][3]."\n";
//	$homepage = $csv[$i][4]." ".$csv[$i][5]." ".$csv[$i][6]."\n";
//	$homepage = "Descrizione: ".utf8_encode($csv[$i][5])."\n";
	$homepage .="Puoi visualizzarlo su :\nhttp://www.openstreetmap.org/?mlat=".$csv[$i][1]."&mlon=".$csv[$i][2]."#map=19/".$csv[$i][0]."/".$csv[$i][1];
	$homepage .="\n";
//	$homepage .="Per vedere tutti i luoghi dove è presente un defribillatore clicca qui: http://u.osmfr.org/m/54531/"

	}

	if (empty($csv[1][0])) $homepage="Errore generico, ti preghiamo di selezionare nuovamente qualità aria";


	 echo $homepage;

	 return $homepage;

	}


  public function get_orariscuole($where)
	{
	   $homepage="";
     switch ($where) {

    case "nido":


    // un google sheet fa il parsing del dataset presente su dati.comune.lecce.it
    $csv = array_map('str_getcsv', file("https://spreadsheets.google.com/tq?tqx=out:csv&tq=SELECT%20A%2CB%2CC%2CD%2CE%2CF%2CG%2CH%2CI%2CJ%2CK%2CL%2CM%2CN%2CO%2CP%2CQ%2CR%2CS%20WHERE%20C%20LIKE%20%27%25NIDO%25%27&key=1EJb0cq6a5C5NzgBZiP-KBAs4C_TIgi_b9vmffROp0QU"));
  //	$homepage  =$csv[0][0];
  //	$homepage .="\n";

    $count = 0;
    foreach($csv as $data=>$csv1){
       $count = $count+1;
    }
    for ($i=1;$i<$count;$i++){

      $homepage .="\n";
      $homepage .=$csv[$i][1]."\n";
      $homepage .="Tipol.: ".$csv[$i][2]."\n";
      //      $homepage .="Categoria: ".$csv[$i][3]."\n";
      $homepage .="Indir.: ".$csv[$i][4]."\n";
          $homepage .="Lun. ".$csv[$i][5]."/".$csv[$i][6]."\n";
          $homepage .="Mar. ".$csv[$i][7]."/".$csv[$i][8]."\n";
          $homepage .="Merc.".$csv[$i][9]."/".$csv[$i][10]."\n";
          $homepage .="Giov.".$csv[$i][11]."/".$csv[$i][12]."\n";
          $homepage .="Ven. ".$csv[$i][13]."/".$csv[$i][14]."\n";
          $homepage .="Sab. ".$csv[$i][15]."/".$csv[$i][16]."\n";

      //	$homepage .="Puoi visualizzarlo su :\nhttp://www.openstreetmap.org/?mlat=".$csv[$i][17]."&mlon=".$csv[$i][18]."#map=19/".$csv[$i][17]."/".$csv[$i][18];
    $homepage .="\n";
  //	$homepage .="Per vedere tutti i luoghi dove è presente un defribillatore clicca qui: http://u.osmfr.org/m/54531/"

    }

    if (empty($csv[1][0])) $homepage="Errore generico, ti preghiamo di selezionare nuovamente gli orari";


  //   echo $homepage;
     break;



  case "infanziastatale":

	// un google sheet fa il parsing del dataset presente su dati.comune.lecce.it
	$csv = array_map('str_getcsv', file("https://spreadsheets.google.com/tq?tqx=out:csv&tq=SELECT%20A%2CB%2CC%2CD%2CE%2CF%2CG%2CH%2CI%2CJ%2CK%2CL%2CM%2CN%2CO%2CP%2CQ%2CR%2CS%20WHERE%20D%20LIKE%20%27%25STATALE%25%27%20AND%20C%20LIKE%20%27%25INFANZIA%25%27&key=1EJb0cq6a5C5NzgBZiP-KBAs4C_TIgi_b9vmffROp0QU"));
//	$homepage  =$csv[0][0];
//	$homepage .="\n";

	$count = 0;
	foreach($csv as $data=>$csv1){
		 $count = $count+1;
	}
  echo $count;
	for ($i=1;$i<$count;$i++){

    $homepage .="\n";
    $homepage .=$csv[$i][1]."\n";
    $homepage .="Tipol.: ".$csv[$i][2]."\n";
    //      $homepage .="Categoria: ".$csv[$i][3]."\n";
    $homepage .="Indir.: ".$csv[$i][4]."\n";
        $homepage .="Lun. ".$csv[$i][5]."/".$csv[$i][6]."\n";
        $homepage .="Mar. ".$csv[$i][7]."/".$csv[$i][8]."\n";
        $homepage .="Merc.".$csv[$i][9]."/".$csv[$i][10]."\n";
        $homepage .="Giov.".$csv[$i][11]."/".$csv[$i][12]."\n";
        $homepage .="Ven. ".$csv[$i][13]."/".$csv[$i][14]."\n";
        $homepage .="Sab. ".$csv[$i][15]."/".$csv[$i][16]."\n";

//	$homepage .="Puoi visualizzarlo su :\nhttp://www.openstreetmap.org/?mlat=".$csv[$i][17]."&mlon=".$csv[$i][18]."#map=19/".$csv[$i][17]."/".$csv[$i][18];
	$homepage .="\n";
//	$homepage .="Per vedere tutti i luoghi dove è presente un defribillatore clicca qui: http://u.osmfr.org/m/54531/"

	}

	if (empty($csv[1][0])) $homepage="Errore generico, ti preghiamo di selezionare nuovamente gli orari";


	// echo $homepage;
   break;

   case "infanziacomunale":

 	// un google sheet fa il parsing del dataset presente su dati.comune.lecce.it
 	$csv = array_map('str_getcsv', file("https://spreadsheets.google.com/tq?tqx=out:csv&tq=SELECT%20A%2CB%2CC%2CD%2CE%2CF%2CG%2CH%2CI%2CJ%2CK%2CL%2CM%2CN%2CO%2CP%2CQ%2CR%2CS%20WHERE%20D%20LIKE%20%27%25COMUNALE%25%27%20AND%20C%20LIKE%20%27%25INFANZIA%25%27&key=1EJb0cq6a5C5NzgBZiP-KBAs4C_TIgi_b9vmffROp0QU"));
 //	$homepage  =$csv[0][0];
 //	$homepage .="\n";

 	$count = 0;
 	foreach($csv as $data=>$csv1){
 		 $count = $count+1;
 	}
   echo $count;
 	for ($i=1;$i<$count;$i++){

     $homepage .="\n";
     $homepage .=$csv[$i][1]."\n";
     $homepage .="Tipol.: ".$csv[$i][2]."\n";
     $homepage .="Categoria: ".$csv[$i][3]."\n";
     $homepage .="Indir.: ".$csv[$i][4]."\n";
         $homepage .="Lun. ".$csv[$i][5]."/".$csv[$i][6]."\n";
         $homepage .="Mar. ".$csv[$i][7]."/".$csv[$i][8]."\n";
         $homepage .="Merc.".$csv[$i][9]."/".$csv[$i][10]."\n";
         $homepage .="Giov.".$csv[$i][11]."/".$csv[$i][12]."\n";
         $homepage .="Ven. ".$csv[$i][13]."/".$csv[$i][14]."\n";
         $homepage .="Sab. ".$csv[$i][15]."/".$csv[$i][16]."\n";

 //	$homepage .="Puoi visualizzarlo su :\nhttp://www.openstreetmap.org/?mlat=".$csv[$i][17]."&mlon=".$csv[$i][18]."#map=19/".$csv[$i][17]."/".$csv[$i][18];
 	$homepage .="\n";
 //	$homepage .="Per vedere tutti i luoghi dove è presente un defribillatore clicca qui: http://u.osmfr.org/m/54531/"

 	}

 	if (empty($csv[1][0])) $homepage="Errore generico, ti preghiamo di selezionare nuovamente gli orari";


 	// echo $homepage;
    break;
   case "infanziaparitaria":


   // un google sheet fa il parsing del dataset presente su dati.comune.lecce.it
   $csv = array_map('str_getcsv', file("https://spreadsheets.google.com/tq?tqx=out:csv&tq=SELECT%20A%2CB%2CC%2CD%2CE%2CF%2CG%2CH%2CI%2CJ%2CK%2CL%2CM%2CN%2CO%2CP%2CQ%2CR%2CS%20WHERE%20D%20LIKE%20%27%25PARITARIA%25%27%20AND%20C%20LIKE%20%27%25INFANZIA%25%27&key=1EJb0cq6a5C5NzgBZiP-KBAs4C_TIgi_b9vmffROp0QU"));
   //	$homepage  =$csv[0][0];
   //	$homepage .="\n";

   $count = 0;
   foreach($csv as $data=>$csv1){
      $count = $count+1;
   }
   echo $count;
   for ($i=1;$i<$count;$i++){

     $homepage .="\n";
     $homepage .=$csv[$i][1]."\n";
     $homepage .="Tipol.: ".$csv[$i][2]."\n";
     $homepage .="Categoria: ".$csv[$i][3]."\n";
     $homepage .="Indir.: ".$csv[$i][4]."\n";
         $homepage .="Lun. ".$csv[$i][5]."/".$csv[$i][6]."\n";
         $homepage .="Mar. ".$csv[$i][7]."/".$csv[$i][8]."\n";
         $homepage .="Merc.".$csv[$i][9]."/".$csv[$i][10]."\n";
         $homepage .="Giov.".$csv[$i][11]."/".$csv[$i][12]."\n";
         $homepage .="Ven. ".$csv[$i][13]."/".$csv[$i][14]."\n";
         $homepage .="Sab. ".$csv[$i][15]."/".$csv[$i][16]."\n";

   //	$homepage .="Puoi visualizzarlo su :\nhttp://www.openstreetmap.org/?mlat=".$csv[$i][17]."&mlon=".$csv[$i][18]."#map=19/".$csv[$i][17]."/".$csv[$i][18];
   $homepage .="\n";
   //	$homepage .="Per vedere tutti i luoghi dove è presente un defribillatore clicca qui: http://u.osmfr.org/m/54531/"

   }

   if (empty($csv[1][0])) $homepage="Errore generico, ti preghiamo di selezionare nuovamente gli orari";


   // echo $homepage;
    break;


   case "primariaparitaria":


  // un google sheet fa il parsing del dataset presente su dati.comune.lecce.it
  $csv = array_map('str_getcsv', file("https://spreadsheets.google.com/tq?tqx=out:csv&tq=SELECT%20A%2CB%2CC%2CD%2CE%2CF%2CG%2CH%2CI%2CJ%2CK%2CL%2CM%2CN%2CO%2CP%2CQ%2CR%2CS%20WHERE%20D%20LIKE%20%27%25PARITARIA%25%27%20AND%20C%20LIKE%20%27%25PRIMARIA%25%27&key=1EJb0cq6a5C5NzgBZiP-KBAs4C_TIgi_b9vmffROp0QU"));
 //	$homepage  =$csv[0][0];
 //	$homepage .="\n";

  $count = 0;
  foreach($csv as $data=>$csv1){
     $count = $count+1;
  }

  echo $count;
  for ($i=1;$i<$count;$i++){
//    for ($i=1;$i<18;$i++){


$homepage .="\n";
$homepage .=$csv[$i][1]."\n";
$homepage .="Tipol.: ".$csv[$i][2]."\n";
$homepage .="Categoria: ".$csv[$i][3]."\n";
$homepage .="Indir.: ".$csv[$i][4]."\n";
    $homepage .="Lun. ".$csv[$i][5]."/".$csv[$i][6]."\n";
    $homepage .="Mar. ".$csv[$i][7]."/".$csv[$i][8]."\n";
    $homepage .="Merc.".$csv[$i][9]."/".$csv[$i][10]."\n";
    $homepage .="Giov.".$csv[$i][11]."/".$csv[$i][12]."\n";
    $homepage .="Ven. ".$csv[$i][13]."/".$csv[$i][14]."\n";
    $homepage .="Sab. ".$csv[$i][15]."/".$csv[$i][16]."\n";


    //	$homepage .="Puoi visualizzarlo su :\nhttp://www.openstreetmap.org/?mlat=".$csv[$i][17]."&mlon=".$csv[$i][18]."#map=19/".$csv[$i][17]."/".$csv[$i][18];
  $homepage .="\n";
 //	$homepage .="Per vedere tutti i luoghi dove è presente un defribillatore clicca qui: http://u.osmfr.org/m/54531/"

  }

  if (empty($csv[1][0])) $homepage="Errore generico, ti preghiamo di selezionare nuovamente gli orari";


   //echo $homepage;
    break;
    case "primaria":


    // un google sheet fa il parsing del dataset presente su dati.comune.lecce.it
    $csv = array_map('str_getcsv', file("https://spreadsheets.google.com/tq?tqx=out:csv&tq=SELECT%20A%2CB%2CC%2CD%2CE%2CF%2CG%2CH%2CI%2CJ%2CK%2CL%2CM%2CN%2CO%2CP%2CQ%2CR%2CS%20WHERE%20D%20LIKE%20%27%25STATALE%25%27%20AND%20C%20LIKE%20%27%25PRIMARIA%25%27&key=1EJb0cq6a5C5NzgBZiP-KBAs4C_TIgi_b9vmffROp0QU"));
    //	$homepage  =$csv[0][0];
    //	$homepage .="\n";

    $count = 0;
    foreach($csv as $data=>$csv1){
      $count = $count+1;
    }

    echo $count;
    for ($i=1;$i<$count;$i++){
    //    for ($i=1;$i<18;$i++){


    $homepage .="\n";
    $homepage .=$csv[$i][1]."\n";
    $homepage .="Tipol.: ".$csv[$i][2]."\n";
    $homepage .="Categoria: ".$csv[$i][3]."\n";
    $homepage .="Indir.: ".$csv[$i][4]."\n";
     $homepage .="Lun. ".$csv[$i][5]."/".$csv[$i][6]."\n";
     $homepage .="Mar. ".$csv[$i][7]."/".$csv[$i][8]."\n";
     $homepage .="Merc.".$csv[$i][9]."/".$csv[$i][10]."\n";
     $homepage .="Giov.".$csv[$i][11]."/".$csv[$i][12]."\n";
     $homepage .="Ven. ".$csv[$i][13]."/".$csv[$i][14]."\n";
     $homepage .="Sab. ".$csv[$i][15]."/".$csv[$i][16]."\n";


     //	$homepage .="Puoi visualizzarlo su :\nhttp://www.openstreetmap.org/?mlat=".$csv[$i][17]."&mlon=".$csv[$i][18]."#map=19/".$csv[$i][17]."/".$csv[$i][18];
    $homepage .="\n";
    //	$homepage .="Per vedere tutti i luoghi dove è presente un defribillatore clicca qui: http://u.osmfr.org/m/54531/"

    }

    if (empty($csv[1][0])) $homepage="Errore generico, ti preghiamo di selezionare nuovamente gli orari";


    //echo $homepage;
     break;


    case "secondaria_primogrado":


    // un google sheet fa il parsing del dataset presente su dati.comune.lecce.it
    $csv = array_map('str_getcsv', file("https://spreadsheets.google.com/tq?tqx=out:csv&tq=SELECT%20A%2CB%2CC%2CD%2CE%2CF%2CG%2CH%2CI%2CJ%2CK%2CL%2CM%2CN%2CO%2CP%2CQ%2CR%2CS%20WHERE%20C%20LIKE%20%27%25SECONDARIA%25%27&key=1EJb0cq6a5C5NzgBZiP-KBAs4C_TIgi_b9vmffROp0QU"));
  //	$homepage  =$csv[0][0];
  //	$homepage .="\n";

    $count = 0;
    foreach($csv as $data=>$csv1){
       $count = $count+1;
    }
    for ($i=1;$i<$count;$i++){

      $homepage .="\n";
      $homepage .=$csv[$i][1]."\n";
      $homepage .="Tipol.: ".$csv[$i][2]."\n";
      $homepage .="Categoria: ".$csv[$i][3]."\n";
      $homepage .="Indir.: ".$csv[$i][4]."\n";
          $homepage .="Lun. ".$csv[$i][5]."/".$csv[$i][6]."\n";
          $homepage .="Mar. ".$csv[$i][7]."/".$csv[$i][8]."\n";
          $homepage .="Merc.".$csv[$i][9]."/".$csv[$i][10]."\n";
          $homepage .="Giov.".$csv[$i][11]."/".$csv[$i][12]."\n";
          $homepage .="Ven. ".$csv[$i][13]."/".$csv[$i][14]."\n";
          $homepage .="Sab. ".$csv[$i][15]."/".$csv[$i][16]."\n";

      //	$homepage .="Puoi visualizzarlo su :\nhttp://www.openstreetmap.org/?mlat=".$csv[$i][17]."&mlon=".$csv[$i][18]."#map=19/".$csv[$i][17]."/".$csv[$i][18];
    $homepage .="\n";
  //	$homepage .="Per vedere tutti i luoghi dove è presente un defribillatore clicca qui: http://u.osmfr.org/m/54531/"

    }

    if (empty($csv[1][0])) $homepage="Errore generico, ti preghiamo di selezionare nuovamente gli orari";


  //   echo $homepage;
     break;

	}
	  return $homepage;

	}

	public function get_aria($where)
	{
	$homepage="";

		switch ($where) {

	case "lecce":
	// un google sheet fa il parsing del dataset presente su dati.comune.lecce.it
	$csv = array_map('str_getcsv', file("https://docs.google.com/spreadsheets/d/1It2A_VDqWFP01Z7UguDDPDrKGY6xD94AdCl7dWgt5YA/export?format=csv&gid=1088545279&single=true"));
	$homepage  =$csv[0][0];
	$homepage .="\n";

	$count = 0;
	foreach($csv as $data=>$csv1){
		 $count = $count+1;
	}
	for ($i=2;$i<$count;$i++){

	$homepage .="\n";
	$homepage .="Nome Centralina: ".$csv[$i][0]."\n";
	$homepage .="Valore_Pm10: ".$csv[$i][1]." µg/m³\n";
	$homepage .="Valore_Benzene: ".$csv[$i][2]." µg/m³\n";
	$homepage .="Valore_CO: ".$csv[$i][3]." mg/m³\n";
	$homepage .="Valore_SO2: ".$csv[$i][4]." µg/m³\n";
	$homepage .="Valore_PM_2.5: ".$csv[$i][5]." µg/m³\n";
	$homepage .="Valore_O3: ".$csv[$i][6]." µg/m³\n";
	$homepage .="Valore_NO2: ".$csv[$i][7]." µg/m³\n";
	$homepage .="Superati: ".$csv[$i][8]."\n";


	}

 if (empty($csv[2][0])) $homepage="Errore generico, ti preghiamo di selezionare nuovamente qualità aria";


	break;

		}
	// echo $homepage;

	 return $homepage;

	}

	public function get_traffico($where)
	{
	$homepage="";

		switch ($where) {

	case "lecce":
	// un google sheet fa il parsing del dataset presente su dati.comune.lecce.it
	// servizio sperimentale e Demo.
	$csv = array_map('str_getcsv', file("https://docs.google.com/spreadsheets/d/1IfmPLAFr7Ce0Iyd0fj_LQu1EPR0-vJMY5kaWS7IuRAA/pub?output=csv"));
	//$homepage  =$csv[0][0];
	$homepage .="\n";
	$count = 0;
	foreach($csv as $data=>$csv1){
	   $count = $count+1;
	}
	for ($i=1;$i<$count;$i++){

	$homepage .="\n";
	$homepage .="Tipologia: ".$csv[$i][0]."\n";
	$homepage .="Descrizione: ".$csv[$i][1]."\n";
	$homepage .="Data: ".$csv[$i][2]."\n";
	$homepage .="Luogo: ".$csv[$i][3]."\n";
	$homepage .="Puoi visualizzarlo su :\nhttp://www.openstreetmap.org/?mlat=".$csv[$i][4]."&mlon=".$csv[$i][5]."#map=19/".$csv[$i][4]."/".$csv[$i][5];

	//$homepage .="Mappa: http://www.openstreetmap.org/#map=19/".$csv[$i][4]."/".$csv[$i][5];
	$homepage .="\n";


	}

	break;

		}

	 return $homepage;

	}


//monitoraggio temperatura
public function get_temperature($where)
{
	switch ($where) {

		 //Lecce centro
		 case "Lecce centro":
		 $json_string = file_get_contents("http://api.wunderground.com/api/b3f95b06a21229ff/conditions/q/pws:IPUGLIAL7.json");
		 $parsed_json = json_decode($json_string);
		 $location = $parsed_json->{'location'}->{'city'};
		 $temp_c = $parsed_json->{'current_observation'}->{'temp_c'};
		 break;

		 //Lequile
		 case "Lequile":
		 $json_string = file_get_contents("http://api.wunderground.com/api/b3f95b06a21229ff/conditions/q/pws:IPUGLIAL3.json");
		 $parsed_json = json_decode($json_string);
		 $location = $parsed_json->{'location'}->{'city'};
		 $temp_c = $parsed_json->{'current_observation'}->{'temp_c'};
		 break;

		 //Galatina
		 case "Galatina":
		 $json_string = file_get_contents("http://api.wunderground.com/api/b3f95b06a21229ff/conditions/q/pws:IPUGLIAG14.json");
		 $parsed_json = json_decode($json_string);
		 $location = $parsed_json->{'location'}->{'city'};
		 $temp_c = $parsed_json->{'current_observation'}->{'temp_c'};
		 break;

		 //Nardò
		 case "Nardò":
		 $json_string = file_get_contents("http://api.wunderground.com/api/b3f95b06a21229ff/conditions/q/pws:IPUGLIAN2.json");
		 $parsed_json = json_decode($json_string);
		 $location = $parsed_json->{'location'}->{'city'};
		 $temp_c = $parsed_json->{'current_observation'}->{'temp_c'};
		 break;

	}
	return $temp_c;
}

//definisci il path dell'immagine
public function get_image_path($image)
{
	return "data/". $image. ".jpg";
}

//preleva ultima allerta del feed protezione civile di Prato o in locale o in remoto e ritorna titolo e data.
public function load_prot($islocal)
{
	date_default_timezone_set('UTC');

	$logfile=(dirname(__FILE__).'/logs/storedata.log');

	if($islocal)
	{
		//carico dati salvati in locale per confrontarli con quelli remoti
		$prot_civ=dirname(__FILE__)."/data/prot.xml";
		echo "carico dati in locale";
		print_r($prot_civ);
	}
	else
	{
		//carico dati salvati in remoto
		$prot_civ=PROT_CIV;
		echo "carico dati da remoto";
		print_r($prot_civ);

	}

	$xml_file=simplexml_load_file($prot_civ);

	if ($xml_file==false)
		{
			print("Errore nella ricerca del file relativo alla protezione civile");
		}

		//ritorna il primo elemento del feed rss
		$data[0]=$xml_file->channel->item->title;
		//print_r($data[0]);
		$data[1]=$xml_file->channel->item->pubDate;
		//print_r($data[1]);
		return $data;
}

public function update_prot($data)
{
	$prot_civ=dirname(__FILE__)."/data/prot.xml";

	// load the document
	$info = simplexml_load_file($prot_civ);

	// update
	$info->channel->item->title = $data[0];
	$info->channel->item->pubDate = $data[1];

	// save the updated document
	$info->asXML($prot_civ);

}


}
//Fonti
//http://www.lamma.rete.toscana.it/…/comuni_web/dati/prato.xml
//http://data.biometeo.it/BIOMETEO.xml
//http://data.biometeo.it/PRATO/PRATO_ITA.xml
//http://www.sir.toscana.it/supports/xml/risks_395/".$today.".xml"
//http://www.wunderground.com/weather/api/
//https://github.com/alfcrisci/WU_weather_list/blob/master/WU_stations.csv
 ?>
