<? 
/*
    Copyright (C) 2013-2014 xtr4nge [_AT_] gmail.com

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/ 
?>
<!DOCTYPE html>
<link href="style.css" rel="stylesheet" type="text/css">
<? include "menu.php"; ?>
<m-eta name="viewport" content="initial-scale=1.0, width=device-width" />

<script src="js/jquery.js"></script>
<script src="js/jquery-ui.js"></script>

<? 
include "login_check.php";
include "config/config.php";
?>
<?
include "functions.php";

//$bin_danger = "/usr/share/fruitywifi/bin/danger"; //DEPRECATED

// Checking POST & GET variables...
if ($regex == 1) {
    regex_standard($_POST["filename"], "msg.php", $regex_extra);
    regex_standard($_POST["newdata"], "msg.php", $regex_extra);
    regex_standard($_POST["iface"], "msg.php", $regex_extra);
    regex_standard($_POST["io_out_iface"], "msg.php", $regex_extra);
    regex_standard($_POST["io_in_iface"], "msg.php", $regex_extra);
    regex_standard($_POST["iface_supplicant"], "msg.php", $regex_extra);
    regex_standard($_POST["newSSID"], "msg.php", $regex_extra);
    regex_standard($_POST["hostapd_secure"], "msg.php", $regex_extra);
    regex_standard($_POST["hostapd_wpa_passphrase"], "msg.php", $regex_extra);
    regex_standard($_POST["supplicant_ssid"], "msg.php", $regex_extra);
    regex_standard($_POST["supplicant_psk"], "msg.php", $regex_extra);
    regex_standard($_POST["pass_old"], "msg.php", $regex_extra);
    regex_standard($_POST["pass_new"], "msg.php", $regex_extra);
    regex_standard($_POST["pass_new_repeat"], "msg.php", $regex_extra);
    regex_standard($_GET["service"], "msg.php", $regex_extra);
    regex_standard($_GET["action"], "msg.php", $regex_extra);
    //regex_standard($_POST["in_out_mode"], "msg.php", $regex_extra);
}
?>
<?
$filename = $_POST['filename'];
$newdata = $_POST['newdata'];
/*
if ($newdata != "") { $newdata = ereg_replace(13,  "", $newdata);
    $fw = fopen($filename, 'w') or die('Could not open file.');
    $fb = fwrite($fw,stripslashes($newdata)) or die('Could not write to file');
    fclose($fw);
    $fileMessage = $strings["config-updated"]." " . $filename . "<br /><br />";
} 
*/
?>


<?

// -------------- IN | OUT ------------------
if ($_GET["service"] == "io_in") {
    if ($_GET["action"] == "start") {
        // START IFACE (io_in)
        start_iface($io_in_iface, $io_in_ip, $io_in_gw);
    } else {
        // STOP IFACE (io_in)
        stop_iface($io_in_iface, $io_in_ip, $io_in_gw);
    }
}

if ($_GET["service"] == "io_out") {
    if ($_GET["action"] == "start") {
        // START IFACE (io_in)
        start_iface($io_out_iface, $io_out_ip, $io_out_gw);
    } else {
        // STOP IFACE (io_in)
        stop_iface($io_out_iface, $io_out_ip, $io_out_gw);
    }
}

// -------------- INTERFACES ------------------
if(isset($_POST["iface"]) and $_POST["iface"] == "internet"){
    echo "internet:" . $_POST["io_out_iface"];
}

if(isset($_POST["iface"]) and $_POST["iface"] == "wifi"){
    echo "wifi:" . $_POST["io_in_iface"];
}

if(isset($_POST["iface"]) and $_POST["iface"] == "wifi_extra"){
    echo "wifi extra:" . $_POST["io_in_iface_extra"];
}

if(isset($_POST["iface"]) and $_POST["iface"] == "wifi_supplicant"){
    echo "wifi supplicant:" . $_POST["iface_supplicant"];
}

if ($_GET["service"] == "wlan0mon") {
    if ($_GET["action"] == "start") {
        // START MONITOR MODE (wlan0mon)
        start_monitor_mode($io_in_iface_extra);
    } else {
        // STOP MONITOR MODE (wlan0mon)
        stop_monitor_mode($io_in_iface_extra);
    }
}

// -------------- WIRELESS ------------------

