<script id="validation_externe_form_multiple" type="text/html">
    <div id="cartouche">
        //cas où on a pas pu se connecter au LDAP
        {{#erreurLDAP}}
        <h2 class="little_bigger">Inscription</h2>
            
        <div class="description_evenement">
            <h3 class="bigger">{{titre}}</h3>
            <p class="date very_bigger">{{date}}</p>
            {{#lieu}}<p class="lieu very_bigger">{{lieu}}</p>{{/lieu}}
        </div>
        <div class="formulaire_interne">
            <h3 class="bit_big">Vous êtes interne à Sciences po</h3>
            <form>
                {{#toutesLesSessions}}
                    <div class="session">
                        <p class="bit_big"><input type="checkbox" name="sessions[]" value="{{identifiant}}" id="session_{{identifiant}}"/><label for="session_{{identifiant}}" class="session">{{nom}}</label></p>
                        {{#casque}}
                            <p class="bit_small"><label for="casque_{{identifiant}}">Réserver un casque pour la traduction : </label><input name="inscrit_casque[]" type="checkbox" id="casque_{{identifiant}}" value="{{identifiant}}"/></p>
                        {{/casque}}
                        <p class="bit_small horaire">{{horaire}} {{#placement}} {{placement}} {{/placement}}</p>
                        <p class="bit_small lieu">{{lieu}}</p>
                    </div>
                {{/toutesLesSessions}}
                <input type="hidden" id="id_evenement" name="id_evenement" value="{{id}}"/>
                <input type="hidden" id="titre" name="titre" value="{{titre}}"/>
                <input type="hidden" id="date" name="date" value="{{date}}"/>
                <p class="bit_small"><label for="login">Identifiant* :</label><input type="text" id="login" name="login" /></p>
                <p class="bit_small"><label for="password">Mot de passe* :</label><input type="password" id="password" name="password" /></p>
                <p class="erreur bit_small">* Champs obligatoires</p>
                <p class="erreur bit_small">{{erreurLDAP}}</p>
                <p class="bit_small"><a href="#" id="renvoyer_multiple" class="">Valider</a>
            </form>
        </div>
        <div class="mentions small">
            <p>{{mention}}</p>
        </div>
        {{/erreurLDAP}}

        //Cas où les champs login ou mdp ne sont pas saisis
        {{#champVide}}
        <h2 class="little_bigger">Inscription</h2>
            
        <div class="description_evenement">
            <h3 class="bigger">{{titre}}</h3>
            <p class="date very_bigger">{{date}}</p>
            {{#lieu}}<p class="lieu very_bigger">{{lieu}}</p>{{/lieu}}
        </div>
        <div class="formulaire_interne">
            <h3 class="bit_big">Vous êtes interne à Sciences po</h3>
            <form>
                {{#toutesLesSessions}}
                    <div class="session">
                        <p class="bit_big"><input type="checkbox" name="sessions[]" value="{{identifiant}}" id="session_{{identifiant}}"/><label for="session_{{identifiant}}" class="session">{{nom}}</label></p>
                        {{#casque}}
                            <p class="bit_small"><label for="casque_{{identifiant}}">Réserver un casque pour la traduction : </label><input name="inscrit_casque[]" type="checkbox" id="casque_{{identifiant}}" value="{{identifiant}}"/></p>
                        {{/casque}}
                        <p class="bit_small horaire">{{horaire}} {{#placement}} {{placement}} {{/placement}}</p>
                        <p class="bit_small lieu">{{lieu}}</p>
                    </div>
                {{/toutesLesSessions}}
                <input type="hidden" id="id_evenement" name="id_evenement" value="{{id}}"/>
                <input type="hidden" id="titre" name="titre" value="{{titre}}"/>
                <input type="hidden" id="date" name="date" value="{{date}}"/>
                <p class="bit_small"><label for="login">Identifiant* :</label><input type="text" id="login" name="login" /></p>
                <p class="bit_small"><label for="password">Mot de passe* :</label><input type="password" id="password" name="password" /></p>

                <p class="erreur bit_small">* Champs obligatoires</p>
                <p class="erreur bit_small">{{champVide}}</p>
                <p class="bit_small"><a href="#" id="renvoyer_multiple" class="">Valider</a>
            </form>
        </div>
        <div class="mentions small">
            <p>{{mention}}</p>
        </div>
        {{/champVide}}

        //Erreur dans les champs de saisie (si aucune session sélectionnée)
        {{#erreurChamps}}
        <h2 class="little_bigger">Inscription</h2>
            
        <div class="description_evenement">
            <h3 class="bigger">{{titre}}</h3>
            <p class="date very_bigger">{{date}}</p>
            {{#lieu}}<p class="lieu very_bigger">{{lieu}}</p>{{/lieu}}
        </div>
        <div class="formulaire_interne">
            <h3 class="bit_big">Vous êtes interne à Sciences po</h3>
            <form>
                {{#toutesLesSessions}}
                    <div class="session">
                        <p class="bit_big"><input type="checkbox" name="sessions[]" value="{{identifiant}}" id="session_{{identifiant}}"/><label for="session_{{identifiant}}" class="session">{{nom}}</label></p>
                        {{#casque}}
                            <p class="bit_small"><label for="casque_{{identifiant}}">Réserver un casque pour la traduction : </label><input name="inscrit_casque[]" type="checkbox" id="casque_{{identifiant}}" value="{{identifiant}}"/></p>
                        {{/casque}}
                        <p class="bit_small horaire">{{horaire}} {{#placement}} {{placement}} {{/placement}}</p>
                        <p class="bit_small lieu">{{lieu}}</p>
                    </div>
                {{/toutesLesSessions}}
                <input type="hidden" id="id_evenement" name="id_evenement" value="{{id}}"/>
                <input type="hidden" id="titre" name="titre" value="{{titre}}"/>
                <input type="hidden" id="date" name="date" value="{{date}}"/>
                <p class="bit_small"><label for="login">Identifiant* :</label><input type="text" id="login" name="login" /></p>
                <p class="bit_small"><label for="password">Mot de passe* :</label><input type="password" id="password" name="password" /></p>
                
                <p class="erreur bit_small">* Champs obligatoires</p>
                <p class="erreur bit_small">{{erreurChamps}}</p>
                <p class="bit_small"><a href="#" id="renvoyer_multiple" class="">Valider</a>
            </form>
        </div>
        <div class="mentions small">
            <p>{{mention}}</p>
        </div>
        {{/erreurChamps}}

        //Succès de l'inscription
        {{^erreurLDAP}}
            {{^champVide}}
                {{^erreurChamps}}
                    <div class="confirmation">
                        <h3 class="little_bigger">{{title}}</h3>
                        <h4 class="bit_big">{{titre}}</h4>
                        <p class="date bit_big">{{date}}</p>
                        <p class="lieu bit_big">{{lieu}}</p>
                    </div>
                    {{#sessions}}
                        <div class="informations_inscription">
                            <h4 class="bit_big">{{session_nom}}</h4>
                            <p class="date bit_big">{{horaire}}</p>
                            <p class="lieu bit_big">{{endroit}}</p>
                            {{#dejaInscrit}}
                                <p class="nom biggest">{{dejaInscrit}}</p>
                            {{/dejaInscrit}}

                            {{#completeDerniereMinute}}
                                <p class="nom biggest">{{completeDerniereMinute}}</p>
                            {{/completeDerniereMinute}}

                            {{#inscriptionOK}}
                                <p>{{infos_inscription}}</p>
                                <p class="nom biggest">{{nom}} <span>{{prenom}}</span></p>
                                <p class="lieu biggest">{{type_inscription}}</p>
                                <p class="numero biggest">{{numero}}</p>
                            {{/inscriptionOK}}
                        </div>
                    {{/sessions}}
                    <div class="important bit_small">
                        <p>{{{important}}}</p>
                    </div>
                {{/erreurChamps}}
            {{/champVide}}
        {{/erreurLDAP}}
    </div>
</script>