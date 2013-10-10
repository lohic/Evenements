  <table cellspacing="0" cellpadding="0" border="0" style="border-right: 2px solid #000;border-top: 2px solid #000;border-left: 2px solid #000;border-bottom: 2px solid #000;">
    <tr>
      <td width="25" height="25" bgcolor="#CC0000"></td>
      <td width="110" bgcolor="#CC0000"></td>
      <td width="175" bgcolor="#CC0000"></td>
      <td width="5" bgcolor="#CC0000"></td>
      <td width="5" bgcolor="#CC0000"></td>
      <td width="95" bgcolor="#CC0000"></td>
      <td width="190" bgcolor="#CC0000"></td>
      <td width="25" bgcolor="#CC0000"></td>
    </tr>

    <tr>
      <td bgcolor="#CC0000"></td>
      <td colspan="4" bgcolor="#CC0000"><img src="<?php echo $this->absoluteBilletFolder.'images/pdf/';?>logo-billet-pdf.png" /></td>
      <td colspan="2" align="right" valign="bottom" bgcolor="#CC0000"><p style="font-style:italic;"><?php echo $this->uc_strtoupper_fr($this->ecouteurs? 'Écouteurs réservés' : 'Pas d\'écouteurs'); ?></p></td>
      <td bgcolor="#CC0000"></td>
    </tr>

    <tr>
      <td height="25" bgcolor="#CC0000"></td>
      <td bgcolor="#CC0000"></td>
      <td bgcolor="#CC0000"></td>
      <td bgcolor="#CC0000"></td>
      <td bgcolor="#CC0000"></td>
      <td bgcolor="#CC0000"></td>
      <td bgcolor="#CC0000"></td>
      <td bgcolor="#CC0000"></td>
    </tr>

    <tr>
      <td></td>
      <td colspan="6">&nbsp;</td>
      <td></td>
  </tr>
    <tr>
      <td></td>
      <td colspan="6"><h1 style="font-weight:normal;"><?php echo $this->uc_strtoupper_fr($this->session_name);?></h1></td>
      <td></td>
  </tr>

    <tr>
      <td></td>
      <td colspan="6"><h3 style="font-style:italic;font-weight:normal;"><?php echo 'Le '.$this->date.' à '.$this->horaire; ?></h3></td>
      <td></td>
  </tr>

    <tr>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td colspan="2"></td>
      <td></td>
  </tr>
    <tr>
      <td></td>
      <td>Nom :</td>
      <td><strong><?php echo $this->uc_strtoupper_fr($this->nom); ?></strong></td>
      <td rowspan="10" style="border-right:1px dotted #000;">&nbsp;</td>
      <td>&nbsp;</td>
      <td colspan="2" rowspan="10" align="center" valign="top">
      	<h2 style="font-weight:normal;"><?php echo $this->uc_strtoupper_fr($this->acces); ?></h2>       <p><?php echo $QRcode; ?></p><p><?php echo $barcode1D; ?></p></td>
      <td></td>
  </tr>

    <tr>
      <td></td>
      <td>Pr&eacute;nom :</td>
      <td><strong><?php echo $this->prenom; ?></strong></td>
      <td>&nbsp;</td>
      <td></td>
  </tr>

    <tr>
      <td></td>
      <td>N&deg; :</td>
      <td><strong><?php echo $this->presentUniqueID(); ?></strong></td>
      <td></td>
      <td></td>
  </tr>

    <tr>
      <td></td>
      <td>Statut :</td>
      <td><strong><?php echo $this->statut; ?></strong></td>
      <td>&nbsp;</td>
      <td></td>
  </tr>

    <tr>
      <td></td>
      <td>Organisateur :</td>
      <td><strong><?php echo $this->organisateur;?></strong></td>
      <td></td>
      <td></td>
  </tr>

    <tr>
      <td></td>
      <td>Adresse :</td>
      <td><strong><?php echo $this->lieu;?></strong></td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <td></td>
      <td colspan="2" style="font-size:9px;"><p><strong>INFORMATIONS COMPLÉMENTAIRES :</strong><br/>
      Ce billet sera contrôlé à l'entrée, nous vous conseillons vivement d'arriver au plus tard 20 minutes avant le début de l'événement. - Pour vérifier la bonne qualité du billet, assurez-vous que les informations, ainsi que le code barre, sont bien lisibles. - Ce billet est strictement personnel et incessible. Lors des contrôles, vous devrez obligatoirement être muni(e) d'une pièce d'identité, en cours de validité avec photo (carte d'identité, carte d'étudiant, passeport, permis de conduire ou carte de séjour). - Ce billet est uniquement valable pour cet événement, à la date et aux conditions mentionnées. - Sciences Po décline toute responsabilité en cas de perte ou de vol du billet ainsi que pour les anomalies pouvant survenir en cours de réservation ou de traitement du billet.</p></td>
      <td></td>
      <td></td>
  </tr>

    <tr>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
  </tr>

    <tr>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
  </tr>

    <tr>
      <td height="10"></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
  </tr>

    <tr>
      <td></td>
      <td colspan="2" bgcolor="#CC0000"><h2 style="color:#FFF;text-align:center;margin:0;padding:0;font-weight:normal;">Précautions d'emploi</h2></td>
      <td></td>
      <td></td>
      <td colspan="2" rowspan="3" valign="top"><a href="http://boutique.sciences-po.fr" target="_blank"><img src="<?php echo $this->imageBillet;?>" /></a></td>
      <td></td>
    </tr>
    <tr>
      <td></td>
      <td colspan="2" align="left" valign="top">
        <p style="font-size:12px;margin:0;">- Ce billet est reconnu électroniquement lors de  votre arrivée<br/>
        - À ce titre, il ne doit être ni dupliqué ni photocopié.<br/>
        - Seule la première personne se présentant avec le  billet sera admise à l'événement.<br/>
        - >Le détenteur du billet est responsable de son  utilisation.</p>
      </td>
      <td></td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <td></td>
      <td colspan="2" align="left" valign="top"></td>
      <td></td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <td height="10"></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
    </tr>

    <tr>
      <td>&nbsp;</td>
      <td colspan="6"><a href="<?php echo $this->url_image;?>"><img src="<?php echo $this->absoluteBilletFolder.'images/pdf/billet_event.jpg'; ?>" /></a></td>
      <td></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
    </tr>
  </table>