if(isset($_POST[newSSID])){
    
    $hostapd_ssid=$_POST[newSSID];
    
    $exec = "sed -i 's/hostapd_ssid=.*/hostapd_ssid=\\\"".$_POST[newSSID]."\\\";/g' ./config/config.php";
    //exec("$bin_danger \"" . $exec . "\"" ); //DEPRECATED
    exec_fruitywifi($exec);

    $exec = "/usr/sbin/karma-hostapd_cli -p /var/run/hostapd-phy0 karma_change_ssid $_POST[newSSID]";
    //exec("$bin_danger \"" . $exec . "\"" ); //DEPRECATED
    exec_fruitywifi($exec);
    
    // replace interface in hostapd.conf and hostapd-secure.conf
    $exec = "/bin/sed -i 's/^ssid=.*/ssid=".$_POST["newSSID"]."/g' /usr/share/fruitywifi/conf/hostapd.conf";
    //exec("$bin_danger \"" . $exec . "\"" ); //DEPRECATED
    exec_fruitywifi($exec);
    $exec = "/bin/sed -i 's/^ssid=.*/ssid=".$_POST["newSSID"]."/g' /usr/share/fruitywifi/conf/hostapd-secure.conf";
    //exec("$bin_danger \"" . $exec . "\"" ); //DEPRECATED
    exec_fruitywifi($exec);
}


if (isset($_POST['hostapd_secure'])) {
    $exec = "sed -i 's/hostapd_secure=.*/hostapd_secure=\\\"".$_POST["hostapd_secure"]."\\\";/g' ./config/config.php";
    //exec("$bin_danger \"" . $exec . "\"" ); //DEPRECATED
    exec_fruitywifi($exec);

    $hostapd_secure = $_POST["hostapd_secure"];
}

if (isset($_POST['hostapd_wpa_passphrase'])) {
    $exec = "sed -i 's/hostapd_wpa_passphrase=.*/hostapd_wpa_passphrase=\\\"".$_POST["hostapd_wpa_passphrase"]."\\\";/g' ./config/config.php";
    //exec("$bin_danger \"" . $exec . "\"" ); //DEPRECATED
    exec_fruitywifi($exec);
    
    $exec = "sed -i 's/wpa_passphrase=.*/wpa_passphrase=".$_POST["hostapd_wpa_passphrase"]."/g' ../conf/hostapd-secure.conf";
    //exec("$bin_danger \"" . $exec . "\"" ); //DEPRECATED
    exec_fruitywifi($exec);
    
    $hostapd_wpa_passphrase = $_POST["hostapd_wpa_passphrase"];
}

// -------------- SUPPLICANT ------------------
if(isset($_POST["supplicant_ssid"]) and isset($_POST["supplicant_psk"])) {
    $exec = "sed -i 's/supplicant_ssid=.*/supplicant_ssid=\\\"".$_POST["supplicant_ssid"]."\\\";/g' ./config/config.php";
    //exec("$bin_danger \"" . $exec . "\"" ); //DEPRECATED
    exec_fruitywifi($exec);
    
    $exec = "sed -i 's/supplicant_psk=.*/supplicant_psk=\\\"".$_POST["supplicant_psk"]."\\\";/g' ./config/config.php";
    //exec("$bin_danger \"" . $exec . "\"" ); //DEPRECATED
    exec_fruitywifi($exec);
    
    $supplicant_ssid = $_POST["supplicant_ssid"];
    $supplicant_psk = $_POST["supplicant_psk"];
}

// -------------- PASSWORD ------------------

if(isset($_POST["pass_old"]) and isset($_POST["pass_new"])) {
    include "users.php";
    if ( ($users["admin"] == md5($_POST["pass_old"])) and ($_POST["pass_new"] == $_POST["pass_new_repeat"])) {
	$exec = "sed -i 's/\\\=\\\"".md5($_POST["pass_old"])."\\\"/\\\=\\\"".md5($_POST["pass_new"])."\\\"/g' ./users.php";
	//echo $exec;
	//exit;
    //exec("$bin_danger \"" . $exec . "\"" ); //DEPRECATED
    exec_fruitywifi($exec);
    
    $pass_msg = 1;
    } else {
	$pass_msg = 2;
    }
}

?>

<?
#echo $io_out_iface;
#echo $io_in_iface;

$ifaces = exec("/sbin/ifconfig -a | cut -c 1-8 | sort | uniq -u |grep -v lo|sed ':a;N;$!ba;s/\\n/|/g'");
$ifaces = str_replace(" ","",$ifaces);
$ifaces = explode("|", $ifaces);
?>


<br>

<!-- SETUP IN|OUT END -->

<div class="rounded-top" align="center"> IN | OUT </div>
<div class="rounded-bottom" style="padding-top: 6px; padding-bottom: 8px;">

