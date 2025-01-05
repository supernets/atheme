<?php
/**
 * Created by Klaas Tammling. (https://gist.github.com/KlaasT/449b28c63db129a7714d41b8e2074d90)
 * Project: anope2atheme
 * User: ktammling
 * Date: 10.10.2016
 * Time: 15:55
 */

$mu_hold =       	0x00000001;
$mu_neverop =    	0x00000002;
$mu_noop =       	0x00000004;
$mu_waitauth =   	0x00000008;
$mu_hidemail =   	0x00000010;
$mu_oldalias =   	0x00000020;
$mu_nomemo =     	0x00000040;
$mu_emailmemos =	0x00000080;
$mu_cryptpass = 	0x00000100;
$mu_old_sasl =   	0x00000200;
$mu_noburstlogin =  0x00000400;
$mu_enforce =   	0x00000800;
$mu_usepriv =  		0x00001000;
$mu_private =  		0x00002000;
$mu_quietchg =  	0x00004000;

$levels['VOP'] = "+AV";
$levels['HOP'] = "+AHehirtv";
$levels['AOP'] = "+AOehiortv";
$levels['SOP'] = "+AOaefhiorstv";
$levels['QOP'] = "+AFORaefhioqrstv";

$access[1] = "+A";
$access[3] = "+VA";
$access[4] = "+vHiA";
$access[5] = "+vhoOirtA";
$access[10] = "+vhoOairRftA";
$access[15] = "+vhoOairRftA";
$access[20] = "+vhoOairRftA";
$access[999] = "+vhoOairRftA";
$access[9000] = "+vhoOaqsirRftA";
$access[9999] = "+vhoOaqsirRftA";
$access[10000] = "+vhoOaqsirRftA";

$tdata = file_get_contents("anope.db");

$data = explode("\n", $tdata);
unset($tdata);

$userset = array();
#$aliasset = array();
$channelset = array();

