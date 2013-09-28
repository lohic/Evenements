<script id="validation_form" type="text/html">
    <div id="cartouche">
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
                {{#alerteInterne}}{{{alerteInterne}}{{/alerteInterne}}
                <input type="hidden" id="id_session" name="id_session" value="{{session_id}}"/>
                <input type="hidden" id="titre" name="titre" value="{{titre}}"/>
                <input type="hidden" id="date" name="date" value="{{date}}"/>
                <input type="hidden" id="lieu" name="lieu" value="{{lieu}}"/>
                <p class="bit_small"><label for="login">Identifiant* :</label><input type="text" id="login" name="login" /></p>
                <p class="bit_small"><label for="password">Mot de passe* :</label><input type="password" id="password" name="password" /></p>
                {{#casque}}
                <p>
                    <label for="inscrit_casque" class="inline">Réserver un casque pour la traduction :</label>
                    <input name="inscrit_casque" type="checkbox" id="inscrit_casque" value="1"/>
                </p>
                {{/casque}}
                <p class="erreur bit_small">* Champs obligatoires</p>
                <p class="erreur bit_small">{{erreurLDAP}}</p>
                <p class="bit_small"><a href="#" id="renvoyer" class="">Valider</a>
            </form>
        </div>
        {{/erreurLDAP}}

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
                {{#alerteInterne}}{{{alerteInterne}}{{/alerteInterne}}
                <input type="hidden" id="id_session" name="id_session" value="{{session_id}}"/>
                <input type="hidden" id="titre" name="titre" value="{{titre}}"/>
                <input type="hidden" id="date" name="date" value="{{date}}"/>
                <input type="hidden" id="lieu" name="lieu" value="{{lieu}}"/>
                <p class="bit_small"><label for="login">Identifiant* :</label><input type="text" id="login" name="login" /></p>
                <p class="bit_small"><label for="password">Mot de passe* :</label><input type="password" id="password" name="password" /></p>
                {{#casque}}
                <p>
                    <label for="inscrit_casque" class="inline">Réserver un casque pour la traduction :</label>
                    <input name="inscrit_casque" type="checkbox" id="inscrit_casque" value="1"/>
                </p>
                {{/casque}}
                <p class="erreur bit_small">* Champs obligatoires</p>
                <p class="erreur bit_small">{{champVide}}</p>
                <p class="bit_small"><a href="#" id="renvoyer" class="">Valider</a>
            </form>
        </div>
        {{/champVide}}

        {{^erreurLDAP}}
            {{^champVide}}
                <div class="confirmation">
                    {{^dejaInscrit}}
                        {{^completeDerniereMinute}}
                            <h3 class="little_bigger">{{title}}</h3>
                        {{/completeDerniereMinute}}
                    {{/dejaInscrit}}
                    <h4 class="bit_big">{{titre}}</h4>
                    <p class="date bit_big">{{date}}</p>
                    <p class="lieu bit_big">{{lieu}}</p>
                </div>
            {{/champVide}}
        {{/erreurLDAP}}

        {{#inscriptionOK}}
        <div class="informations_inscription">
            <p>{{infos_inscription}}</p>
            <p class="nom biggest">{{nom}} <span>{{prenom}}</span></p>
            <p class="lieu biggest">{{type}}</p>
            <p class="numero biggest">{{numero}}</p>
        </div>

        <div class="important bit_small">
            <p>{{{important}}}</p>
        </div>
        {{/inscriptionOK}}

        {{#dejaInscrit}}
        <div class="deja_inscrit">
            <h3 class="little_bigger">VOUS ÊTES déjà inscrit à cet événement !</h3>
            <a href="#" class="bit_big">Retour à la liste des événements</a>
        </div>
        {{/dejaInscrit}}

        {{#completeDerniereMinute}}
        <div class="plus_de_place">
            <p class="bit_small">{{{completeDerniereMinute}}}</p>
        </div>
        {{/completeDerniereMinute}}
    </div>
</script>