<?php
/**
 * 
 * this is what you have to change if you want to alter the hosts
 * 
 */
function get_hosts(){
	return array(array("scylla.cs.uoi.gr",22),array("ecourse.uoi.gr",80),array("eudoxus.gr",80),array("classweb.uoi.gr",443));
}

/**
 * @param $ip
 * @param $port
 * @return bool
 * Function to ping each host.
 * It returns a simple boolean value based on whether that host is online
 */
function ping_address($ip,$port="") {

  if(empty($port))
  {
    exec("/bin/ping -c 2 $ip", $outcome, $status);
    
    return 0 == $status;
  }

  $fp = fsockopen("udp://".$ip, $port, $errno, $errstr);
  if($errstr){
      return false;
  }
  if (!$fp) {
    //return false;
    exec("/bin/ping -c 2 $ip", $outcome, $status);
    return 0 == $status;
  }else{
    return true;
  }
}



function get_status($hosts){

  $servers=array();
  $i=0;
  foreach ($hosts as $key=>$server_port) {

    $server=$server_port[0];
    $port=$server_port[1];
    $result=ping_address($server,$port);
    $servers[$server]=$result;
  }
  return $servers;
}



function get_new_values($servers){
    $values=array("time"=>time());

    foreach($servers as $server=>$status)
    {
        if($status)
        {
            $values[$server]="true";
        }else{
            $values[$server]="false";
        }
    }
    return $values;
}


function write_to_file($values,$file_name,$EOL=PHP_EOL,$delimiter=NULL)
{
    if(is_null($delimiter))
    {
        global $file_delimiter;
        $delimiter=$file_delimiter;
    }
    $result="";
    foreach($values as $key=>$value)
    {
        $result=$result.$key.$delimiter.$value.$EOL;
    }
    file_put_contents($file_name, $result);
}

function renew_values($file_name)
{
    $values=get_new_values(get_status(get_hosts()));
    write_to_file($values,$file_name,"\n");
    return $values;
}

/**
 * @param $host
 * @param $hosts
 * @return int|void
 * Function to get wether the host is http/https or something else
 */
function isHTTP($host,$hosts)
{
	foreach($hosts as $temp_host)
	{
		if($host==$temp_host[0])
		{
			if($temp_host[1]==80)
			{
				return 1;
			}
			if($temp_host[1]==443)
			{
				return 0;
			}
			return -1;
		}
		
	}
}

/**
 * @param $file_name
 * @param $file_delimiter
 * @return array
 * Function to read the hosts that are temporarily stored in the file
 */
function read_file($file_name,$file_delimiter)
{
    $fn = file($file_name);
    $data=array();
    foreach ($fn as $value) {
        $temp_data=explode($file_delimiter,$value);
        $data[$temp_data[0]]=$temp_data[1];
    }
    return $data;
}




/**
 * Change these values to change the work progress of the program.
 */

$file_name="meassures.txt";
$file_delimiter=":";
$awaiting_time=5*60;


$data=read_file($file_name,$file_delimiter);
$renew=time()>intval($data["time"])+$awaiting_time;
if($renew)
{
  //file_get_contents('update-cronjob.php');
    $data=renew_values($file_name);
}
$new_data_values=array();
$new_data=array();
foreach ($data as $key => $value) {
  if($key=="time")
  {
      // Adding 3*600 to make it from utc to utc+3
    $new_data["next_update"]=date("D d-m-Y H:i:s",intval($value)+$awaiting_time);
    $new_data["last_update"]=date("D d-m-Y H:i:s",intval($value));
  }else{
    if(trim($value)=="true")
    {
      $new_data[$key]=True;
    }else{
      $new_data[$key]=False;
    }

    $new_data_values[$key]=isHTTP($key,get_hosts());
  }
}
?>

<!doctype html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="content-type" content="text/html;charset=utf-8" />
<title>
  <?php

  $servers = array_slice($new_data, 2, count($new_data)-1, true);

  $are_up=true;
  foreach ($servers as $value) {
    if(!$value)
    {
      $are_up=false;
    }
  }
  if($are_up)
  {
    echo "Server is Online";
  }else
  {
    echo "Server Maintenance";
  }
  ?>
</title>