$is_mu = false;
$is_na = false;
$is_cu = false;
$is_ca = false;
$mu_account = "";
$mu_pass = "";
$mu_lang = "";
$mu_email = "";
$mu_register = "";
$mu_flags = 0;
$channel_flags = "+";
$mu_vhost_ident = "";
$mu_vhost_host = "";
$mu_vhost_creator = "";
$mu_vhost_time = "";
for($i = 0; $i < count($data); $i++) {

    $line = $data[$i];
    $data2 = explode(" ", $line);

    if($is_mu or ($data2[0] == 'OBJECT' && $data2[1] == "NickCore")) {
        $is_md = false;
        $is_mu = true;
        $is_cu = false;
        $is_na = false;

        if (isset($data2[1])) {
            if ($data2[1] == "pass") {
                $passvars = explode(":", $data2[2]);
                $pass = $passvars[1];
                $mu_pass = '$rawmd5$'.$pass;
            }

            if ($data2[1] == "display") {
                $mu_account = $data2[2];
            }

            if ($data2[1] == "language") {
                $mu_lang = (isset($data2[2])) ? $data2[2] : "default";
            }

            if ($data2[1] == "email") {
                $mu_email = (isset($data2[2])) ? $data2[2] : "admin@st-city.net";
            }

            if ($data2[1] == "time_registered" && isset($data2[2])) {
                $mu_register = $data2[2];
            }

            if($data2[1] == "HIDE_EMAIL")
                $mu_flags |= $mu_hidemail;

            if($data2[1] == "NS_PRIVATE")
                $mu_flags |= $mu_private;

            if($data2[1] == "NS_SECURE")
                $mu_flags |= $mu_enforce;

            $mu_flags = 272;
        }

        if (!empty($mu_account)) {
            $userset[$mu_account] = array(
                "mu_pass" => $mu_pass,
                "mu_account" => $mu_account,
                "mu_lang" => $mu_lang,
                "mu_email" => $mu_email,
                "mu_register" => $mu_register,
                "mu_flags" => $mu_flags,
            );
        }

        if ($data2[0] == "END") {
            $mu_account = "";
            $mu_pass = "";
            $mu_lang = "";
            $mu_email = "";
            $mu_register = "";
            $is_mu = false;
        }
    }

    if($is_na or ($data2[0] == 'OBJECT' && $data2[1] == "NickAlias")) {
        $is_md = false;
        $is_mu = false;
        $is_cu = false;
        $is_na = true;

        if (isset($data2[1]) && $data2[1]=="nick") {
            $tmplined = 'MN ' . $data2[2];
        }

        if (isset($data2[1]) && $data2[1] == "time_registered") {
            $mu_register = $data2[2];
        }

        if (isset($data2[1]) && $data2[1]=="nc") {
            $mu_account = $data2[2];
        }

        if (isset($data2[1]) && $data2[1] == "vhost_ident") {
            $mu_vhost_ident = $data2[2];
        }

        if (isset($data2[1]) && $data2[1] == "vhost_host") {
            $mu_vhost_host = $data2[2];
        }

        if (isset($data2[1]) && $data2[1] == "vhost_creator") {
            $mu_vhost_creator = $data2[2];
        }

        if (isset($data2[1]) && $data2[1] == "vhost_time") {
            $mu_vhost_time = $data2[2];
        }

        if ($data2[0] == "END") {

            $tmplined .= " ".$mu_account." ".$mu_register." ".time();
            $lastnick = $mu_account;
            $lastid = 'NA';

            $dataNA = explode(" ", $tmplined);
            if (!empty($userset[$dataNA[1]])) {
                $userset[$dataNA[2]]['mu_register'] = $mu_register;
            }
            else {
                $userset[$dataNA[2]]['aliases'][$dataNA[1]] = array(
                    "mu_register"   => $mu_register,
                    "last_seen" =>  time(),
                );

                $userset[$dataNA[2]]['mu_register'] = $mu_register;
            }

            if (!empty($mu_vhost_host)) {

                $userset[$mu_account]['vhost_ident'] = $mu_vhost_ident;
                $userset[$mu_account]['vhost_host'] = $mu_vhost_host;
                $userset[$mu_account]['vhost_creator'] = $mu_vhost_creator;
                $userset[$mu_account]['vhost_time'] = $mu_vhost_time;
            }

            $mu_vhost_ident = "";
            $mu_vhost_host = "";
            $mu_vhost_creator = "";
            $mu_vhost_time = "";

            $mu_account = "";
            $mu_pass = "";
            $mu_lang = "";
            $mu_email = "";
            $mu_register = "";
            $is_na = false;
            $tmplined = "";
        }
    }

    if($is_cu or ($data2[0] == 'OBJECT' && $data2[1] == "ChannelInfo")) {

        $is_md = false;
        $is_mu = false;
        $is_cu = true;
        $is_na = false;

        if (isset($data2[1]) && $data2[1] == 'name') {
            $cu_name = $data2[2];
        }

        if (isset($data2[1]) && $data2[1] == 'time_registered') {
            $cu_register = $data2[2];
        }


        if (isset($data2[1]) && $data2[1] == 'last_used') {
            $cu_last_used = $data2[2];
        }

        if (isset($data2[1]) && $data2[1] == 'founder') {
            $cu_founder = $data2[2];
        }

        if (isset($data2[1]) && $data2[1] == 'last_topic') {
            $cu_last_topic = "";
            for($i2 = 2; $i2 < count($data2); $i2++) {
                if (!empty($cu_last_topic)) {
                    $cu_last_topic .= " ".$data2[$i2];
                }
                else {
                    $cu_last_topic .= $data2[$i2];
                }
            }
        }

        if (isset($data2[1]) && $data2[1] == 'last_topic_setter') {
            $cu_last_topic_setter = $data2[2];
        }

        if (isset($data2[1]) && $data2[1] == 'last_topic_time') {
            $cu_last_topic_time = $data2[2];
        }

        if (isset($data2[1]) && $data2[1] == 'KEEPTOPIC') {
           if ($data2[2] == "1") {
               $channel_flags .= "k";
           }
        }

        if (isset($data2[1]) && $data2[1] == 'CS_SECURE') {
            if ($data2[2] == "1") {
                $channel_flags .= "z";
            }
        }

        if (isset($data2[1]) && $data2[1] == 'CS_PRIVATE') {
            if ($data2[2] == "1") {
                $channel_flags .= "p";
            }
        }

        if (isset($data2[1]) && $data2[1] == 'CS_RESTRICT') {
            if ($data2[2] == "1") {
                $channel_flags .= "r";
            }
        }

        if (!empty($cu_name)) {
            $channelset[$cu_name] = array(
                "cu_name"   =>  $cu_name,
                "cu_register"   =>  $cu_register,
                "cu_last_used"  =>  $cu_last_used,
                "cu_founder"    =>  $cu_founder,
                "cu_last_topic" =>  $cu_last_topic,
                "cu_last_topic_setter"  =>  $cu_last_topic_setter,
                "cu_last_topic_time"    =>  $cu_last_topic_time,
                ##"channel_flags" =>  $channel_flags,
                "channel_flags" => 600,
            );
        }

        if ($data2[0] == "END") {
            $is_cu = false;
            $cu_name = "";
            $cu_register = 0;
            $cu_last_used = 0;
            $cu_founder = "";
            $cu_last_topic = "";
            $cu_last_topic_setter = "";
            $cu_last_topic_time = 0;
            $channel_flags = "+";
        }
    }

    if($is_ca or ($data2[0] == 'OBJECT' && $data2[1] == "ChanAccess")) {
        $is_md = false;
        $is_mu = false;
        $is_cu = false;
        $is_na = false;
        $is_ca = true;

        if (isset($data2[1]) && $data2[1] == "provider") {
            $ca_provider = $data2[2];
        }

        if (isset($data2[1]) && $data2[1] == "ci") {
            $ca_channel = $data2[2];
        }

        if (isset($data2[1]) && $data2[1] == "mask") {
            $ca_mask = $data2[2];
        }

        if (isset($data2[1]) && $data2[1] == "creator") {
            $ca_creator = $data2[2];
        }

        if (isset($data2[1]) && $data2[1] == "created") {
            $ca_created = $data2[2];
        }

        if (isset($data2[1]) && $data2[1] == "data") {
            if ($ca_provider == "access/xop") {
                $ca_level = $levels[$data2[2]];
            }
            else if ($ca_provider == "access/access") {
                $ca_level = $access[$data2[2]];
            }
        }

        if ($data2[0] == "END") {
            $channel_access = array(
                "ca_channel"    =>  $ca_channel,
                "ca_mask"   =>  $ca_mask,
                "ca_creator"    =>  $ca_creator,
                "ca_created"    =>  $ca_created,
                "ca_level"  =>  $ca_level,
            );

            if (!empty($userset[$ca_mask])) {
                $channelset[$ca_channel]['channel_access'][] = $channel_access;
            }
            $is_ca = false;
            $channel_access = array();
            $ca_channel = "";
            $ca_mask = "";
            $ca_creator = "";
            $ca_created = "";
            $ca_level = "";
            $ca_provider = "";
        }
    }
}

