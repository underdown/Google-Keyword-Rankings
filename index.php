<!doctype=html>
<html>
<head>
	<title>Keyword Rankings Checker</title>
	<script type="text/javascript" src="jquery-1.3.1.min.js"></script>
	<script type="text/javascript" src="jquery.tablesorter.min.js"></script>
	<link rel="stylesheet" href="style.css" type="text/css" ></link>
</head>
<body>
<script type="text/JavaScript">
       $(document).ready(function(){
       $("#myTable").tablesorter();
       $(".stripeMe tr").mouseover(function() {$(this).addClass("over");}).mouseout(function() {$(this).removeClass("over");});
       $(".stripeMe tr:even").addClass("alt");
       $(".update").click(function() {
       var selectR = $(this);
       var urlsearch = $(".urlse a").html();
       var se = $(selectR).find("td.se").html();
       var wt = $(selectR).find("td.wt").html();
       var str = $(selectR).find("td.keywords a").html();
       var ranking = $(selectR).find("td.rankingspan").html();
       var orank = $(selectR).find("td.orank").html();
       var delta = $(selectR).find("td.hclass div").html();
       var keysearch = str.replace('/%20', '+');
       var dataString = 'domain=' + urlsearch + '&keyword=' + keysearch + '&wt=' + wt + '&se=' + se + '&rank=' + ranking + '&orank=' + orank + '&delta=' + delta;
       $(this).html("<td colspan='9' align='center'><img src='loading.gif'></td>");
       $.ajax({
          type: "post",
          url: "postjax2.php",
          data: dataString,
          cache: false,
          success: function(data){
          $(selectR).html(data);
          }
          });
      });
  });
</script>

<?php
include "connect.php";
             if($_GET['submit'])
             {
             $client = $_GET['client'];
             }else{
                    $client = $_GET['client'];
                   }

?>
<div style="margin:0 auto;float:left;">&nbsp;</div>
<div style="margin:0 auto;width:964px;">
     <div style="background:#fafafa;margin:6px 0 2px 0;border:1px solid #BCD4EC;padding:0px 2px 10px 2px;"><h1 style='padding-left:4px'><?php echo $client; ?></h1>
       <table width='960' align='center' class="stripeMe" id="myTable">
       <thead align='left'><tr><th class='u'>domain</th><th class='k'>keyword</th><th class='r'>prev rank</th><th class='r'>curr rank</th><th class='nc'>&#916;</th><th class='w'>search/day</th><th class='clicks'>exp clicks</th><th class='s'>engine</th><th class='date'>date</th></tr></thead>
       <tbody>
       <?php
         $getclientlist="SELECT DISTINCT keyword, client, wt FROM keywords WHERE client LIKE '$client' ORDER BY keyword ASC";
         $getclientlist2=mysql_query($getclientlist) or die("unable to open ze db");
         while($getclientlist3=mysql_fetch_array($getclientlist2)){
                $cclient=strip_tags($getclientlist3[client]);
                $ckeyword=strtolower(strip_tags($getclientlist3[keyword]));
                $wt=strip_tags($getclientlist3[wt]);
 
                 //grab data
                $getclientdata="SELECT timestamp, url, ranking, keyword, domain, se FROM rankings WHERE keyword='$ckeyword' and domain='$cclient' order by timestamp DESC limit 1";
                $getclientdata2=mysql_query($getclientdata) or die("Could not look up client data");
                while($getclientdata3=mysql_fetch_array($getclientdata2))
                         {
                         $url=strip_tags($getclientdata3[url]);
                         $ranking=strip_tags($getclientdata3[ranking]);
                         $getolddata="SELECT timestamp, ranking FROM rankings WHERE keyword='$ckeyword' and domain='$cclient' order by timestamp ASC limit 1";
                         $getolddata2=mysql_query($getolddata) or die("unable to open old db");
                         while( $getolddata3=mysql_fetch_array($getolddata2)) { 	
								$orank=strip_tags($getolddata3[ranking]);
								$otimestamp=strip_tags($getolddata3[timestamp]);
							}
								$se=strip_tags($getclientdata3[se]);
								$keyword=strtolower(strip_tags($getclientdata3[keyword]));
								$queryclean = str_replace(' ','+', $keyword);
								$datevar = $getclientdata3[timestamp];
								$datex = date('m-d-Y',strtotime($datevar));
								$numor = (int)$orank;
								$numcr = (int)$ranking;
								if($numor>$numcr){
									$delta = ($numor-$numcr);
									$hclass= "green";  
								}
								elseif($numor<$numcr) {
									$delta = ($numcr-$numor);
									$hclass= "red"; 
								}
								elseif($numor==$numcr) {
									$delta = "-";
									$hclass= "neutral"; 
								}
								$googlestr = "http://www.google.com/search?q=";
                        
								if($numcr<22){
									$getctr="SELECT ctr FROM ctr WHERE pos='$numcr' limit 1";
									$getctr2=mysql_query($getctr) or die("unable to open old db");
									while($getctr3=mysql_fetch_array($getctr2)) {  
											$nctr=strip_tags($getctr3[ctr]);
											$clicks = round( $wt * $nctr );
									}
								} else {$clicks = "n/a";}
								print "<tr class='update'><td class='urlse'><a title='$url' href=$url>$client</a></td><td class='keywords'><a title='$keyword' target='_blank' href='$googlestr$queryclean'>$keyword</a></td><td class='orank'>$orank</td><td class='rankingspan'>$ranking</td><td class='hclass'><div class='$hclass'>$delta</div></td><td class='wt'>$wt</td><td class='clicks'>$clicks</td><td class='se'>$se</td><td class='date'>$datex</td></tr>";
                         }
            }
?>
        </tbody></table>
        <form style="clear:left;" action="<?php $_SERVER['PHP_SELF']; ?>" method="post">
        <input type="hidden" value="fakevalue">
        <input type=submit value='reorder'>
        </form>
      </div>
  </div>
  <div style="margin:0 auto;float:right;">&nbsp;</div>
  </body>
  </html>

