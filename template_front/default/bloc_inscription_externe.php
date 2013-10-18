<script id="inscription_externe_form" type="text/html">
    <div id="cartouche">
        <h2 class="little_bigger">Inscription</h2>
        
        <div class="description_evenement">
            <h3 class="bigger">{{titre}}</h3>
            {{#date}}<p class="date very_bigger">{{date}}</p>{{/date}}
            {{#lieu}}<p class="lieu very_bigger">{{lieu}}</p>{{/lieu}}
        </div>

        {{#externeOuvert}}
        <div class="formulaire_externe">
            <h3 class="bit_big">Vous êtes externe à Sciences po</h3>
            <form>
                {{#alerteExterne}}{{{alerteExterne}}{{/alerteExterne}}
                <input type="hidden" id="id_session" name="id_session" value="{{session_id}}"/>
                <input type="hidden" id="titre" name="titre" value="{{titre}}"/>
                <input type="hidden" id="date" name="date" value="{{date}}"/>
                <input type="hidden" id="lieu" name="lieu" value="{{lieu}}"/>

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
                <p class="bit_small"><a href="#" id="envoyer_externe">Valider</a>
            </form>
        </div>
        {{/externeOuvert}}

        {{#externeComplet}}
        <div class="plus_de_place">
            <h3 class="bit_big">Vous êtes externe à Sciences po</h3>
            <p class="bit_small">Il n’y a plus de place «externe» à cet événement</p>
        </div>
        {{/externeComplet}}

        {{#codeErreur}}{{{codeErreur}}}{{/codeErreur}}

        <div class="mentions small">
            <p>{{mention}}</p>
        </div>
    </div>
</script>