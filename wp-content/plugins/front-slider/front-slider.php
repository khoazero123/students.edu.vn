<?php
$direct_path =  get_bloginfo('wpurl')."/wp-content/plugins/front-slider";
?>
<style>

#slide-wrapper {
background: #<?php if ( get_option('front_sl_bg_color')) {echo get_option('front_sl_bg_color');} else {echo 'EEE';} ?>;
border: 1px solid #<?php if ( get_option('front_sl_border_color')) {echo get_option('front_sl_border_color');} else {echo 'CCC';} ?>;
display: block;
width: 645px;
margin: 0px 0px;
z-index:1;
_margin-bottom:10px;
*margin-bottom:10px;
_padding-bottom:10px;
*padding-bottom:10px;
_border:none;
*border:none;
}

.featurebox {
display: block;
width: 100%;
margin:auto;
}

#image-wrapper {
margin:0 auto;
display:none;
width: 100%;
}
	
#full-image {
position:relative;
padding:15px;
width: 100%;
display: block;
}

.copy {
font-size: 9px;
float: right;
margin-top: 5px;
color: #7F7F7F;
}

#text {
float:right;
position: absolute;
top: 5px;
right: 50px;
width: 50%;
height:0;
overflow:hidden;
margin-top: 8px;
z-index:4;
padding:0px;
}

#text h3 {
margin-bottom: 5px !important;
}

#text h3 a {
color:#<?php if ( get_option('front_sl_title_color')) {echo get_option('front_sl_title_color');} else {echo '333';} ?>;;
font-size: 18px;
display: block;
font-weight:bold;
}

#text p {
color:#<?php if ( get_option('front_sl_text_color')) {echo get_option('front_sl_text_color');} else {echo '333';} ?>;;
display: block !important;
float: none !important;
font-size: 13px;
}

.front_date {
color:#<?php if ( get_option('front-sl_date_color')) {echo get_option('front_sl_date_color');} else {echo '8D8D8D';} ?>;
font-size: 10px;
display: block;
margin-bottom: 5px;
font-style: italic;
}

#image {
width:<?php if ( get_option('front_sl_img_width')) {echo get_option('front_sl_img_width');} else {echo '250';} ?>px;
height:<?php if ( get_option('front_sl_img_height')) {echo get_option('front_sl_img_height');} else {echo '150';} ?>px;
}

#image img {
position:absolute;
z-index:2;
width:<?php if ( get_option('front_sl_img_width')) {echo get_option('front_sl_img_width');} else {echo '250';} ?>px;
height:<?php if ( get_option('front_sl_img_height')) {echo get_option('front_sl_img_height');} else {echo '150';} ?>px;
padding: 0px !important;
float: left;
}

.imgnav {
position:absolute;
width:25%;
height:180px;
cursor:pointer;
z-index:3;
}

#imgprev {left:0;background:none;}
#imgnext {right:0;background:none;}

#imglink {
position:absolute;
height:150px;
width:100%;
z-index:5;
opacity:.4;
filter:alpha(opacity=40);
}

.linkhover { }

#thumbnails {margin-top:20px;height:38px;}

#arrowleft {
float:left;
width:26px;
height:49px;
background:url(<?php echo $direct_path;?>/images/left.png) top center no-repeat;
margin-top: 40px;
margin-right: 15px;
z-index:6;
}

#slideleft:hover {}

#arrowright {
float:right;
width:26px;
height:49px;
background:url(<?php echo $direct_path;?>/images/right.png) top center no-repeat;
margin-top: 40px;
margin-left: 15px;
z-index:7;
}

#slideright:hover {	}

#frontarea {
float:left;
position:relative;
width: 85%;
overflow:hidden;
margin-top: 10px;
display: block;
height: 100px;
background: #<?php if ( get_option('front_sl_bg_color')) {echo get_option('front_sl_bg_color');} else {echo 'EEE';} ?>;
border: 1px solid #<?php if ( get_option('front_sl_border_color')) {echo get_option('front_sl_border_color');} else {echo 'CCC';} ?>;
margin-bottom:10px;
margin-left:5px;
}

html* #frontarea {margin-left:0;}

#fronter {
padding: 15px;
display: block;
left: 0px;
position: absolute;
float: left;
}

#fronter img {
cursor:pointer;
padding: 0px !important;
}

.copy_wrap {
display: block;
margin: 0px;
clear: both;
}

a.copy {
font-size: 9px;
text-align: right;
display: block;
}
		
</style>




