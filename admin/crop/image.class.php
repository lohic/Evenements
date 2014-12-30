<?php

/**
 * This is part of https://github.com/nilopc/NilPortugues_Javascript_Multiple_JCrop
 *
 * (c) 2013 Nil Portugués Calderó <contact@nilportugues.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class imageClass
{
    protected $filetype;
    protected $filename;
    protected $width;
    protected $height;
    protected $original_image;
    protected $current_image;
    protected $optimal_width;
    protected $optimal_height;

    /**
     * Constructor. Checks if GD lib is available.
     */
    public function __construct() {
        if (!function_exists('gd_info')) {
            throw new \Exception('GD library is not installed');
        }
    }

    /**
     * Image setter.
     *
     * @param $filename
     * @return imageClass
     */
    public function setImage($filename) {

        if($this->isImage($filename)){
            $this->filename = $filename;
            $this->original_image = $this->imageCreate($filename);
            $this->current_image = NULL;

        } else {
            throw new \Exception('File '.$filename.' is not a supported file type. JPEG/PNG/GIF only.');
        }
        return $this;
    }

	public function getFileType()
	{
		return $this->filetype;
	}

    /**
     * @return int
     */
    public function getWidth(){
        $image = $this->getWorkingResource();
        return imagesx($image);
    }

    /**
     * @return int
     */
    public function getHeight(){
        $image = $this->getWorkingResource();
        return imagesy($image);
    }

    /**
     * Sets image working resource.
     * @return mixed
     */
    protected function getWorkingResource(){
        if(!isset($this->current_image)){
            $this->current_image = $this->original_image;
        }
        return $this->current_image;
    }

    /**
     * Image resize function. Resize options depends on $option's value {exact,portrait,landscape,crop,auto}.
     * Default resize method is auto (autoscale).
     *
     * @param $newWidth
     * @param $newHeight
     * @param string $option
     * @return imageClass
     */
    public function resize($newWidth, $newHeight, $option="auto")
    {
        $image = $this->getWorkingResource();
        $this->width  = imagesx($image);
        $this->height = imagesy($image);

        // Get optimal width and height - based on $option
        $optionArray = $this->getDimensions($newWidth, $newHeight, $option);
        $optimal_width  = $optionArray['optimal_width'];
        $optimal_height = $optionArray['optimal_height'];

        $this->optimal_width = $optimal_width;
        $this->optimal_height = $optimal_height;

        // create image canvas of x, y size
        $this->current_image = imagecreatetruecolor($optimal_width, $optimal_height);

        if($this->filetype=='png' || $this->filetype=='gif'){
            imagealphablending($this->current_image, false);
            imagesavealpha($this->current_image,true);
            $transparent = imagecolorallocatealpha($this->current_image, 255, 255, 255, 127);
            imagefilledrectangle($this->current_image, 0, 0, $newWidth,$newHeight, $transparent);
        }
        imagecopyresampled($this->current_image, $image, 0, 0, 0, 0, $optimal_width, $optimal_height, $this->width, $this->height);

        // if option is 'crop', then crop too
        if ($option == 'crop') {

            $this->cropResize($optimal_width, $optimal_height, $newWidth, $newHeight);

        }
        return $this;
    }


    /**
     * Return TRUE if the file passed is an image, FALSE otherwise.
     *
     * @param $filename
     * @return bool
     */
    protected function isImage($filename) {

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $filename);
        finfo_close($finfo);

        switch($mime_type) {
            case 'image/jpg':
            case 'image/jpeg':
                $this->filetype='jpg';
                return true;
                break;

            case 'image/png':
                $this->filetype='png';
                return true;
                break;

            case 'image/gif':
                $this->filetype='gif';
                return true;
                break;

            default:
                return false;
                break;
        }
    }

    /**
     * Reads the image's color palette and returns an array with HEX code for them.
     * $numColors determines the number of colors and total number of results.
     *
     * @param int $numColors
     * @return array
     * @throws \Exception
     */
    public function getPalette($numColors) {

        $numColors = (int) $numColors;
        $this->original_image = $this->imageCreate($this->filename);
        $this->width  = imagesx($this->original_image);
        $this->height = imagesy($this->original_image);

        //read all the image colour palette
        $colors = array();

        for($x = 0; $x < $this->width; $x += 1) {
            for($y = 0; $y < $this->height; $y += 1) {

                $thisColor = imagecolorat($this->original_image, $x, $y);
                $rgb = imagecolorsforindex($this->original_image, $thisColor);

                $red = round(round(($rgb['red'] / 0x33)) * 0x33);
                $green = round(round(($rgb['green'] / 0x33)) * 0x33);
                $blue = round(round(($rgb['blue'] / 0x33)) * 0x33);

                $thisRGB = sprintf('%02X%02X%02X', $red, $green, $blue);

                if(array_key_exists($thisRGB, $colors)) {
                    $colors[$thisRGB]++;
                } else {
                    $colors[$thisRGB] = 1;
                }
            }
        }
        arsort($colors);
        return array_slice(array_keys($colors), 0, $numColors);
    }


    /**
     * Returns an image resource for PNG,JPG or GIF image files.
     *
     * @param $filename
     * @return bool|resource
     */
    protected function imageCreate($filename) {

        $img = false;
        switch($this->filetype) {
            case 'gif':
                $img = imagecreatefromgif($filename);
                break;
            case 'jpg':
                $img = imagecreatefromjpeg($filename);
                break;
            case 'png':
                $img = imagecreatefrompng($filename);
                break;
        }
        return $img;
    }

    /**
     * Does calculations for the new image dimensions.
     * Calculation method depends on $option's value {exact,portrait,landscape,auto}.
     *
     * @param $newWidth
     * @param $newHeight
     * @param $option
     * @return array
     */
    protected function getDimensions($newWidth, $newHeight, $option)
    {
        switch (strtolower($option)) {
            case 'exact':
                $optimal_width  = $newWidth;
                $optimal_height = $newHeight;
                break;

            case 'portrait':
                $optimal_width = $this->getSizeByFixedHeight($newHeight);
                $optimal_height = $newHeight;
                break;

            case 'landscape':
                $optimal_width = $newWidth;
                $optimal_height = $this->getSizeByFixedWidth($newWidth);
                break;

            case 'crop':
                $optionArray = $this->getOptimalCrop($newWidth, $newHeight);
                $optimal_width = $optionArray['optimal_width'];
                $optimal_height = $optionArray['optimal_height'];
                break;

            case 'auto':
            default:
                $optionArray = $this->getSizeByAuto($newWidth, $newHeight);
                $optimal_width = $optionArray['optimal_width'];
                $optimal_height = $optionArray['optimal_height'];
                break;

        }
        return array('optimal_width' => $optimal_width, 'optimal_height' => $optimal_height);
    }

    /**
     * * Calculates a new width based on the height's value while keeping the image proportion.
     *
     * @param $newHeight
     * @return mixed
     */
    protected function getSizeByFixedHeight($newHeight)
    {
        $ratio = $this->width / $this->height;
        $newWidth = $newHeight * $ratio;
        return $newWidth;
    }

    /**
     * Calculates a new height based on the width's value while keeping the image proportion.
     *
     * @param $newWidth
     * @return mixed
     */
    protected function getSizeByFixedWidth($newWidth)
    {
        $ratio = $this->height / $this->width;
        $newHeight = $newWidth * $ratio;
        return $newHeight;
    }

    /**
     * Image new width and height calculation function.
     *
     * @param $newWidth
     * @param $newHeight
     * @return array
     */
    protected function getSizeByAuto($newWidth, $newHeight) {
        if ($this->height < $this->width) {

            // Image to be resized is wider (landscape)
            $optimal_width = $newWidth;
            $optimal_height= $this->getSizeByFixedWidth($newWidth);

        } elseif ($this->height > $this->width) {

            // Image to be resized is taller (portrait)
            $optimal_width = $this->getSizeByFixedHeight($newHeight);
            $optimal_height= $newHeight;

        } else {

            // Image to be resized is a square
            if ($newHeight < $newWidth) {
                $optimal_width = $newWidth;
                $optimal_height= $this->getSizeByFixedWidth($newWidth);
            } else if ($newHeight > $newWidth) {
                $optimal_width = $this->getSizeByFixedHeight($newHeight);
                $optimal_height= $newHeight;
            } else {
                // Square being resized to a square
                $optimal_width = $newWidth;
                $optimal_height= $newHeight;
            }
        }
        return array('optimal_width' => $optimal_width, 'optimal_height' => $optimal_height);
    }

    /**
     * @param $newWidth
     * @param $newHeight
     * @return array
     */
    protected function getOptimalCrop($newWidth, $newHeight)
    {
        $heightRatio = $this->height / $newHeight;
        $widthRatio  = $this->width /  $newWidth;

        if ($heightRatio < $widthRatio) {
            $optimalRatio = $heightRatio;
        } else {
            $optimalRatio = $widthRatio;
        }

        $optimal_height = $this->height / $optimalRatio;
        $optimal_width  = $this->width  / $optimalRatio;

        return array('optimal_width' => $optimal_width, 'optimal_height' => $optimal_height);
    }


    /**
     * Crops the image to the specified size, counting from the center to the borders.
     *
     * @param $optimal_width
     * @param $optimal_height
     * @param $newWidth
     * @param $newHeight
     * @return imageClass
     */
    protected function cropResize($optimal_width, $optimal_height, $newWidth, $newHeight)
    {
        // Find center - this will be used for the crop
        $cropStartX = ( $optimal_width / 2) - ( $newWidth /2 );
        $cropStartY = ( $optimal_height/ 2) - ( $newHeight/2 );

        $crop = $this->current_image;

        // Now crop from center to exact requested size
        $this->current_image = imagecreatetruecolor($newWidth , $newHeight);

        //Keep transparency
        if($this->filetype=='png' || $this->filetype=='gif'){
            imagealphablending($this->current_image, false);
            imagesavealpha($this->current_image,true);
            $transparent = imagecolorallocatealpha($this->current_image, 255, 255, 255, 127);
            imagefilledrectangle($this->current_image, 0, 0, $newWidth,$newHeight, $transparent);
        }

        imagecopyresampled($this->current_image, $crop , 0, 0, $cropStartX, $cropStartY, $newWidth, $newHeight , $newWidth, $newHeight);

        return $this;
    }

    /**
     * Crops the image using the specified points and distances.
     *
     * @param $cropStartX
     * @param $cropStartY
     * @param $newWidth
     * @param $newHeight
     * @return imageClass
     */
    public function crop($cropStartX, $cropStartY, $newWidth, $newHeight)
    {
         $crop = $this->getWorkingResource();

        // Now crop from center to exact requested size
        $this->current_image = imagecreatetruecolor($newWidth , $newHeight);

        //Keep transparency
        if($this->filetype=='png' || $this->filetype=='gif'){
            imagealphablending($this->current_image, false);
            imagesavealpha($this->current_image,true);
            $transparent = imagecolorallocatealpha($this->current_image, 255, 255, 255, 127);
            imagefilledrectangle($this->current_image, 0, 0, $newWidth,$newHeight, $transparent);
        }

        imagecopyresampled($this->current_image, $crop , 0, 0, $cropStartX, $cropStartY, $newWidth, $newHeight , $newWidth, $newHeight);

        return $this;
    }

    /**
     * Replaces background color to white or transparent.
     *
     * @param $image
     * @param $color
     * @return resource
     */
    protected function backgroundColor(&$image,$color) {
        // Create a new true color image with the same size
        $w = imagesx($image);
        $h = imagesy($image);

        switch($color){
            case 'transparent':
                imagecolortransparent($image, imagecolorallocatealpha($image, 0, 0, 0, 127));
                imagealphablending($image, false);
                imagesavealpha($image, true);
            break;

            case 'white':
            default:
                $background = imagecreatetruecolor($w, $h);
                $background_color = imagecolorallocate($background, 255, 255, 255);
                imagefill($background, 0, 0, $background_color);
                imagecopy($background, $image, 0, 0, 0, 0, $w, $h);
                $image = $background;
            break;
        }
    }


    /**
     * The class' image saving function.
     *
     * @param $savePath
     * @param $filename
     * @param $extension
     * @param int|string $imageQuality
     * @return bool|string
     */
    public function save($savePath,$filename,$extension,$imageQuality=100)
    {
        $this->current_image = $this->getWorkingResource();

        switch(strtolower($extension))
        {
            case 'jpg':
            case 'jpeg':
                $path = $savePath.'/'.$filename.'.jpg';
                $this->backgroundColor($this->current_image,'white');

                if( !imagejpeg($this->current_image, $path, $imageQuality) ){
                    return false;
                }
                break;

            case 'gif':
                $path = $savePath.'/'.$filename.'.gif';
                $this->backgroundColor($this->current_image,'transparent');

                if( !imagegif($this->current_image,$path) ){
                    return false;
                }

                break;

            case 'png':
                $path = $savePath.'/'.$filename.'.png';
                $this->backgroundColor($this->current_image,'transparent');

                if( !imagepng($this->current_image,$path,9,PNG_ALL_FILTERS) ){
                    return false;
                }

                break;

            default:
                return false;
        }
        //destroy image resource
        imagedestroy($this->current_image);
        return $path;
    }


    /**
     * Allows an image to be converted into another format.
     *
     * @param $new_format
     * @return imageClass
     */
    public function changeFormat($new_format){

        $current_format = substr($this->filename,-3);

        $remain_untouch_jpg = ( (($new_format=='jpg' || $new_format=='jpeg') && $new_format==$current_format));
        $remain_untouch_gif = ( ($new_format=='gif' && $new_format==$current_format) && $this->isAnimatedGIF($this->filename)==true );

        if ($remain_untouch_jpg==false && $remain_untouch_gif==false) {

            $this->current_image = $this->getWorkingResource();
            $width  = imagesx($this->current_image);
            $height = imagesy($this->current_image);

            //does the actual conversion
            $this->resize($width,$height,'exact');
        }
        return $this;
    }


    /**
     * Thanks to ZeBadger for original example, and Davide Gualano for pointing me to it
     * Original at http://it.php.net/manual/en/function.imagecreatefromgif.php#59787
     *
     * @param $filename
     * @return bool
     */
    protected function isAnimatedGIF( $filename )
    {
        $raw = file_get_contents( $filename );

        $offset = 0;
        $frames = 0;
        while ($frames < 2)
        {
            $where1 = strpos($raw, "\x00\x21\xF9\x04", $offset);
            if ( $where1 === false ) {
                break;
            } else  {
                $offset = $where1 + 1;
                $where2 = strpos( $raw, "\x00\x2C", $offset );

                if ( $where2 === false )  {
                    break;
                } else  {
                    if ( $where1 + 8 == $where2 ) {
                        $frames ++;
                    }
                    $offset = $where2 + 1;
                }
            }
        }
        return $frames > 1;
    }

    /**
     * Does proper imagecopymerge with Alpha support for PNGs
     *
     * Source: http://www.php.net/manual/en/function.imagecopymerge.php#88456
     * @param $dst_im
     * @param $src_im
     * @param $dst_x
     * @param $dst_y
     * @param $src_x
     * @param $src_y
     * @param $src_w
     * @param $src_h
     * @param $pct
     */
    protected function imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct){
        if(!isset($pct)){
            return;
        }
        $pct /= 100;
        // Get image width and height
        $w = imagesx( $src_im );
        $h = imagesy( $src_im );
        // Turn alpha blending off
        imagealphablending( $src_im, false );
        // Find the most opaque pixel in the image (the one with the smallest alpha value)
        $minalpha = 127;
        for( $x = 0; $x < $w; $x++ )
            for( $y = 0; $y < $h; $y++ ){
                $alpha = ( imagecolorat( $src_im, $x, $y ) >> 24 ) & 0xFF;
                if( $alpha < $minalpha ){
                    $minalpha = $alpha;
                }
            }
        //loop through image pixels and modify alpha for each
        for( $x = 0; $x < $w; $x++ ){
            for( $y = 0; $y < $h; $y++ ){
                //get current alpha value (represents the TANSPARENCY!)
                $colorxy = imagecolorat( $src_im, $x, $y );
                $alpha = ( $colorxy >> 24 ) & 0xFF;
                //calculate new alpha
                if( $minalpha !== 127 ){
                    $alpha = 127 + 127 * $pct * ( $alpha - 127 ) / ( 127 - $minalpha );
                } else {
                    $alpha += 127 * $pct;
                }
                //get the color index with new alpha
                $alphacolorxy = imagecolorallocatealpha( $src_im, ( $colorxy >> 16 ) & 0xFF, ( $colorxy >> 8 ) & 0xFF, $colorxy & 0xFF, $alpha );
                //set pixel with the new color + opacity
                if( !imagesetpixel( $src_im, $x, $y, $alphacolorxy ) ){
                    return;
                }
            }
        }
        // The image copy
        imagecopy($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h);
    }


    /**
     * Function that puts a watermark on any image. Rescales watermark to fit into the image.
     *
     * @param $filename
     * @param $opacity
     * @param int $margin
     * @param $x_position
     * @param $y_position
     * @param int $ratio_correction Allows correction on the auto-scaling of the watermark, in case it's needed.
     * @return imageClass
     */
    public function watermark($filename,$opacity,$margin=0,$x_position='',$y_position='',$ratio_correction=1) {

        $this->current_image = $this->getWorkingResource();

        //Image size
        $source_width = imagesx($this->current_image);
        $source_height = imagesy($this->current_image);

        //Watermark resource and size
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $filename);
        finfo_close($finfo);

        switch($mime_type) {
            case 'image/jpg':
            case 'image/jpeg':
                $watermark = imagecreatefromjpeg($filename);
                break;

            case 'image/gif':
                $watermark = imagecreatefromgif($filename);
                break;

            case 'image/png':
                $watermark = imagecreatefrompng($filename);
                break;

            default:
                $watermark = NULL;
                break;
        }

        $watermark_width = imagesx($watermark);
        $watermark_height = imagesy($watermark);

        //Watermark ratio
        $ratio = ($watermark_width/$source_width);
        if($ratio>1){
            $ratio = ($source_width/$watermark_width);
        }
        $ratio = $ratio * $ratio_correction;

        //Resize watermark to image ratio
        $new_watermark = imagecreatetruecolor($watermark_width,$watermark_height);
        imagealphablending($new_watermark, false);
        imagesavealpha($new_watermark,true);
        imagecopyresampled($new_watermark,$watermark, 0, 0, 0, 0, $watermark_width*$ratio, $watermark_height*$ratio, $watermark_width, $watermark_height);

        $watermark = $new_watermark;
        $watermark_width = $watermark_width*$ratio;
        $watermark_height = $watermark_height*$ratio;

        //WATERMARKING ALGORITHMS
        if($x_position=='tiled'){

            imagealphablending($watermark, false);
            imagesavealpha($watermark,true);

            for($i=0;$i<=$source_width;$i=$i+$watermark_width){
                for($j=0;$j<=$source_height;$j=$j+$watermark_height){

                    $this->imagecopymerge_alpha($this->current_image,$watermark, $i+1+$margin, $j+1+$margin, 0, 0, $watermark_width, $watermark_height,$opacity);
                }
            }

        } else {

            //Place watermark on image
            $pos = $x_position.'-'.$y_position;
            $dest_x = $margin;
            $dest_y = $margin;

            switch($pos) {

                case 'top-left':
                    break;

                case "top-right":
                    $dest_x = $source_width - $watermark_width - $margin;     //right
                    break;

                case 'top-center':
                    $dest_x = ($source_width/2) - ($watermark_width/2);
                    break;

                case "bottom-left":
                    $dest_y = $source_height - $watermark_height - $margin;   //bottom
                    break;

                case "bottom-right":
                    $dest_x = $source_width - $watermark_width - $margin;     //right
                    $dest_y = $source_height - $watermark_height - $margin;   //bottom
                    break;

                case 'bottom-center':
                    $dest_x = ($source_width/2) - ($watermark_width/2);
                    $dest_y = $source_height - $watermark_height - $margin;   //bottom
                    break;

                case 'center-center':
                    $dest_x = ($source_width/2) - ($watermark_width/2);
                    $dest_y = ($source_height/2) - ($watermark_height/2);
                    break;

                case 'center-left':
                    $dest_y = ($source_height/2) - ($watermark_height/2);
                    break;

                case 'center-right':
                    $dest_x = $source_width - $watermark_width - $margin;     //right
                    $dest_y = ($source_height/2) - ($watermark_height/2);
                    break;

                default:
                    throw new \Exception('Watermark option does not exist.');
                    break;
            }
            //SAVE WATERMARK
            $this->imagecopymerge_alpha($this->current_image,$watermark, $dest_x, $dest_y, 0, 0, $watermark_width, $watermark_height,$opacity);
        }

        return $this;
    }


    /**
     * @param $degrees
     * @return imageClass
     */
    public function rotate($degrees){

        $this->current_image=$this->getWorkingResource();

        if($degrees<0){
            $degrees = 360 + $degrees;
        }

        $this->current_image = imagerotate($this->current_image, $degrees, -1);

        if($this->filetype=='png' || $this->filetype=='gif'){
            $this->backgroundColor($this->current_image,'transparent');
        } else {
            $this->backgroundColor($this->current_image,'white');
        }

        return $this;
    }

    /**
     * @param string $action
     * @return imageClass
     */
    public function flip($action='horizontal'){

        $this->current_image=$this->getWorkingResource();

        //Image size
        $source_width = imagesx($this->current_image);
        $source_height = imagesy($this->current_image);

        //create the empty destination image
        $flipped = imagecreatetruecolor($source_width, $source_height);
        imagealphablending($flipped, false);
        imagesavealpha($flipped, true);

        switch($action){

            case 'vertical':
                for($i = 0; $i < $source_height; $i++) {
                    imagecopy($flipped, $this->current_image, 0, ($source_height - $i - 1), 0, $i, $source_width, 1);
                }
                break;

            case 'horizontal':
            default :
                for($i = 0; $i < $source_width; $i++) {
                    imagecopy($flipped, $this->current_image, ($source_width - $i - 1), 0, $i, 0, 1, $source_height);
                }
                break;

            case 'both' :
                    //vertical
                    for($i = 0; $i < $source_height; $i++) {
                        imagecopy($flipped, $this->current_image, 0, ($source_height - $i - 1), 0, $i, $source_width, 1);
                    }
                    //..and horizontal
                    $source_width = imagesx($flipped);
                    $source_height = imagesy($flipped);

                    $flipped2 = imagecreatetruecolor($source_width, $source_height);
                    imagealphablending($flipped2, false);
                    imagesavealpha($flipped2, true);

                    for($i = 0; $i < $source_width; $i++) {
                        imagecopy($flipped2, $flipped, ($source_width - $i - 1), 0, $i, 0, 1, $source_height);
                    }
                    $flipped = $flipped2;
                    break;
        }

        $this->current_image = $flipped;
        return $this;
    }


    /**
     * Allows usage of all the GD image filters and custom filters.
     * The GD native filters use the expected parameters values.
     *
     * @param $name
     * @param null $arg1
     * @param null $arg2
     * @param null $arg3
     * @param null $arg4
     * @return imageClass
     * @throws \Exception
     */
    public function effect($name,$arg1=NULL,$arg2=NULL,$arg3=NULL,$arg4=NULL){

        $this->current_image=$this->getWorkingResource();

        switch($name){
            case 'sharpen':
                    if( $arg1 && is_numeric($arg1)){
                        $this->effectSharpen($this->current_image,$arg1);
                    } else {
                        throw new \Exception('Use arg1 to set the level of sharpness. $arg1 positive value sharpens, negative blurs/unsharpens.');
                    }
                    break;

            case 'sepia':
                    $this->effectSepia($this->current_image);
                    break;
            case 'bw':
                    imagefilter($this->current_image, IMG_FILTER_GRAYSCALE); //first, convert to grayscale
                    imagefilter($this->current_image, IMG_FILTER_CONTRAST, -255); //then, apply a full contrast
                    imagefilter($this->current_image, IMG_FILTER_GRAYSCALE); //just make sure contrast didn't add extra colours
                    break;

            case 'greyscale':
            case 'grayscale':
                    imagefilter($this->current_image, IMG_FILTER_GRAYSCALE);
                    break;

            case 'negative':
                    imagefilter($this->current_image, IMG_FILTER_NEGATE);
                    break;

            case 'brightness':

                    if( $arg1 && ($arg1>=-255 && $arg1<=255)){
                        imagefilter($this->current_image, IMG_FILTER_BRIGHTNESS,$arg1);
                    } else {
                        throw new \Exception('Use arg1 to set the level of brightness. $arg1 value ranges from -255 to 255.');
                    }
                    break;

            case 'contrast':
                    if( $arg1 && ($arg1>=-255 && $arg1<=255)){
                        imagefilter($this->current_image, IMG_FILTER_CONTRAST,$arg1);
                    } else {
                        throw new \Exception('Use arg1 to set the level of contrast. $arg1 value ranges from -255 to 255.');
                    }
                    break;

            case 'colorize':
                    if( $arg1 && $arg2 && $arg3 && $arg4 && ($arg1>=0 && $arg1<=255)  && ($arg2>=0 && $arg2<=255) && ($arg3>=0 && $arg3<=255) && ($arg4>=0 && $arg4<=100)){
                        imagefilter($this->current_image, IMG_FILTER_COLORIZE,$arg1,$arg2,$arg3,$arg4);
                    } else {
                        throw new \Exception('Use arg1, arg2 & arg3 in the form of red, blue, green and arg4 for the alpha channel. The range for each color is 0 to 255, alpha is from 0 to 100.');
                    }
                    break;

            case 'edge-detect':
                    imagefilter($this->current_image, IMG_FILTER_EDGEDETECT);
                    break;

            case 'emboss':
                    imagefilter($this->current_image, IMG_FILTER_EMBOSS);
                    break;

            case 'gaussian-blur':
                    imagefilter($this->current_image, IMG_FILTER_GAUSSIAN_BLUR);
                    break;

            case 'blur':
                    imagefilter($this->current_image, IMG_FILTER_SELECTIVE_BLUR);
                    break;

            case 'mean-removal':
                    imagefilter($this->current_image, IMG_FILTER_MEAN_REMOVAL);
                    break;

            case 'smooth':
                    if( $arg1 && ($arg1>=0 && $arg1<=100)){
                        imagefilter($this->current_image, IMG_FILTER_SMOOTH,$arg1);
                    } else {
                        throw new \Exception('Use arg1 to set the level of smoothing. $arg1 value ranges from 0 to 100.');
                    }
                    break;

            case 'pixelate':
                    if( $arg1 && $arg1>0){
                        if($arg2!==true){
                            $arg2 = false;
                        }
                        imagefilter($this->current_image, IMG_FILTER_PIXELATE,$arg1,$arg2);
                    } else {
                        throw new \Exception('Use arg1 to set the level of pixelation. $arg1 sets the pixel size, $arg2 enables advance pixel filter, values being boolean values: true/false.');
                    }
                    break;

            default:
                    break;
        }

        if($this->filetype=='png' || $this->filetype=='gif'){
            $this->backgroundColor($this->current_image,'transparent');
        }
        return $this;
    }


    /**
     * Does the sepia image effect
     *
     * @param $image
     */
    protected function effectSepia(&$image){

        $width = imagesx($image);
        $height = imagesy($image);
        for($_x = 0; $_x < $width; $_x++){
            for($_y = 0; $_y < $height; $_y++){
                $rgb = imagecolorat($image, $_x, $_y);
                $rgba = imagecolorsforindex($image, imagecolorat($image, $_x, $_y));

                if($rgba['alpha']<120){ //Only recolour to sepia non-transparent pixels
                    $r = ($rgb>>16)&0xFF;
                    $g = ($rgb>>8)&0xFF;
                    $b = $rgb&0xFF;

                    $y = $r*0.299 + $g*0.587 + $b*0.114;
                    $i = 0.15*0xFF;
                    $q = -0.001*0xFF;

                    $r = $y + 0.956*$i + 0.621*$q;
                    $g = $y - 0.272*$i - 0.647*$q;
                    $b = $y - 1.105*$i + 1.702*$q;

                    if($r<0||$r>0xFF){$r=($r<0)?0:0xFF;}
                    if($g<0||$g>0xFF){$g=($g<0)?0:0xFF;}
                    if($b<0||$b>0xFF){$b=($b<0)?0:0xFF;}

                    $color = imagecolorallocate($image, $r, $g, $b);
                    imagesetpixel($image, $_x, $_y, $color);
                }
            }
        }
        return $image;
    }

    /**
     * Does image sharping or unsharping.
     *
     * Source: http://duthler.net/2011/06/23/gd-wrapper-for-php-part-3-sharpen-blur/
     * @param $factor
     */
    protected function effectSharpen(&$image, $factor )
    {
        if( $factor == 0 ) return;

        // get a value thats equal to 64 - abs( factor ).( using min/max to limited the factor to 0 - 64 to not get out of range values )
        $val1Adjustment = 64 - min( 64, max( 0, abs( $factor ) ) );

        // the base factor for the "current" pixel depends on if we are blurring or sharpening. If we are blurring use 1, if sharpening use 9.
        $val1Base = ( abs( $factor ) != $factor ) ? 1 : 9;

        // value for the center/currrent pixel is:
        //  1 + 0 - max blurring, 1 + 64- minimal blurring, 9 + 64- minimal sharpening, 9 + 0 - maximum sharpening
        $val1 = $val1Base + $val1Adjustment;

        // the value for the surrounding pixels is either positive or negative depending on if we are blurring or sharpening.
        $val2 = ( abs( $factor ) != $factor ) ? 1 : -1;

        // get source values

        $widthSrc = imagesx( $image );
        $heightSrc = imagesy( $image );

        // setup matrix ..
        $matrix = array(
            array( $val2, $val2, $val2 ),
            array( $val2, $val1, $val2 ),
            array( $val2, $val2, $val2 )
        );

        // calculate the correct divisor
        // actual divisor is equal to "$divisor = $val1 + $val2 * 8;"
        // but the following line is more generic
        $divisor = array_sum( array_map( 'array_sum', $matrix ) );

        // apply the matrix
        imageconvolution( $image, $matrix, $divisor, 0 );
    }

}