$atheme = fopen("atheme.db", 'w');
fwrite($atheme, "DBV 7\n");
fwrite($atheme, "CF +vVoOtsriRfhHAb\n");

/* MDU Obi_Wan private:usercloak city-1464.galaxy-media.highway.hyperspace
MDU Obi_Wan private:usercloak-assigner Obi_Wan
MDU Obi_Wan private:usercloak-timestamp 1476550194
*/
foreach ($userset as $key => $value) {
    fwrite($atheme, "MU ".$value['mu_account']." ".$value['mu_pass']. " ".$value['mu_email']." ".$value['mu_register']." ".time()." ".$mu_flags." ".$mu_lang."\n");

    if (!empty($value['vhost_host'])) {
        fwrite($atheme, "MDU ".$value['mu_account']." private:usercloak ".$value['vhost_host']."\n");
    }

    if (!empty($value['vhost_creator'])) {
        fwrite($atheme, "MDU ".$value['mu_account']." private:usercloak-assigner ".$value['vhost_creator']."\n");
    }

    if (!empty($value['vhost_time'])) {
        fwrite($atheme, "MDU ".$value['mu_account']." private:usercloak-timestamp ".$value['vhost_time']."\n");
    }

    if (isset($value['aliases'])) {
        foreach ($value['aliases'] as $key2 => $value2) {
            fwrite($atheme, "MN ". $value['mu_account'] ." ".$key2." ".$value2['mu_register']." ".$value2['last_seen']."\n");
        }
    }
}

foreach ($channelset as $key => $value) {
    fwrite($atheme, "MC ".$key." ".$value['cu_register']." ".$value['cu_last_used']." ".$value['channel_flags']." 272 6 0\n");
    fwrite($atheme, "CA ".$key." ".$value['cu_founder']." +AFORafhioqrstv ".$value['cu_register']." ".$value['cu_founder']."\n");
    if (isset($value['channel_access'])) {
        foreach ($value['channel_access'] as $key2 => $value2) {
            fwrite($atheme, "CA ". $value2['ca_channel']." ".$value2['ca_mask']." ".$value2['ca_level']." ".$value2['ca_created']." ".$value2['ca_creator']."\n");
          #  die($key2);
        }
    }
    fwrite($atheme, "MDC ".$key." private:topic:setter ".$value['cu_last_topic_setter']."\n");
    fwrite($atheme, "MDC ".$key." private:topic:text ".$value['cu_last_topic']."\n");
    fwrite($atheme, "MDC ".$key." private:topic:ts ".$value['cu_last_topic_time']."\n");
}

fclose($atheme);
