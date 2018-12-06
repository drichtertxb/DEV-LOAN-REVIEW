<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Log;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('guest')->except('logout');
        Log::info("LoginController construct");
    }

    public function logout(Request $request) {
        $this->guard()->logout();

        // And redirect.
        return redirect($this->redirectTo);
    }

    protected function authenticated($request, $user)
    {
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

            ob_start();
            var_dump($entries);
            $res = ob_get_clean();
            Log::info("entries [" . $res . "]");

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
        }
    }
}