<table cellpadding="0" CELLSPACING="0">
    <tr>
	<td>
	    <form action="scripts/config_iface.php" method="post" style="margin:0px">
	    Mode
	    <select class="input" onchange="this.form.submit()" name="io_mode">
		<option value="1" <? if ($io_mode == 1) echo "selected"?> >IN - OUT | [AP]</option>
		<option value="2" <? if ($io_mode == 2) echo "selected"?> >IN - --- | [AP]</option>
		<option value="3" <? if ($io_mode == 3) echo "selected"?> >IN - OUT</option>
		<option value="4" <? if ($io_mode == 4) echo "selected"?> >IN - ---</option>
		<option value="5" <? if ($io_mode == 5) echo "selected"?> >-- - OUT</option>
	    </select>
	    </form>
	    </td>
	    <td>
	    
	    <form action="scripts/config_iface.php" method="post" style="margin:0px">
	    &nbsp;[AP]
	    <select class="input" onchange="this.form.submit()" name="ap_mode">
		<option value="1" <? if ($ap_mode == 1) echo "selected"?> >Hostapd</option>
		<? if (file_exists("/usr/share/FruityWifi/www/modules/mana/includes/hostapd")) { ?>
		<option value="3" <? if ($ap_mode == 3) echo "selected"?> >Hostapd-Mana</option>
		<? } ?>
		<? if (file_exists("/usr/share/FruityWifi/www/modules/karma/includes/hostapd")) { ?>
		<option value="4" <? if ($ap_mode == 4) echo "selected"?> >Hostapd-Karma</option>
		<? } ?>
		<option value="2" <? if ($ap_mode == 2) echo "selected"?> >Airmon-ng</option>
	    </select>
	    </form>
	</td>
    </tr>
</table>
<br>