<meta name="description" content="Check the status of various servers. This page displays the online status of servers, including scylla.cs.uoi.gr, ecourse.uoi.gr, eudoxus.gr, and classweb.uoi.gr.">
  <meta name="keywords" content="server status, online, scylla, ecourse, eudoxus, classweb">
  <meta name="author" content="Παύλος Ορφανίδης">
  <meta name="twitter:card" content="summary">
  <meta name="twitter:site" content="@paul_porfanid">
  
  <meta name="twitter:title" content="Server Status - <?php if($are_up){ echo "Online";}else{echo "Offline";} ?>">
  <meta name="twitter:description" content="Check the status of various servers. This page displays the online status of servers, including scylla.cs.uoi.gr, ecourse.uoi.gr, eudoxus.gr, and classweb.uoi.gr.">
  
  <meta property="og:title" content="Server Status - <?php if($are_up){ echo "Online";}else{echo "Offline";} ?>">
  <meta property="og:description" content="Check the status of various servers. This page displays the online status of servers, including scylla.cs.uoi.gr, ecourse.uoi.gr, eudoxus.gr, and classweb.uoi.gr.">
  <meta property="og:type" content="website">
  <meta property="og:url" content="https://cse.uoi.gr/~cse74134/servers">
  <script src="https://tarptaeya.github.io/repo-card/repo-card.js"></script>
  <!-- Add Twitter image here using: <meta name="twitter:image" content="your-image-url.jpg"> -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">


    <style>
  body { text-align: center; padding: 7%; font: 20px Helvetica, sans-serif; color: #333;}
  .title{ font-size: 30px; }
  article { display: block; text-align: left; width: 650px; margin: 0 auto; }
  a { color: #dc8100; text-decoration: none; }
  a:hover { color: #333; text-decoration: none; }
</style>
</head>
<body>
<article class="container mt-5">
    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th>Server</th>
            <th>Status</th>
        </tr>
        </thead>
        <tbody>
  <?php
    $text="";

    foreach ($servers as $host => $condition) {
      $text=$text."<tr><td>";

      	if($new_data_values[$host]==0)
	{
		$text=$text."<strong><a href='https://$host' target='_blank'>$host</a></strong>";
	}
	else
	{
		if($new_data_values[$host]==1)
		{
			$text=$text."<strong><a href='http://$host' target='_blank'>$host</a></strong>";
		}
		else{
			$text=$text."<strong>$host</strong>";
		}
	}
        $text=$text."</td><td>";

	//://$host' target='_blank'>$host</a> είναι: ";
      if($condition)
      {
        $text=$text."<span class='badge bg-success'>online</span>";
      }else{
        $text=$text."<span class='badge bg-error'>offline</span>";
      }


      $text=$text."</td></tr>\n";
    }
  ?>

  <?php echo $text ?>
        </tbody>
    </table>


    <div>
        <p>Τα παραπάνω στοιχεία προκύπτουν με προσπάθεια σύνδεσης σε συγκεκριμένη θύρα, και αν αυτό αποτύχει, συνεχίζει με εκτέλεση της εντολής ping. Αυτό υποδηλώνει πως ο server απαντάει σε ορισμένα αιτήματα. Υπάρχει όμως η πιθανότητα και πάλι να μην μπορείτε να συνδεθείτε. Σε περίπτωση που συμβεί αυτό, μπορείτε πάντοτε να <a href="https://support.cs.uoi.gr/doku.php?id=cse:%CE%B3%CE%B5%CE%BD%CE%B9%CE%BA%CE%AC:%CE%B5%CF%80%CE%B9%CE%BA%CE%BF%CE%B9%CE%BD%CF%89%CE%BD%CE%AF%CE%B1">επικοινωνήσετε</a> με την ομάδα υποστήριξης.</p>
        
        <p>
            Το παρόν project μπορεί να βρεθεί στο GitHub:
            <div class="repo-card" data-repo="porfanid/Server-Check"></div>
        </p>
        
         <p>&mdash; <a href="https://github.com/porfanid"> Παύλος Ορφανίδης</a></p>
    </div>
</article>
<footer class="container mt-5">
  Τελευταία ενημέρωση στις: <?php echo $new_data["last_update"]; ?><br>
  Επόμενη ενημέρωση στίς: <?php echo $new_data["next_update"]; ?><br>
  Η ενημέρωση γίνεται κάθε <?php $minutes=$awaiting_time/60; echo $minutes; ?> λεπτ<?php
  if($minutes>1)
  {
    echo "ά";
  }else{
    echo "ό";
  }
  ?>.
</footer>

<script src='https://storage.ko-fi.com/cdn/scripts/overlay-widget.js'></script>
<script>
  kofiWidgetOverlay.draw('porfanid', {
    'type': 'floating-chat',
    'floating-chat.donateButton.text': 'Support me',
    'floating-chat.donateButton.background-color': '#00b9fe',
    'floating-chat.donateButton.text-color': '#fff'
  });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
</body>
</html>
