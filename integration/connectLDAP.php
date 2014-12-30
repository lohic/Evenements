<?php
function connectLDAP($login=NULL,$password=NULL){
    // Eléments d'authentification LDAP

    $retour->info   = NULL;
    $retour->login  = NULL;
    $retour->prenom = NULL;
    $retour->nom    = NULL;
    $retour->email  = NULL;
    $retour->type   = NULL;

    if(isset($login) && isset($password) && $login!="" && $password!=""){
        $login = strtolower($login);
        
        $ldaprdn  = 'uid='.$login.',ou=Users,o=sciences-po,c=fr';
        $ldappass = $password;

        
        // Connexion au serveur LDAP
        $ldapconn = ldap_connect("ldap.sciences-po.fr") or die("Impossible de se connecter au serveur LDAP.");
        
        if ($ldapconn) {
            // Authentification au serveur LDAP
            $ldapbind = ldap_bind($ldapconn, $ldaprdn, $ldappass);
    
            // Vérification de l'authentification
            if ($ldapbind) {
                $retour->info = "ok";
        
                //recuperation des informations
                $sr=ldap_search($ldapconn,"ou=Users, o=sciences-po, c=fr", "uid=".$login);
                $info = ldap_get_entries($ldapconn, $sr);
                for ($i=0; $i<$info["count"]; $i++) 
                {
                    if ( isset($info[$i]["cn"][0]) ){           $retour->login  = $info[$i]["cn"][0]; }
                    if ( isset($info[$i]["givenname"][0]) ){    $retour->prenom = $info[$i]["givenname"][0]; }
                    if ( isset($info[$i]["sn"][0]) ){           $retour->nom    = $info[$i]["sn"][0]; }
                    if ( isset($info[$i]["mail"][0]) ){         $retour->email  = $info[$i]["mail"][0]; }
                    if ( isset($info[$i]["employeetype"][0]) ){ $retour->type   = $info[$i]["employeetype"][0]; }

                }
                ldap_close($ldapconn);
            } else {
                $retour->info = "login error";
            }
        
        }else{
            $retour->info = "no connexion";
        }
    }else{
        $retour->info = "no login";
    }
    return $retour;
}
?>