<div id="slide-wrapper">
        
        <ul id="slideshow">
                
               <?php
                
		global $post;
                
                $front_sl_slide_max = get_option('front_sl_slide_max');
        
                if(empty($front_sl_slide_max)) {
                        
                        $front_sl_slide_max = 10;
                        
                }
                
                $front_sl_slide_sort = get_option('front_sl_slide_sort');
        
                if(empty($front_sl_slide_sort)) {
                        
                        $front_sl_slide_sort = 'DESC';
                        
                }
                
                $front_sl_slide_order = get_option('front_sl_slide_order');
        
                if(empty($front_sl_slide_order)) {
                        
                        $front_sl_slide_order = 'post_date';
                        
                }
                
                $front_sl_slide_categories = get_option('front_sl_slide_categories');
        
                if(empty($front_sl_slide_categories)) {
                        
                        $front_sl_slide_categories = 0;
                        
                }
                
                $front_sl_slide_chars = get_option('front_sl_slide_chars');
        
                if(empty($front_sl_slide_chars)) {
                        
                        $front_sl_slide_chars = 230;
                        
                }
		
		$args = array( 'meta_key' => 'front_sl_slider', 'meta_value'=> '1', 'post_type' => array('post', 'page'), 'suppress_filters' => 0, 'numberposts' => $front_sl_slide_max, 'orderby' => $front_sl_slide_order, 'order' => $front_sl_slide_sort, 'category' => $front_sl_slide_categories);
		
                if(isset($front_slide_categories) && $front_slide_categories != 0) {
                        
                        $args = array('post_type' => array('post', 'page'), 'suppress_filters' => 0, 'numberposts' => $front_sl_slide_max, 'orderby' => $front_sl_slide_sort, 'order' => $front_sl_slide_order, 'category' => $front_sl_slide_categories);
                        
                }
        
		$myposts = get_posts( $args );
		foreach( $myposts as $post ) :	setup_postdata($post);
		
			
			$custom = get_post_custom($post->ID);
			
			$thumb = front_sl_get_thumb("front_size",$post->ID);
                        $thumb_small = front_sl_get_thumb("front_sl_thumb",$post->ID);
                        
                ?>

                        <li>
                                <h3><a href="<?php the_permalink();?>" title="<?php the_title();?>"><?php the_title();?></a></h3>
                                <span><?php echo $thumb;?></span>
                                <p>
                                        <span class="front_date">Đăng ngày <?php the_time(get_option('date_format')); ?> vào lúc  <?php the_time(get_option('time_format')); ?>  - <?php comments_number('Không có bình luận','One Comment','% Comments'); ?></span>
                                        <?php echo front_sl_cut_text(get_the_excerpt(), $front_sl_slide_chars); ?>
                                </p>
                                <a href="<?php the_permalink();?>" title="<?php the_title();?>"><img src="<?php echo $thumb_small;?>" alt="<?php the_title();?>"> </a>		
                        </li> 	                    	 
                  	  	
      

                <?php endforeach; ?> 

        </ul>
      
        <div id="image-wrapper">
                <div id="full-image">
                        <div id="imgprev" class="imgnav"></div>
                        <div id="imglink"></div>
      
                        <div id="imgnext" class="imgnav"></div>
                        <div id="image"></div>
                        <div id="text">
                                <h3></h3>
                                <p></p>
                        </div>
                </div>
                <div id="thumbs">
                        
                        <div id="arrowleft"></div>
                        <div id="frontarea">
                                
                                <div id="fronter"></div>
                                
                        </div>
                        <div id="arrowright"></div>
                       
                </div>
        </div>
        <script type="text/javascript">
          $('slideshow').style.display='none';
          $('image-wrapper').style.display='block';
          var slideshow=new SLIDE.slideshow("slideshow");
          window.onload=function(){
                  slideshow.auto=<?php if ( get_option('front_sl_slide_auto')) {echo get_option('front_sl_slide_auto');} else {echo 'true';} ?>;
                  slideshow.speed=<?php if ( get_option('front_sl_slide_speed')) {echo get_option('front_sl_slide_speed');} else {echo '6';} ?>;
                  slideshow.link="linkhover";
                  slideshow.info="text";
                  slideshow.thumbs="fronter";
                  slideshow.left="arrowleft";
                  slideshow.right="arrowright";
                  slideshow.scrollSpeed=2;
                  slideshow.spacing=20;
                  slideshow.active="#<?php if ( get_option('front_sl_active_border')) {echo get_option('front_sl_active_border');} else {echo '000';} ?>";
                  slideshow.init("slideshow","image","imgprev","imgnext","imglink");
          }
        </script>
</div>