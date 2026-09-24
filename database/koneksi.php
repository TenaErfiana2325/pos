<?php  
date_default_timezone_set('Asia/Jakarta');

$con = mysqli_connect('localhost','root','','pos'); 

if (!$con) 
{
    die('Connect Error: ' . mysqli_connect_errno());
}


function base_url($url = null)

  {
    $base_url = "";
    if ($url != null)
    {
    	return $base_url."/".$url;
    }
    else
    {
    	return $base_url;
    }

  } 

?>