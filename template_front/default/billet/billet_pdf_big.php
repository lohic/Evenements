  <table cellspacing="0" cellpadding="1" border="1" bgcolor="#CC0000">
    <tr>
      <td width="25" height="25"></td>
      <td width="100"></td>
      <td width="190"></td>
      <td width="100"></td>
      <td width="190"></td>
      <td width="25"></td>
    </tr>

    <tr>
      <td></td>
      <td colspan="2">Logo Grand</td>
      <td colspan="2"><?php echo $this->ecouteurs? 'ecouteurs réservés' : 'pas d\'écouteurs'; ?></td>
      <td></td>
    </tr>

    <tr>
      <td height="25"></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
    </tr>

    <tr>
      <td></td>
      <td colspan="4"><h1><?php echo $this->session_name;?></h1></td>
      <td></td>
    </tr>

    <tr>
      <td></td>
      <td colspan="4"><?php echo 'Le '.$this->date.' à '.$this->horaire; ?></td>
      <td></td>
    </tr>

    <tr>
      <td></td>
      <td></td>
      <td></td>
      <td colspan="2">&nbsp;</td>
      <td></td>
    </tr>
    <tr>
      <td></td>
      <td>Nom :</td>
      <td><?php echo $this->nom; ?></td>
      <td colspan="2" rowspan="4" align="center" valign="top"><p><?php echo $this->acces; ?></p>        <?php echo $QRcode; ?></td>
      <td></td>
    </tr>

    <tr>
      <td></td>
      <td>Pr&eacute;nom :</td>
      <td><?php echo $this->prenom; ?></td>
      <td></td>
    </tr>

    <tr>
      <td></td>
      <td>N&deg; :</td>
      <td><?php echo $this->presentUniqueID(); ?></td>
      <td></td>
    </tr>

    <tr>
      <td></td>
      <td>Statut :</td>
      <td><?php echo $this->statut; ?></td>
      <td></td>
    </tr>

    <tr>
      <td></td>
      <td>Organisateur :</td>
      <td><?php echo $this->organisateur;?></td>
      <td colspan="2" rowspan="5" align="center"><?php echo $barcode1D; ?></td>
      <td></td>
    </tr>

    <tr>
      <td></td>
      <td>Adresse :</td>
      <td><?php echo $this->lieu;?></td>
      <td></td>
    </tr>

    <tr>
      <td></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td></td>
    </tr>

    <tr>
      <td></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td></td>
    </tr>

    <tr>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
    </tr>

    <tr>
      <td height="25"></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
    </tr>

    <tr>
      <td height="100"></td>
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
      <td colspan="2"><a href="<?php echo $this->url_image;?>" target="_blank"><img src="<?php echo $this->imageBillet;?>" /></a></td>
      <td></td>
    </tr>

    <tr>
      <td></td>
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
      <td></td>
    </tr>
  </table>