<script id="inscription_form" type="text/html">
    <div id="cartouche">
        <h2 class="little_bigger">Inscription</h2>
        
        <div class="description_evenement">
            <h3 class="bigger">{{titre}}</h3>
            <p class="date very_bigger">{{date}}</p>
            {{#lieu}}<p class="lieu very_bigger">{{lieu}}</p>{{/lieu}}
        </div>

        {{#interneOuvert}}
        <div class="formulaire_interne">
            <h3 class="bit_big">Vous êtes interne à Sciences po</h3>
            <form>
                {{#alerteInterne}}{{{alerteInterne}}{{/alerteInterne}}
                <p class="bit_small"><label for="login">Identifiant* :</label><input type="text" id="login" name="login" /></p>
                <p class="bit_small"><label for="password">Mot de passe* :</label><input type="password" id="password" name="password" /></p>
                {{#casque}}
                <p>
                    <label for="inscrit_casque" class="inline">Réserver un casque pour la traduction :</label>
                    <input name="inscrit_casque" type="checkbox" id="inscrit_casque" value="1"/>
                </p>
                {{/casque}}
                <p class="erreur bit_small">* Champs obligatoires</p>
                <p class="erreur bit_small">Échec de connexion : identifiant ou mot de passe incorrect(s), veuillez recommencer.</p>
                <p class="bit_small"><a href="#" id="envoyer">Valider</a>
            </form>
        </div>
        {{/interneOuvert}}


        {{#interneComplet}}
        <div class="plus_de_place">
            <h3 class="bit_big">Vous êtes interne à Sciences po</h3>
            <p class="bit_small">Il n’y a plus de place «interne» à cet événement</p>
        </div>
        {{/interneComplet}}

        {{#externeOuvert}}
        <div class="formulaire_externe">
            <h3 class="bit_big">Vous êtes externe à Sciences po</h3>
            <form>
                {{#alerteExterne}}{{{alerteExterne}}{{/alerteExterne}}
                <p class="bit_small"><label for="nom">Nom* :</label><input type="text" id="nom" name="nom" /></p>
                <p class="bit_small"><label for="prenom">Prénom* :</label><input type="text" id="prenom" name="prenom" /></p>
                <p class="bit_small"><label for="mail">Mail* :</label><input type="text" id="mail" name="mail" /></p>
                <p class="bit_small"><label for="entreprise">Organisation :</label><input type="text" id="entreprise" name="entreprise" /></p>
                <p class="bit_small"><label for="fonction">Fonction :</label><input type="text" id="fonction" name="fonction" /></p>
                {{#casque}}
                <p>
                    <label for="inscrit_casque" class="inline">Réserver un casque pour la traduction :</label>
                    <input name="inscrit_casque" type="checkbox" id="inscrit_casque" value="1"/>
                </p>
                {{/casque}}
                <p class="erreur bit_small">* Champs obligatoires</p>
                <p class="erreur bit_small">Échec : tous les champs sont obligatoires.</p>
                <p class="bit_small"><a href="#" id="envoyer_externe">Valider</a>
            </form>
        </div>
        {{/externeOuvert}}

        {{#externeComplet}}
        <div class="plus_de_place">
            <h3 class="bit_big">Vous êtes interne à Sciences po</h3>
            <p class="bit_small">Il n’y a plus de place «externe» à cet événement</p>
        </div>
        {{/externeComplet}}

        {{#toutComplet}}{{{toutComplet}}}{{/toutComplet}}

        {{#toutClos}}{{{toutClos}}}{{/toutClos}}

        <div class="mentions small">
            <p>Mention CNIL : Les informations qui vous concernent sont destinées exclusivement à Sciences Po. Vous disposez dun droit daccès, de modification, de rectification et de suppression des données qui vous concernent (art. 34 de la loi « Informatique et Libertés »). Pour lexercer, adressez-vous à Sciences Po Pôle Evénements - 27 rue Saint Guillaume - 75007 Paris</p>
        </div>
    </div>
</script>

<script id="validation_form" type="text/html">
    <div class="confirmation">
        <h3 class="little_bigger">Vous êtes bien inscrit à lévénement</h3>
        <h4 class="bit_big">Intégration économique et conflit de souveraineté : peut-on circonscrire le politique ?</h4>
        <p class="date bit_big">Samedi 06/07</p>
        <p class="horaire bit_big">de 18:00 à 19:30</p>
        <p class="lieu bit_big">Amphithéâtre</p>
    </div>

    <div class="informations_inscription">
        <p>Vos informations inscription sont les suivantes :</p>
        <p class="nom biggest">SOPHIE <span>sophie</span></p>
        <p class="lieu biggest">AMPHITHÉÂTRE</p>
        <p class="numero biggest">7101101201129</p>
    </div>

     <div class="important bit_small">
        <p><strong>IMPORTANT :</strong> Un mail contenant un billet au format .pdf vous a été envoyé à ladresse sophblum@free.fr. <strong>Veuillez imprimer le billet et vous présenter à laccueil à ladresse spécifiée.</strong></p>
    </div>

    <div class="deja_inscrit">
        <h3 class="little_bigger">VOUS ÊTES déjà inscrit à cet événement !</h3>
        <a href="#" class="bit_big">Retour à la liste dévénements</a>
    </div>

    <div>
        <h2>{{reponse}} et :</h2>
        <p>votre login : <strong>{{login}}</strong></p>
        <p>votre mot de passe : <strong>{{password}}</strong></p>
        <p>votre adresse mail : <strong>{{email}}</strong></p>
    </div>
</script>