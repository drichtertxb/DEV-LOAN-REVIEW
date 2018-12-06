<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Http\Request;

use App\Http\Requests;

class LdapTestController extends Controller {

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

    public function index() {

        $data = '<table border=2 width="900" align="center"><tr><td align="center"> User data from public LDAP server ' . '</td></tr><tr><td align="center">';

        if(env("LDAP_TEST_SERVER") == "true") {
            $ldapHostIp = env("LDAP_HOST_IP");
            $ldapHostName = env("LDAP_HOST_DNS");
            $ldapResolvedHostName = gethostbyaddr($ldapHostIp);
            $ldapPort = env("LDAP_PORT");
            $ldapAdminUser = env("LDAP_ADMIN_USER");
            $ldapUser = env("LDAP_USER");
            $ldapPasswd = env("LDAP_PASSWD");
        }

        // Protocol version 3 and no referrals are required for AD
        $ldapConn = ldap_connect($ldapHostIp, $ldapPort);
        ldap_set_option($ldapConn, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($ldapConn, LDAP_OPT_REFERRALS, 0);

        if(ldap_bind($ldapConn, $ldapAdminUser, $ldapPasswd)) {
            // Here LoginController-authenticated Bind succeeded.";
            // LDAP query for search
            $filter = "(uniqueMember=*)";
            $result  = ldap_search($ldapConn, $ldapUser, $filter);
            $entries = ldap_get_entries($ldapConn, $result);

            $entryArray = $this->cleanUpEntry($entries);

            foreach($entryArray['ou=mathematicians,dc=example,dc=com']['uniquemember'] as $member) {
                $data .= "<tr><td align='center'>" . $member . "</td></tr>";
            }

            if($ldapConn && is_resource($ldapConn)) {
                ldap_unbind($ldapConn);
                $gLdapConn = null;
            }
        } else {
            $ldapInfo = "Here in LoginController-authenticated Bind failed - " . ldap_error($ldapConn) . "]";
            if(ldap_get_option($ldapConn, LDAP_OPT_DIAGNOSTIC_MESSAGE, $extended_error)) {
                $ldapInfo .= "Error Binding to LDAP: $extended_error";
            } else {
                $ldapInfo .= "Error Binding to LDAP: No additional information is available.";
            }
            Log::info("ldap [" . $ldapInfo . "]");
            // Bad connection.
            $data .= " Bad conection.";
        }

        $data .= '</td></tr></table><br><br>';

        // Return everything.
        return view('ldaptest') . '<br>' . $data;
    }
}