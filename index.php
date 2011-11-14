<html>
<head><title>Keyword Rankings Checker</title>
<script type="text/javascript" src="jquery-1.3.1.min.js"></script>
<script type="text/javascript" src="jquery.tablesorter.min.js"></script>
<link rel="stylesheet" href="style.css" type="text/css" />

<style type=text/css>
*{padding:0;margin:0;}
body{color:#000;margin:0; padding:0;background:#efefef;font-family:arial, helvetica, georgia;}
.shadow {background:#fafafa;cursor:pointer}
#wrapper {width:960px;margin:0 auto;padding:0;}
.date {width:100px}
#popular {padding:4px;}
li{margin-bottom:2px;text-align:left;list-style-type:none;background:#fff;border-top:1px solid #0099d4;padding:4px}
li:hover{background:#EFFBFF}
ul {padding:4px;margin:0 2px 0 2px;width:760px;}
.padleft{margin-left:1px;}
.border {border:4px solid #000;}
.noborder {border:0}
.green{background:#BFFFCE;}
.red{background:#FFBFBF;}
.delta {padding:0 12px 0 12px 0}
.w, .r, .nc, .orank, .wt, .rankingspan, .clicks {text-align:right;padding-right:2px;}
/*****
    FONTS
************/
h1, h2 {padding:0;margin:0;font-family: Calibri, Arial, helvetica;color:#000}
h3{font-size:24px;text-align:center;padding:9px;}
/*****
   Forms
************/
.form input{border: 1px solid #d0ccc9;background: #fff;color: #5f95ef;font-size: 11px;font-weight: 700;padding-bottom: 2px;}
.form input.text{font-weight: normal;color: #565656;border: 1px solid #9c9c9c;background: #ddeff6;padding: 2px;margin-bottom: 5px;text-align: left;}
.form input.text.active{background: #ddeff6;border: 1px solid #0099d4;}
th {padding-left:4px;background: #bcd4ec;border-bottom:2px solid #efefef;border-top:2px solid #efefef;}
tr.update {margin:2px 2px 2px 2px;padding:4px; background:#fafafa; border:1px solid #BCD4EC;}
tr.alt td {background: #ecf6fc;}
tr.over td {background: #bcd4ec;cursor:pointer;}
td {padding:4px;border-bottom:1px solid #BCD4EC;}
.hclass {padding:0 12px 0 12px 0;margin:0; text-align:right;};
.date {width:100px}
</style>
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
               // $cclient=strip_tags($getclientlist3[url]);
                $ckeyword=strtolower(strip_tags($getclientlist3[keyword]));
                $wt=strip_tags($getclientlist3[wt]);
    
                 //grab data
                       $getclientdata="SELECT timestamp, url, ranking, keyword, domain, se FROM rankings WHERE keyword='$ckeyword' and domain='$cclient' order by timestamp DESC limit 1";
                       $getclientdata2=mysql_query($getclientdata) or die("Could not look up client data");
                       while($getclientdata3=mysql_fetch_array($getclientdata2))
                         {
                         //$getclientdata3[url]=strip_tags($getclientdata3[url]);
                         $url=strip_tags($getclientdata3[url]);
                         $ranking=strip_tags($getclientdata3[ranking]);
                         $getolddata="SELECT timestamp, ranking FROM rankings WHERE keyword='$ckeyword' and domain='$cclient' order by timestamp ASC limit 1";
                         $getolddata2=mysql_query($getolddata) or die("unable to open old db");
                         while($getolddata3=mysql_fetch_array($getolddata2))
                         { $orank=strip_tags($getolddata3[ranking]);
                           $otimestamp=strip_tags($getolddata3[timestamp]);}
    
                         $se=strip_tags($getclientdata3[se]);
                         $keyword=strtolower(strip_tags($getclientdata3[keyword]));
                         $queryclean = str_replace(' ','+', $keyword);
                         $datevar = $getclientdata3[timestamp];
                         $datex = date('m-d-Y',strtotime($datevar));
                         $numor = (int)$orank;
                         $numcr = (int)$ranking;
                         if($numor>$numcr){
                         $delta = ($numor-$numcr);
                         $hclass= "green";  }
                         elseif($numor<$numcr) {
                         $delta = ($numcr-$numor);
                         $hclass= "red"; }
                         elseif($numor==$numcr) {
                         $delta = "-";
                         $hclass= "neutral"; }
                         $googlestr = "http://www.google.com/search?q=";
                        // $nctr = "n/a";
                         if($numcr<22){

                           $getctr="SELECT ctr FROM ctr WHERE pos='$numcr' limit 1";
                           $getctr2=mysql_query($getctr) or die("unable to open old db");
                           while($getctr3=mysql_fetch_array($getctr2))
                           {  $nctr=strip_tags($getctr3[ctr]);
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