<table cellpadding="0" CELLSPACING="0">
    <tr>
	<td valign="top">
	    <!-- SUB IN  -->
	    <div id="div_in" name="div_in" <? if($io_mode == 5) echo "style='visibility: hidden;'"?> >
		<table cellpadding="0" CELLSPACING="0">
		    <tr>
			<td style="padding-right:10px" align="right">&nbsp;&nbsp;IN</td>
			<td style="padding-right:10px" nowrap>
			<form action="scripts/config_iface.php" method="post" style="margin:0px">
			    <select class="input" onchange="this.form.submit()" name="io_in_iface">
				<option>-</option>
				<?
				for ($i = 0; $i < count($ifaces); $i++) {
				    if (strpos($ifaces[$i], "mon") === false) {
					if ($io_in_iface == $ifaces[$i]) $flag = "selected" ; else $flag = "";
					echo "<option $flag>$ifaces[$i]</option>";
				    }
				}
				?>
			    </select>
			</form>
			</td>
		    </tr>
		    <tr>
			<td style="padding-right:10px" align="right"></td>
			<td style="padding-right:10px" nowrap>
			<form action="scripts/config_iface.php" method="post" style="margin:0px">
			    <select class="input" onchange="this.form.submit()" name="io_in_set">
				<option value="1" <? if($io_in_set == "1") echo "selected" ?> >[Manual]</option>
				<option value="0" <? if($io_in_set == "0") echo "selected" ?> >[Current]</option>
			    </select>
			</form>
			<?
			    if($io_in_set == "0") {
				$tmp_ip = exec("/sbin/ifconfig $io_in_iface | grep 'inet addr:' | cut -d: -f2 |awk '{print $1}'");
				echo "<input class='input' style='width:120' value='$tmp_ip' disabled>";
			    }
			?>
			</td>
		    </tr>
		    <form action="scripts/config_iface.php" method="post" style="margin:0px">
		    <tr <? if($io_in_set == "0") echo "style='display:none;'"?> >
			<td style="padding-right:10px" align="right">IP</td>
			<td style="padding-right:10px"><input class="input" name="io_in_ip" style="width:120" value="<?=$io_in_ip?>"></td>
		    </tr>
		    <tr <? if($io_in_set == "0") echo "style='display:none;'"?> >
			<td style="padding-right:10px" align="right">MASK</td>
			<td style="padding-right:10px"><input class="input" name="io_in_mask" style="width:120" value="<?=$io_in_mask?>"></td>
		    </tr>
		    <tr <? if($io_in_set == "0") echo "style='display:none;'"?> >
			<td style="padding-right:10px" align="right">GW</td>
			<td style="padding-right:10px"><input class="input" name="io_in_gw" style="width:120" value="<?=$io_in_gw?>"></td>
		    </tr>
		    <tr <? if($io_in_set == "0") echo "style='display:none;'"?> >
			<td style="padding-right:10px" align="right"></td>
			<td style="padding-right:10px">
			    <input class="input" type="submit" value="Save">
			    <?
			    $tmp_ip = exec("/sbin/ifconfig $io_in_iface | grep 'inet addr:' | cut -d: -f2 |awk '{print $1}'");
			    
			    if (trim($tmp_ip) == trim($io_in_ip)) {
				echo "<a href='page_config_adv.php?service=io_in&action=stop'><b>stop</b></a> [<font color='lime'>on</font>]";
			    } else {
				echo "<a href='page_config_adv.php?service=io_in&action=start'><b>start</b></a> [<font color='red'>off</font>]";
			    }
			    
			    ?>
			</td>
		    </tr>
		    </form>
		</table>
	    </div>
	</td>
	
	<td width="40px"></td>
	
	<td valign="top">
	    <!-- SUB OUT -->
	    <div <? if($io_mode == 2 or $io_mode == 4) echo "style='visibility: hidden;'"?> >
		<table cellpadding="0" CELLSPACING="0">
		    <tr>
			<td style="padding-right:10px" align="right">OUT</td>
			<td style="padding-right:10px">
			<form action="scripts/config_iface.php" method="post" style="margin:0px">
				<select class="input" onchange="this.form.submit()" name="io_out_iface">
					<option>-</option>
					<?
					for ($i = 0; $i < count($ifaces); $i++) {
						if (strpos($ifaces[$i], "mon") === false) {
							if ($io_out_iface == $ifaces[$i]) $flag = "selected" ; else $flag = "";
							echo "<option $flag>$ifaces[$i]</option>";
						}
					}
					?>
				</select>
			</form>
			</td>
		    </tr>
		    <tr>
			<td style="padding-right:10px" align="right"></td>
			<td style="padding-right:10px" nowrap>
			<form action="scripts/config_iface.php" method="post" style="margin:0px">
			    <select class="input" onchange="this.form.submit()" name="io_out_set">
				<option value="1" <? if($io_out_set == "1") echo "selected" ?> >[Manual]</option>
				<option value="0" <? if($io_out_set == "0") echo "selected" ?> >[Current]</option>
			    </select>
			</form>
			<?
			    if($io_out_set == "0") {
				$tmp_ip = exec("/sbin/ifconfig $io_out_iface | grep 'inet addr:' | cut -d: -f2 |awk '{print $1}'");
				echo "<input class='input' style='width:120' value='$tmp_ip' disabled>";
			    }
			?>
			</td>
		    </tr>
		    <form action="scripts/config_iface.php" method="post" style="margin:0px">
		    <tr <? if($io_out_set == "0") echo "style='display:none;'"?> >
			<td style="padding-right:10px" align="right">IP</td>
			<td style="padding-right:10px"><input class="input" name="io_out_ip" style="width:120" value="<?=$io_out_ip?>"></td>
		    </tr>
		    <tr <? if($io_out_set == "0") echo "style='display:none;'"?> >
			<td style="padding-right:10px" align="right">MASK</td>
			<td style="padding-right:10px"><input class="input" name="io_out_mask" style="width:120" value="<?=$io_out_mask?>"></td>
		    </tr>
		    <tr <? if($io_out_set == "0") echo "style='display:none;'"?> >
			<td style="padding-right:10px" align="right">GW</td>
			<td style="padding-right:10px"><input class="input" name="io_out_gw" style="width:120" value="<?=$io_out_gw?>"></td>
		    </tr>
		    <tr <? if($io_out_set == "0") echo "style='display:none;'"?> >
			<td style="padding-right:10px" align="right"></td>
			<td style="padding-right:10px">
			    <input class="input" type="submit" value="Save">
			    <?
			    $tmp_ip = exec("/sbin/ifconfig $io_out_iface | grep 'inet addr:' | cut -d: -f2 |awk '{print $1}'");
			    
			    if (trim($tmp_ip) == trim($io_out_ip)) {
			        echo "<a href='page_config_adv.php?service=io_out&action=stop'><b>stop</b></a> [<font color='lime'>on</font>]";
			    } else {
				echo "<a href='page_config_adv.php?service=io_out&action=start'><b>start</b></a> [<font color='red'>off</font>]";
			    }
			    
			    ?>
			</td>
		    </tr>
		    </form>
		</table>
	    </div>
	</td>
    </tr>
</table>

<br>

