<script id="event_info" type="text/html">
    <article class="event resume">
        <div class="resumeContent">
            <div class="fermer" style="background-color:{{couleur}}">
                <a href="#" id="close"></a>
            </div>
            <div class="row">
                <div class="conteneur_detail">
                    <div class="col visuel">
                        {{#image}}<img src="{{image}}" alt="{{texte_image}}" id="vignette" width="320"/>{{/image}}
                        <h1 class="bit_big">{{#rubrique}}<span class="bit_small">{{rubrique}}</span>{{/rubrique}}{{titre}}</h1>
                    </div>
                    
                    <div class="col informations">
                        <h2 class="biggest">{{date}}</h2>
                        {{#langue}}<p class="langue"><span style="background-color:{{couleur}}"></span>{{langue}}</p>{{/langue}}
                        {{#lieu}}<p class="lieu"><span style="background-color:{{couleur}}"></span>{{lieu}}, {{batiment}}</p>{{/lieu}}
                        {{#inscription}}<p class="inscription"><span style="background-color:{{couleur}}"></span>{{{inscription}}}</p>{{/inscription}}
                        {{#organisateur}}<p class="organisateur"><span style="background-color:{{couleur}}"></span>{{organisateur}}<br/>{{coorganisateur}}</p>{{/organisateur}}
                        {{#infos}}<p class="infos"><span style="background-color:{{couleur}}"></span><a href="{{infos}}" target="_blank">{{infos_texte}}</a></p>{{/infos}}
                    </div>
                </div>  

                <div class="col contenu">
                    <div class="texte bit_big">
                        {{{texte}}}
                    </div>
                </div>

            </div>
            <div class="reset"></div>
            <div class="meta">
                <div>
                    <a href="{{facebook}}" class="reseaux facebook" style="background-color:{{couleur}}"  target="_blank"></a>

                    <a href="{{twitter}}" class="reseaux twitter" style="background-color:{{couleur}}" target="_blank" onclick="javascript:pageTracker._trackPageview ('/outbound/twitter.com');">
                    </a>

                    <a href="{{ical}}" target="_blank" class="reseaux ical" style="background-color:{{couleur}}">
                    </a>

                    {{#sinscrire}} {{{sinscrire}}} {{/sinscrire}}
                </div>
            </div>
            <div class="reset"></div>
            <div class="bottom"  style="background-color:{{couleur}}"></div>
        </div>
    </article>
</script>