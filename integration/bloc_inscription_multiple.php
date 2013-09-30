<script id="inscription_form_multiple" type="text/html">
    <div id="cartouche">
        <h2 class="little_bigger">Inscription</h2>
        
        <div class="description_evenement">
            <h3 class="bigger">{{titre}}</h3>
            <p class="date very_bigger">{{date}}</p>
        </div>

        {{#interneOuvert}}
            <div class="formulaire_interne">
                <h3 class="bit_big">Vous êtes interne à Sciences po</h3>
                <form>
                    {{#sessions}}
                        {{#pascomplete}}
                            <div class="session">
                                <p class="bit_big"><input type="checkbox" name="sessions[]" value="{{identifiant}}" id="session_{{identifiant}}"/><label for="session_{{identifiant}}" class="session">{{nom}}</label></p>
                                {{#casque}}
                                    <p class="bit_small"><label for="casque_{{identifiant}}">Réserver un casque pour la traduction : </label><input name="inscrit_casque[]" type="checkbox" id="casque_{{identifiant}}" value="{{identifiant}}"/></p>
                                {{/casque}}
                                <p class="bit_small horaire">{{horaire}} {{#placement}} (vous serez placé en salle de retransmission) {{/placement}}</p>
                                <p class="bit_small lieu">{{lieu}}</p>
                            </div>
                        {{/pascomplete}}
                    {{/sessions}}
                    <input type="hidden" id="id_evenement" name="id_evenement" value="{{id}}"/>
                    <input type="hidden" id="titre" name="titre" value="{{titre}}"/>
                    <input type="hidden" id="date" name="date" value="{{date}}"/>
                    <p class="bit_small"><label for="login">Identifiant* :</label><input type="text" id="login" name="login" /></p>
                    <p class="bit_small"><label for="password">Mot de passe* :</label><input type="password" id="password" name="password" /></p>
                    
                    <p class="erreur bit_small">* Champs obligatoires</p>
                    <p class="bit_small"><a href="#" id="envoyer_multiple" class="">Valider</a>
                </form>
            </div>
        {{/interneOuvert}}

        {{^interneOuvert}}
        <div class="plus_de_place">
            <h3 class="bit_big">Vous êtes interne à Sciences po</h3>
            <p class="bit_small">Il n’y a plus de place «interne» à cet événement</p>
        </div>
        {{/interneOuvert}}

        {{#code}}
            {{#externeOuvert}}
                <div class="formulaire_externe">
                    <h3 class="bit_big">Vous êtes externe à Sciences po</h3>
                    <form>
                        {{#sessions}}
                            {{#pascompleteexterne}}
                                <div class="session">
                                    <p class="bit_big"><input type="checkbox" name="sessions_externe[]" value="{{identifiant}}" id="session_{{identifiant}}"/><label for="session_{{identifiant}}" class="session">{{nom}}</label></p>
                                    {{#casque}}
                                        <p class="bit_small"><label for="casque_{{identifiant}}">Réserver un casque pour la traduction : </label><input name="inscrit_casque_externe[]" type="checkbox" id="casque_{{identifiant}}" value="{{identifiant}}"/></p>
                                    {{/casque}}
                                    <p class="bit_small horaire">{{horaire}} {{#placementexterne}} (vous serez placé en salle de retransmission) {{/placementexterne}}</p>
                                    <p class="bit_small lieu">{{lieu}}</p>
                                </div>
                            {{/pascompleteexterne}}
                        {{/sessions}}
                        <input type="hidden" id="id_evenement_externe" name="id_evenement_externe" value="{{id}}"/>
                        <input type="hidden" id="titre_externe" name="titre_externe" value="{{titre}}"/>
                        <input type="hidden" id="date_externe" name="date_externe" value="{{date}}"/>
                        <p class="bit_small"><label for="nom">Nom* :</label><input type="text" id="nom" name="nom" /></p>
                        <p class="bit_small"><label for="prenom">Prénom* :</label><input type="text" id="prenom" name="prenom" /></p>
                        <p class="bit_small"><label for="mail">Mail* :</label><input type="text" id="mail" name="mail" /></p>
                        <p class="bit_small"><label for="entreprise">Organisation :</label><input type="text" id="entreprise" name="entreprise" /></p>
                        <p class="bit_small"><label for="fonction">Fonction :</label><input type="text" id="fonction" name="fonction" /></p>
                        
                        <p class="erreur bit_small">* Champs obligatoires</p>
                        <p class="bit_small"><a href="#" id="envoyer_externe_multiple" class="">Valider</a>
                    </form>
                </div>
            {{/externeOuvert}}

            {{^externeOuvert}}
            <div class="plus_de_place">
                <h3 class="bit_big">Vous êtes externe à Sciences po</h3>
                <p class="bit_small">Il n’y a plus de place «externe» à cet événement</p>
            </div>
            {{/externeOuvert}}
        {{/code}}

        {{#toutComplet}}{{{toutComplet}}}{{/toutComplet}}

        <div class="mentions small">
            <p>{{mention}}</p>
        </div>
    </div>
</script>