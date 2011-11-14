<?php
 include "connect.php";
         if (isset($_POST['domain'])) {
            $domain = $_POST["domain"];
            $query = $_POST["keyword"];
            $queryclean = str_replace(' ','+', $query);
            $wt = $_POST["wt"];
            $se = $_POST["se"];
            $orank = $_POST["orank"];
            $delta = $_POST["delta"];
               function googleResults(
                     $query,$page=1,$perpage=50,
                     $dc="www.google.com",$filter=true
                     ){
                       if($page) $page--;
                       $url=sprintf("http://%s/ie?q=%s&num=%d&start=%d&hl=en&ie=UTF-8&filter=%d&c2coff=1&safe=off",
                       $dc,urlencode($query),$perpage,$page*$perpage,$filter);
                       $html = curl_init( $url );
                       $header = array();
                       $header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,";
                       $header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
                       $header[] =  "Cache-Control: max-age=0";
                       $header[] =  "Connection: keep-alive";
                       $header[] = "Keep-Alive: 300";
                       $header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
                       $header[] = "Accept-Language: en-us,en;q=0.5";
                       $header[] = "Pragma: "; // browsers keep this blank.
                       curl_setopt( $html, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; en)" );
                       curl_setopt( $html, CURLOPT_HTTPHEADER, $header  );
                       ob_start();
                       curl_exec( $html );
                       curl_close( $html );
                       $str = ob_get_contents();
                       ob_end_clean();

                      if(!preg_match_all( "/<nobr>(.+?)<\/nobr>/is", $str, $matches))
                      return false;
                      $matches=$matches[0];
                      $results=array();
                      for($i=0;$i<count($matches);$i++){
                      $match=trim($matches[$i]);
                      if(!preg_match_all( "/(.+?)\.\s<a title=[\"](.+?)[\"] href=(.+?)>(.+?)<\/a>/i",
                      $match, $parts)) continue;
                      $parts[4][0]=strip_tags($parts[4][0]);
                      array_splice($parts,0,1);
                      $LinkTitle    =trim($parts[3][0],"\r\n\t \"");
                      $LinkDesc    =trim($parts[1][0],"\r\n\t \"");
                      $Rank        =trim($parts[0][0]);
                      $LinkUrl    =trim($parts[2][0],"\r\n\t \"");
                      if(!strstr($LinkUrl,"://"))
                          continue;
                      if(!preg_match("/^([^:]+):\/\/([^\/]+)[\/]?(.*)$/",$LinkUrl,$Dom)){
                          continue;
                      }
                      $Http=$Dom[1];
                      $Rel="/".$Dom[3];
                      $Dom=$Dom[2];
                      $serp=array(
                        "Rank"           => $Rank,
                        "Url"            => $LinkUrl,
                        "Title"          => trim(html_entity_decode(strip_tags($LinkTitle))),
                        "Host"           => $Dom,
                        "Protocol"       => $Http,
                        "Path"           => $Rel,
                        "Summary"        => trim(html_entity_decode(strip_tags($LinkDesc))),
                    );
        array_push($results,$serp);
                   }
        return $results;
        }
        // --    old
        function googleLinks(
            $query,$page=1,$perpage=10,
            $dc="www.google.com",$filter=true
            ){
            $res=googleResults($query,$page,$perpage,$dc,$filter);
            $links=array();
            for($i=0;$i<count($res);$i++){
                $link=$res[$i]['Url'];
                array_push($links,$link);
            }
            return $links;
            }
            $resultant = googleResults($query,1,50);
            $resultant2 = $resultant;
            for($i=0;$i<count($resultant2);$i++){
               if ($domain === $resultant2[$i]['Host']){
               $url=$resultant[$i]['Url'];
               $ranking1 = $resultant[$i]['Rank'];
               $ranktrim = ltrim($ranking1, "<nobr>");
               $numor = (int)$orank;
               $numcr = (int)$ranktrim;
               if (is_null($numcr)){
                 $ranktrim = "0"; }
                         if($numor>$numcr){
                         $delta = ($numor-$numcr);
                         $hclass= "green";  }
                         elseif($numor<$numcr) {
                         $delta = ($numcr-$numor);
                         $hclass= "red"; }
                         elseif($numor==$numcr) {
                         $delta = "-";
                         $hclass= "neutral"; }
                           if($numcr<22){

                           $getctr="SELECT ctr FROM ctr WHERE pos='$numcr' limit 1";
                           $getctr2=mysql_query($getctr) or die("unable to open old db");
                           while($getctr3=mysql_fetch_array($getctr2))
                           {  $nctr=strip_tags($getctr3[ctr]);
                              $clicks = round( $wt * $nctr );
                         }
                         } else {$clicks = "n/a";}

               $keyword = $resultant[$i]['Query'];
               $domain = $resultant[$i]['Host'];
               $se = "Google";
               $i = count($resultant2);
               $insertintotable="INSERT into rankings (url,ranking,keyword,domain,se) values('$url','$ranktrim','$query','$domain','$se')";
               mysql_query($insertintotable) or die(mysql_error());
               };
               }
           header('Content-Type: text/html; charset=iso-8859-1');
           $googlestr = "http://www.google.com/search?q=";
           


           echo "<td class='urlse'><a target='_blank' href='$url'>$domain</a></td><td class='keywords'><a target='_blank' href='$googlestr$queryclean'>$query</a></td><td class='orank'>$orank</td><td class='rankingspan'>$ranktrim</td><td class='hclass'><div class='$hclass'>$delta</div></td><td class='wt'>$wt</td><td class='clicks'>$clicks</td><td class='se'>$se</td>";
           echo "<td class=date>";                                                                                                                                                                               
           $today = date("m-d-Y");
           echo $today;
           echo "</td>";


         }
   // return false;
?>
