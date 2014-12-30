# Multiple JCrop with Real-time Preview
Crop images in the front-end using JCrop and this JCrop Modification. With this modification, you'll be able to edit multiple images with JCrop, viewing the crop results in real-time.

Once you're done cropping, use PHP to save the images into a file (PHP class and code provided).

Demo: http://nilportugues.com/opensource/jquery-multiple-jcrop/

### 1. Description

* Multiple JCrop plugin allows you to edit multiple images using JCrop at the same time. 
* All configuration is done using HTML5 data-attributes and CSS Selectors.
* Easy to modify to fit your needs.

### 2. Dependencies
* JQuery (http://jquery.com)
* The original JCrop plugin (https://github.com/tapmodo/Jcrop)

### 3. Configuration
All you need to do to configure this JCrop modification is:

1.  Include **[JQuery](js/jquery.min.js)**, the original JCrop **[Javascript](js/jquery.Jcrop.js)** and **[CSS](css/jquery.Jcrop.css)** and the current plugin files.
```
<script src="js/jquery.min.js"></script>
<script src="js/jquery.Jcrop.js"></script>
<script src="js/jquery.Jcrop.multiple.js"></script>
<link href="css/jquery.Jcrop.css" type="text/css" rel="stylesheet" />
<link href="css/jquery.Jcrop.custom.css" type="text/css" rel="stylesheet"/>
```
2.  Edit the CSS in **[jquery.Jcrop.custom.css](css/jquery.Jcrop.custom.css)** to fit your needs.
3.  Edit the configuration array in **[jquery.Jcrop.multiple.js](js/jquery.Jcrop.multiple.js)** to match any changes in the CSS.
4.  Create a JCrop Item using HTML and the HTML5 data attributes.
```
<form method="post">
	<!-- JCROP ITEM # 1-->
	<div class="jcrop-item">
		<div>		
			<div class="jcrop-preview-pane" class="jcrop-transparent-bg">
				<div class="jcrop-preview-container">
					<img class="jcrop-preview" data-height="116" data-width="116" />
				</div>				
			</div>	
			<img class="jcrop-box" src="demo.png" data-height="300" data-width="300" data-x='7' data-y='0' data-x2='225' data-y2='225' />
		</div>	
		<input type="hidden" class="jcrop-src" name="jcrop-src[]" />
		<input type="hidden" class="jcrop-x" name="jcrop-x[]" />
		<input type="hidden" class="jcrop-y" name="jcrop-y[]" />
		<input type="hidden" class="jcrop-x2" name="jcrop-x2[]" />
		<input type="hidden" class="jcrop-y2" name="jcrop-y2[]" />
	</div>	

	<!-- JCROP ITEM # 2-->
	<!-- ... -->
	
	<div style="clear:left"></div>
	<input type="submit" value="Crop Images"/>
</form>	
```
5.  Hit the submit button and process the form's data server-side!


### 4. Data-Attributes
HTML5 data attributes play an important role in the configuration of this plugin. Two elements are targetted by the script to read the data attributes from.

#### .jcrop-preview
<table>
<thead>
  <tr>
		<th>data</th>
		<th>Use</th>
		<th>Description</th>
	</tr>
</thead>
<tbody>
	<tr>
		<td>data-height</td>
		<td>Mandatory</td>
		<td>Height value in pixels used to crop the image.</td>
	</tr>
	<tr>
		<td>data-width</td>
		<td>Mandatory</td>
		<td>Width value in pixels used to crop the image.</td>
	</tr>
</tbody>
</table>
#### .jcrop-box
<table>
<thead>
	<tr>
		<th>data</th>
		<th>Use</th>
		<th>Description</th>
	</tr>
</thead>
<tbody>
	<tr>
		<td>data-height</td>
		<td>Mandatory</td>
		<td>Height value in pixel used to place the image inside the DIV.</td>
	</tr>
	<tr>
		<td>data-width</td>
		<td>Mandatory</td>
		<td>Width value in pixel used to place the image inside the DIV.</td>
	</tr>
	<tr>
		<td>data-x</td>
		<td>Optional</td>
		<td>X Axis start value based on the selected image area. <br>
    Used to set or reload a stored area selection.</td>
	</tr>
	<tr>
		<td>data-y</td>
		<td>Optional</td>
		<td>Y Axis start value based on the selected image area. <br>
    Used to set or reload a stored area selection.</td>
	</tr>
	<tr>
		<td>data-x2</td>
		<td>Optional</td>
		<td>Value containing the width of the selected image area. <br>
    Used to set or reload a stored area selection.</td>
	</tr>
	<tr>
		<td>data-y2</td>
		<td>Optional</td>
		<td>Value containing the height of the selected image area.<br>
    Used to set or reload a stored area selection.</td>
	</tr>
</tbody>
</table>

### 5. Integration with KCFinder

To integrate JCrop with KCFinder, this plugin will load KCFinder using the textbox method (http://kcfinder.sunhater.com/demos/textbox).

Everything has been configurated for you. To get it working you must:

* Integrate KCFinder in your app (http://kcfinder.sunhater.com/docs/integrate).
* Create a `input[type=text]` element with the class attribute being `jcrop-image kcfinder-open`.
* In the very same text input element, add a `data-kcfinder` attribute pointing to the KCFinder location.

```
<input class="jcrop-image kcfinder-open" data-kcfinder='http://path/to/kcfinder/browse.php?type=images'>
```
