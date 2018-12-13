<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Http\Request;
use Log;

use App\Http\Requests;

class LdapTestController extends Controller {

    function ldapErrStr($ldapConn) {

        $data = '';

        $ldapInfo = " Here in " . __CLASS__ . "." . __METHOD__ . "-authenticated Bind failed - " . ldap_error($ldapConn) . "]";
        if(ldap_get_option($ldapConn, LDAP_OPT_DIAGNOSTIC_MESSAGE, $extended_error)) {
            $ldapInfo .= " Error Binding to LDAP: $extended_error";
        } else {
            $ldapInfo .= " Error Binding to LDAP: No additional information is available.";
        }
        Log::info("ldap [" . $ldapInfo . "]");

        // Bad connection.
        $data .= " Bad conection.";
    }

    function cleanUpEntry( $entry ) {
        $retEntry = array();
        for ( $i = 0; $i < $entry['count']; $i++ ) {
            if (is_array($entry[$i])) {
                $subtree = $entry[$i];
                //This condition should be superfluous so just take the recursive call
                //adapted to your situation in order to increase perf.
                if ( ! empty($subtree['dn']) and ! isset($retEntry[$subtree['dn']])) {
                    $retEntry[$subtree['dn']] = $this->cleanUpEntry($subtree);
                }
                else {
                    $retEntry[] = $this->cleanUpEntry($subtree);
                }
            }
            else {
                $attribute = $entry[$i];
                if ( $entry[$attribute]['count'] == 1 ) {
                    $retEntry[$attribute] = $entry[$attribute][0];
                } else {
                    for ( $j = 0; $j < $entry[$attribute]['count']; $j++ ) {
                        $retEntry[$attribute][] = $entry[$attribute][$j];
                    }
                }
            }
        }
        return $retEntry;
    }

    function ldapPublicSearchResult($args, $ldapConn, $data = null) {

        $rdata = '<table border=2 width="900" align="center"><tr><td align="center"> User data from public LDAP server ' . '</td></tr><tr><td align="center">';
        if($data) {
            $rdata .= $data;
        } else {

            $memberRows     = array();
            $filter = "(uniqueMember=*)";
            $result = ldap_search($ldapConn, $args['ldapUser'], $filter);
            $entryArray = $this->cleanUpEntry(ldap_get_entries($ldapConn, $result));

            foreach ($entryArray['ou=mathematicians,dc=example,dc=com']['uniquemember'] as $member) {
                $memberRows [] = explode('=', explode(",", $member)[0])[1];
            }
            sort($memberRows);
            foreach($memberRows as $row) {
                $rdata .= "<tr><td align='center'>" . $row . "</td></tr>";
            }
        }
        $rdata .= '</td></tr></table><br><br>';
        return $rdata;
    }

    function ldapPGServerSearchResult($args, $ldapConn, $data = null) {

        $rdata = '<table border=2 width="900" align="center"><tr><td align="center"> User data PANDGASSOCIATES LDAP server ' . '</td></tr><tr><td align="center">';
        if($data) {
            $rdata .= $data;
        } else {

            $memberRows     = array();
            $group          = "OU=TXBUsers,OU=PNGUsers,DC=pandgassociates,DC=com";
            $result         = ldap_search($ldapConn, $group,'cn=*',array('member'));
            $entryArray     = $this->cleanUpEntry(ldap_get_entries($ldapConn, $result));

            foreach ($entryArray['CN=TXB_GAL,OU=TXBUsers,OU=PNGUsers,DC=pandgassociates,DC=com']['member'] as $member) {
                $memberRows [] = explode('=', explode(",", $member)[0])[1];
            }
            sort($memberRows);
            foreach($memberRows as $row) {
                $rdata .= "<tr><td align='center'>" . $row . "</td></tr>";
            }
        }
        $rdata .= '</td></tr></table><br><br>';
        return $rdata;
    }

    function ldapPublicTestServerData($args, &$ldapConn) {

        $ret = '';
        $ldapConn = ldap_connect($args['ldapHostIp'], $args['ldapPort']);
        if($ldapConn) {
            ldap_set_option($ldapConn, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($ldapConn, LDAP_OPT_REFERRALS, 0);

            if (@ldap_bind($ldapConn, $args['ldapAdminUser'], $args['ldapPasswd'])) {
                // Here LoginController-authenticated Bind succeeded.";
                $ret = $this->ldapPublicSearchResult($args, $ldapConn);

            } else {
                $errStr = "LDAP server connection failed with IP [" . $args['ldapHostIp'] . "], port [" . $args['ldapPort'] . "],  Admin User [" . $args['ldapUser'] . "], password [" . $args['ldapPasswd'] . "] . error [" . $this->ldapErrStr($ldapConn) . "]";
                $ret .= $this->ldapPublicSearchResult($args, $ldapConn, $errStr );
            }
        }  else {
            $ret = $this->ldapPublicSearchResult($args, $ldapConn,"LDAP server conection failed.");
        }

        return view('ldaptest') . '<br>' . $ret;
    }

    function ldapPGServerData($args, &$ldapConn) {

        $ret = '';
        $ldapConn = ldap_connect($args['ldapHost']);
        if($ldapConn) {
            ldap_set_option($ldapConn, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($ldapConn, LDAP_OPT_REFERRALS, 0);

            ldap_start_tls($ldapConn);

            if (@ldap_bind($ldapConn, $args['ldapAdminUser'], $args['ldapPasswd'])) {
                //$ret = $this->ldapPGServerSearchResult($ldapConn, $args['ldapUser']," Connection established to server " . $args['ldapHost'] . " " );
                $ret = $this->ldapPGServerSearchResult($args, $ldapConn);
            } else {
                $errStr = "LDAP server conection failed with IP [" . $args['ldapHostIp'] . "], port [" . $args['ldapPort'] . "],  Admin User [" . $args['ldapUser'] . "], password [" . $args['ldapPasswd'] . "] error [" .  $this->ldapErrStr($ldapConn) . "]";
                $ret = $this->ldapPGServerSearchResult($args, $ldapConn, $errStr );
            }
        } else {
            $ret = $this->ldapPGServerSearchResult($args, $ldapConn, "LDAP server conection failed.");
        }

        return view('ldaptest') . '<br>' . $ret;
    }

    public function index() {

        $ldapConn = null;
        if(env("LDAP_SERVER") == "ldap.forumsys.com") {

            $args = array(
                "ldapHostIp"        => env("LDAP_TEST_HOST_IP"),
                "ldapPort"          => env("LDAP_TEST_PORT"),
                "ldapAdminUser"     => env("LDAP_TEST_ADMIN_USER"),
                "ldapUser"          => env("LDAP_TEST_USER"),
                "ldapPasswd"        => env("LDAP_TEST_PASSWD"),
            );
            return  $this->ldapPublicTestServerData($args, $ldapConn);
        } else  if(env("LDAP_SERVER") == "pandgassociates") {

            $args = array(
                "ldapHostIp"        => env("LDAP_PG_HOST_IP"),
                "ldapHost"          => env("LDAP_PG_HOST_FQDN"),
                "ldapAdminUser"     => env("LDAP_PG_ADMIN_USER"),
                "ldapUser"          => env("LDAP_PG_USER"),
                "ldapPort"          => env("LDAP_PG_PORT"),
                "ldapPasswd"        => env("LDAP_PG_ADMIN_PASSWD"),
            );
            return $this->ldapPGServerData($args, $ldapConn);
        }

        if($ldapConn && is_resource($ldapConn)) {
            ldap_unbind($ldapConn);
            $gLdapConn = null;
        }
    }
}