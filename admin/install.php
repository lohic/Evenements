<?php

// connection
include("connect.php");

// create albums///////////////////////////////////////////////////
$sql = "CREATE TABLE IF NOT EXISTS `albums` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";

mysql_query($sql)or die(mysql_error());



// create images ///////////////////////////////////////////////////
$sql = "CREATE TABLE IF NOT EXISTS `images` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `color` varchar(30) NOT NULL,
  `album_id` int(11) NOT NULL,
  `link` varchar(255) NOT NULL,
  `thumblink` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";

mysql_query($sql)or die(mysql_error());



// create users ///////////////////////////////////////////////////
$sql = "CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL auto_increment,
  `login` varchar(100) NOT NULL,
  `password` varchar(30) NOT NULL,
  `email` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";

mysql_query($sql)or die(mysql_error());




// add admin user ///////////////////////////////////////////////////
$sql = "INSERT INTO `users` (`id`, `login`, `password`, `email`) VALUES
(1, 'demo', 'demo', '');";

mysql_query($sql)or die(mysql_error());



// create config table ///////////////////////////////////////////////////
$sql = "CREATE TABLE IF NOT EXISTS `config` (
  `id` int(11) NOT NULL auto_increment,
  `tooltipText` varchar(30) NOT NULL,
  `tooltipBg` varchar(30) NOT NULL,
  `vel` varchar(30) NOT NULL,
  `menuMargins` varchar(30) NOT NULL,
  `fadeSpeed` varchar(30) NOT NULL,
  `zoomSize` varchar(30) NOT NULL,
  `thumbWidth` varchar(30) NOT NULL,
  `thumbHeight` varchar(30) NOT NULL,
  `thumbMargin` varchar(30) NOT NULL,
  `galleryMaskBorder` varchar(30) NOT NULL,
  `galleryMenuOver` varchar(30) NOT NULL,
  `showDescMenu` varchar(30) NOT NULL,
  `showPhotoNavigation` varchar(30) NOT NULL,
  `showCloseTag` varchar(30) NOT NULL,
  `galleryNumber` varchar(30) NOT NULL,
  `showGalleries` varchar(30) NOT NULL,
  `fullscreen` varchar(30) NOT NULL,
  `fixedWidth` varchar(30) NOT NULL,
  `fixedHeight` varchar(30) NOT NULL,
  `topLeftAlign` varchar(30) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;";

mysql_query($sql)or die(mysql_error());


// default config values ///////////////////////////////////////////////////
$sql = "INSERT INTO `config` (`id`, `tooltipText`, `tooltipBg`, `vel`, `menuMargins`, `fadeSpeed`, `zoomSize`, `thumbWidth`, `thumbHeight`, `thumbMargin`, `galleryMaskBorder`, `galleryMenuOver`, `showDescMenu`, `showPhotoNavigation`, `showCloseTag`, `galleryNumber`, `showGalleries`,  `fullscreen`,  `fixedWidth`,  `fixedHeight`,  `topLeftAlign`) VALUES
(1, '0x000000', '0xFFFFFF', '2', '70', '2', '120', '90', '90', '10', '10', '#000000', 'true', 'true', 'true', '1', 'true', 'true', '800', '600', 'false');";

mysql_query($sql)or die(mysql_error());



// redirect ///////////////////////////////////////////////////
header("Location:index.php");

?>