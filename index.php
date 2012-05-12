<?php
# Config
# ------------------------------------------------
$allowed_filetypes = Array('jpg','png','bmp','gif');
#---------------------------------------------------
?>
<!DOCTYPE html>
<html>
<head>
	<title>Noda - <?php print basename(getcwd()); ?></title>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
	<script type="text/javascript" src="http://code.jquery.com/jquery-1.7.2.min.js"></script>
	<script type="text/javascript">
	$(document).ready(function() {
		$(".thumb").live("click", function() {
			var image = $(this);
			if(view.visible) {
				view.hide(1000, function() {
					view.open(1000,image);	
				});
			} else {
				view.open(1000, image);	
			}	
		});
		$(".thumb").hover(function() {
			if(!view.visible) {
				$(this).animate({
					opacity: 1
				},500);
			}	
		}, function() {
			$(this).animate({
				opacity: 0.5
			},500);		
		});	
		$(document).keydown(function(e) {
			switch(e.keyCode) {
				case 38: // up
				if(!view.visible) {
					view.open(1000, $("img.thumb:first"));	
				}	
				break;	
				case 40: // down
				if(view.visible) {
					view.hide(1000);	
				}	
				break;											
				case 37: // Backwards
				if(view.visible) {
					view.hide(500, function() {
						if(view.image.index() > 0)
							view.open(500, view.image.prev());
						else 
							view.open(500, $("img.thumb:last"));
					});
				}
				break;				
				case 39: // Forwards
				if(view.visible) {
					view.hide(500, function() {
						if(view.image.next().index() < $(".thumb").length)
							view.open(500, view.image.next());	
						else
							view.open(500, $("img.thumb:first"));
					});
				}
				break;
			}
		});
	});
	
	var view = {
		visible: false,
		open: function(speed,element) {
			view.visible = true;
			view.image = element;
			view.image.addClass("selected");

			var preload = new Image();
			preload.src = element.attr("data-filename");				
			preload.onload = function() {
				$(".view").append('<a href="' + preload.src + '" target="_blank"><img src="' + preload.src + '" alt="" /></a>');	
				$(".view").animate({
					height: $(".view img").height()
				}, speed, function() {
					
				});				
			}
		},
		hide: function(speed, callback) {
			if(callback == undefined)
				callback = function(){};
			else
				callback = callback;

			$(".view").animate({
				height: 0
			}, speed, function() {
				$(this).empty();
				view.visible = false;
				$(".selected").removeClass("selected");
				callback();
			});
		}
	}		
	</script>
	<style type="text/css">
	body {
		background: #202020;
		font: 13px "Helvetica Neue",Helvetica,Arial,sans-serif;
	}
	.thumb {
		-ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=50)";
		filter: alpha(opacity=50);
		-moz-opacity: 0.5;
		-khtml-opacity: 0.5;			
		opacity: 0.5;
		border: 2px solid #CCC;
		margin: 2px;
	}
	.thumb.selected {
		border: 2px solid #FF8904;
		opacity: 1;
	}
	div.view {
		position: fixed;
		bottom: 0;
		left: 0;
		right: 0;
		height: 0;
		padding: 10px 0 40px 0;
		background: #171717;
		-moz-box-shadow: 0px -1px 10px #000;
		-webkit-box-shadow: 0px -1px 10px #000;
		box-shadow: 0px -1px 10px #000;
		text-align: center;
	}
	div.view img {
		max-width: 800px;
		max-height: 600px;
	} 
	div.view .top {
		height: 20px;
		color: #fff;
		padding: 5px;
		font-weight: bold;
	}
	div.view .close {
		color: #fff;
		cursor: pointer;
		font-weight: bold;
		float: right;
		margin-left: 20px;
	} 	

	div.view .next, div.view .previous {	
		width: 0;
		height: 0;
		top: 50%;
		margin-top: -30px;
		position: absolute;
		cursor: pointer;						
	}	
	div.view .next {
		right: 10px;
		border-top: 30px solid transparent;
		border-bottom: 30px solid transparent;
		border-left: 30px solid #fff;			
	} 	
	div.view .previous {
		left: 10px;
		border-top: 30px solid transparent;
		border-bottom: 30px solid transparent; 
		border-right:30px solid #fff;
	} 	
	div.notice {
		width: 360px;
		height: 60px;
		padding: 20px;
		position: absolute;
		top: 50%;
		left: 50%;
		margin-top: -50px;
		margin-left: -200px;
		background: #fff;
		-webkit-border-radius: 8px;
		-moz-border-radius: 8px;
		border-radius: 8px;		
		-moz-box-shadow: inset 0px 0px 10px #4A4A4A;
		-webkit-box-shadow: inset 0px 0px 10px #4A4A4A;
		box-shadow: inset 0px 0px 10px #4A4A4A;				
	}	
	div.footer {
		height: 30px;
		position: fixed;
		bottom: 0;
		left: 0;
		right: 0;
		line-height: 30px;
		color: #fff;
		background: #121212;
		-moz-box-shadow: 0px -1px 10px #000;
		-webkit-box-shadow: 0px -1px 10px #000;
		box-shadow: 0px -1px 10px #000;	
		text-align: center;		
	}
	div.notification a, div.footer a {
		color: #337372;
		text-decoration: none;
	}
	</style>
</head>
<body>
<?php
if(class_exists('Imagick')) {
	foreach (glob("*.*") as $filename) {
		if(!file_exists('thumb')) {
			mkdir('thumb');
		}
		if(!file_exists('thumb/' . $filename) && in_array(end(explode('.', $filename)), $allowed_filetypes)) {

			$imagick = new Imagick($filename);
			$imagick->thumbnailImage(null, 100);
			$imagick->cropThumbnailImage(100, 100);
			$imagick->writeImage('thumb/' . $filename);
			$imagick->clear();
			$imagick->destroy();
			print '<img src="thumb/' . urlencode($filename) . '" alt="" class="thumb" data-filename="' . urlencode($filename) . '" />';
		} elseif(in_array(end(explode('.', $filename)), $allowed_filetypes)) {
			print '<img src="thumb/' . urlencode($filename) . '" alt="" class="thumb" data-filename="' . urlencode($filename) . '" />';
		}
	}
	foreach (glob("thumb/*.*") as $filename) {
		if(!file_exists(basename($filename))) {
			unlink($filename);
		}
	}
} else {
	print '
	<div class="notice">
		<a href="http://www.imagemagick.org">Imagick</a> is not currently installed on your server.<br />
		Noda requires <a href="http://www.imagemagick.org">Imagick</a> for it to work properly.
	</div>';
}
?>
	<div class="view"></div>
	<div class="footer">
		<a href="https://github.com/rikukissa/Noda">Noda</a> image gallery by <a href="http://rikurouvila.fi">Riku Rouvila</a>. Use arrow keys to navigate.
	</div>
</body>
</html>