<form action="scripts/config_iface.php" method="post" style="margin:0px">
    &nbsp;&nbsp;&nbsp;+
    <?
	    //if($ap_mode == "2") $set_action = "disabled" ;
    ?>
    <select class="input" onchange="this.form.submit()" name="io_action" >
	<option value="at0" <? if ($io_action == "at0") echo "selected"?> >at0</option>
	<!-- <option value="wlan0" <? if ($io_action == "wlan0") echo "selected"?> >wlan0</option> -->
	<?
	for ($i = 0; $i < count($ifaces); $i++) {
	    if (strpos($ifaces[$i], "mon") === false) {
		if ($io_action == $ifaces[$i]) $flag = "selected" ; else $flag = "";
		echo "<option value='".$ifaces[$i]."' $flag>$ifaces[$i]</option>";
	    }
	}
	?>
    </select> [sniff|inject]
</form>

</div>

<!-- SETUP IN/OUT END -->


<br>

<!-- Additional INTERFACES -->

<div class="rounded-top" align="center"> Additional Interfaces </div>
<div class="rounded-bottom">

    <form action="scripts/config_iface.php" method="post" style="margin:0px">
    &nbsp;&nbsp;&nbsp;Monitor 
    <? $iface_wlan0mon = exec("/sbin/ifconfig |grep wlan0mon"); ?>
    <select class="input" onchange="this.form.submit()" name="io_in_iface_extra" <? if ($iface_wlan0mon != "") echo "disabled" ?> >
        <option>-</option>
        <?
        for ($i = 0; $i < count($ifaces); $i++) {
        	if (strpos($ifaces[$i], "mon") === false) {
            	if ($io_in_iface_extra == $ifaces[$i]) $flag = "selected" ; else $flag = "";
            	echo "<option $flag>$ifaces[$i]</option>";
            }
        }
        ?>
    </select> 
    <img src="img/help-browser.png" title="Use this interface for extra features like Kismet, MDK3, etc..." width=14>
    <?
        if ($iface_wlan0mon == "") {
            echo "<b><a href='page_config_adv.php?service=wlan0mon&action=start'>start</a></b> [<font color='red'>wlan0mon</font>]";
        } else {
            echo "<b><a href='page_config_adv.php?service=wlan0mon&action=stop'>stop</a></b>&nbsp; [<font color='lime'>wlan0mon</font>]";
        }
        //echo "(kismet, mdk3, etc)";
    ?>
    <input type="hidden" name="iface" value="wifi_extra">
    </form>
    
</div>

<!-- Additional INTERFACES END -->

<br>

<!-- WIRELESS SETUP -->

<div class="rounded-top" align="center"> Wireless Setup </div>
<div class="rounded-bottom">
    <form method="POST" style="margin:1px">
        Open <input type="radio" class="input" name="hostapd_secure" value="0" <? if ($hostapd_secure != 1) echo 'checked'; ?> onchange="this.form.submit()"> 
        Secure <input type="radio" class="input" name="hostapd_secure" value="1" <? if ($hostapd_secure == 1) echo 'checked'; ?> onchange="this.form.submit()">
    </form
    <br>
    <form action="#" method="POST" autocomplete="off" style="margin:1px">
    <input class="input" name="newSSID" value="<?=$hostapd_ssid?>">    
    <input class="input" type="submit" value="change SSID">
    </form>
    <form action="#" method="POST" autocomplete="off" style="margin:1px">
    <input class="input" name="hostapd_wpa_passphrase" type="password" value="<?=$hostapd_wpa_passphrase?>">    
    <input class="input" type="submit" value="passphrase">
    </form>
</div>

<br>

<!-- DOMAIN SETUP -->

<div class="rounded-top" align="center"> Domain Setup </div>
<div class="rounded-bottom">
    <form action="scripts/config_iface.php" method="POST" autocomplete="off" style="margin:1px">
	<input class="input" name="domain" value="<?=$dnsmasq_domain?>">    
	<input class="input" type="submit" value="change Domain">
    </form>
</div>

<br>

<!-- PASSWORD -->

<div class="rounded-top" align="center"> Password </div>
<div class="rounded-bottom">
    <form action="<?=$_SERVER[php_self]?> " method="POST" autocomplete="off">
	Old Pass: <input type="password" class="input" name="pass_old" value=""><br>
	New Pass: <input type="password" class="input" name="pass_new" value=""><br>
	&nbsp;&nbsp;Repeat: <input type="password" class="input" name="pass_new_repeat" value="">
	<input class="input" type="submit" value="Change">
	<?
	    if ($pass_msg != "") {
		echo "<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		if ($pass_msg == 1) echo "<font color='lime'>password changed</font>";
		if ($pass_msg == 2) echo "<font color='red'>password error</font>";
	    }
	?>
    </form>
</div>
