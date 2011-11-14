<html><head>
<title>Keyword Rankings Checker</title>
<script type="text/javascript" src="jquery.js"></script>
<script type="text/javascript" src="searchbox.js"></script>
<style type=text/css>
       *{padding:0;margin:0;}
       body{color:#000;margin:0; padding:0;background:#fff;font-family:arial, helvetica, georgia;}
       #wrapper {width:760px;margin:0 auto;padding:0;}
       #searchrow {margin:0 auto;text-align:center;width:60%;background:#fff;padding:0;}
       #submit {padding:2px;}
       .widelist {background:#fff;font-size:9px;color:#000;padding:4px;text-align:center; }
       .widelist2 {background:#fff;font-size:14px;color:#000;padding:4px;text-align:center;clear:both }
       #popular {padding:4px;}
       li{margin-bottom:2px;text-align:left;list-style-type:none;background:#fff;border-top:1px solid #0099d4;padding:4px}
       li:hover{background:#EFFBFF}
       ul {padding:4px;margin:0 2px 0 2px;width:760px;}
       .padleft{margin-left:1px;}
       /*****
       FONTS
       ************/
       h1, h2 {padding:0;margin:0;font-family: arial, trebuchet, helvetica;color:#000eee}
       h3{font-size:24px;text-align:center;padding:9px;}
       /*****
       Forms
       ************/
        .form input{
     border: 1px solid #d0ccc9;
     background: #fff;
     color: #5f95ef;
     font-size: 11px;
     font-weight: 700;
     padding-bottom: 2px;
 }
 .form input.text{
     font-weight: normal;
     color: #565656;
     border: 1px solid #9c9c9c;
    background: #ddeff6;
     padding: 2px;
     margin-bottom: 5px;
     text-align: left;
 }
 .form input.text.active{
     background: #ddeff6;
     border: 1px solid #0099d4;
 }
</STYLE>
</head>

<body>
      <div style="float:left;height:100%;width:20%;">&nbsp;</div>
      <div id="searchrow" > 
           <img src="smo-analysis.png" alt="keyword lookup" />
           <form method="POST" action="<?php echo $PHP_SELF;?>">
                      <input class='text' value='enter your domain' type='text' size='45' maxlength='75' name='domain' id="search1">
                      <input class='text' value='keyword ' type='text' size='12' maxlength='75' name='keyword' id="search2">
                      <input id='submit' type='submit' value='search' name='submit'>
           </form>

   	  <div class='widelist'>

      <?php
      session_start();
      include "connect.php";
      if (isset($_POST['domain'])) {
                    $domain = $_POST["domain"];
                    $query = $_POST["keyword"];
            function googleResults(
            $query,$page=1,$perpage=50,
            $dc="www.google.com",$filter=true
        ){
            if($page) $page--;
            $url=sprintf("http://%s/ie?q=%s&num=%d&start=%d&hl=en&ie=UTF-8&filter=%d&c2coff=1&safe=off",
            $dc,urlencode($query),$perpage,$page*$perpage,$filter);
            $html = curl_init( $url );
    // Fake out the User Agent
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
            // Start the output buffering
            ob_start();
            // Get the HTML
            curl_exec( $html );
            curl_close( $html );
            // Get the contents of the output buffer
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
    echo "<span class=widelist2>Keyword Rankings for <b>$query</b> on <b>$domain</b></span><br /><br />";
    $resultant = googleResults($query,1,50);
    $resultant2 = $resultant;
          for($i=0;$i<count($resultant2);$i++){
                  if ($domain === $resultant2[$i]['Host']){

                  echo "<span class=widelist>";
                  echo $resultant[$i]['Rank'];
                  echo "</span>";
                  echo "<span class=widelist>";
                  echo $keyword;
                  echo "</span>";
                  echo "<span class=widelist>";
                  echo $resultant[$i]['Host'];
                  echo "</span>";
                  echo "<span class=widelist>";
                  echo $resultant[$i]['Url'];
                  echo "</span><br><br>";
                  $url=$resultant[$i]['Url'];
     $ranking1 = $resultant[$i]['Rank'];
     $ranktrim = ltrim($ranking1, "<nobr>");
     $keyword = $resultant[$i]['query'];
     $domain = $resultant[$i]['Host'];
     $se = "Google";

     $insertintotable="INSERT into rankings (url,ranking,keyword,domain,se) values('$url','$ranktrim','$query','$domain','$se')";
     mysql_query($insertintotable) or die(mysql_error());
     print "<br><br>URL added succesfully to database.<br><br>";
                 } else {

                }
               }
          }




?>   </table>