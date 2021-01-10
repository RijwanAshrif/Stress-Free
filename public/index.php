<?php require_once('../private/init.php');

$user = Session::get_session(new User());
$logged_in = !empty($user);

require("common/php/php-head.php"); ?>

<main>

    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>www.satreefree.com</title>
    <link rel="stylesheet" href="css/main.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" href="css/bootstrap/bootstrap.min.css">
    <script src="js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/howler/2.2.0/howler.min.js"></script>
    <script src="js/sound.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css" integrity="sha512-1PKOgIY59xJ8Co8+NE6FZ+LOAZKjy+KY8iq0G4B3CyeY6wYHN3yt9PW0XpSriVlkMXe40PTKnXrLnZ9+fkDaog==" crossorigin="anonymous" />
</head>
<body>


	<section id="banner">
        <div class="container">
        <div class="row justify-content-end">
        <div style="margin-left:50px;" class="col-md-6">
             
            <video src="img/index-images/v4.MP4" autoplay="" muted=""></video>
            <h1 class="promo-title" ><b>StreeFree is a digital place for focus & relaxation.</b></h1>
            <h5>It is a Webapp for stress relief & relaxation.</h5>
			  <a href="#"><img src="" class="play-btn"><h2><b>Welcome</b></h2></a>
			
         
        </div>
            <div class="works-img">
             <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
<lottie-player src="https://assets6.lottiefiles.com/packages/lf20_29CqAm.json"  background="transparent"  speed="1"  style="width: 700px; height: 700px;"  loop  autoplay></lottie-player>
              
            </div>
        </div>  
        </div>
        
        <img src="img/index-images/seeknew.png" class="bottom-img">


        <div class="bubbles">

          <img src="img/index-images/bubble.png" alt="">
          <img src="img/index-images/bubble.png" alt="">
          <img src="img/index-images/bubble.png" alt="">
          <img src="img/index-images/bubble.png" alt="">
          <img src="img/index-images/bubble.png" alt="">
          <img src="img/index-images/bubble.png" alt="">
          <img src="img/index-images/bubble.png" alt="">
    
        </div>

        
    </section>
	
	 <!-------------services section----------->

    <section id="services">
    <div class="container text-center">
    <h1 class="title">What is StreeFree?</h1>
    <div class="row" text-center>
    <div class="col-md-4 services">

    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
<lottie-player src="https://assets6.lottiefiles.com/packages/lf20_8ouNoM.json"  background="transparent"  speed="1"  style="width: 300px; height: 300px;"  loop  autoplay></lottie-player>
    <h4>Soft</h4>
    <p>Relaxing sounds are effective for relaxation and stress management.</p>

    </div>
    <div class="col-md-4 services">

    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
<lottie-player src="https://assets8.lottiefiles.com/packages/lf20_ZillC7.json"  background="transparent"  speed="1"  style="width: 300px; height: 300px;"  loop  autoplay></lottie-player>
    <h4>Relax</h4>
    <p>Help people to feel stress free & relax.</p>

    </div>
    <div class="col-md-4 services">

    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
<lottie-player src="https://assets1.lottiefiles.com/packages/lf20_bXjlL4.json"  background="transparent"  speed="1"  style="width: 300px; height: 300px;"  loop  autoplay></lottie-player>
    <h4>Random</h4>
    <p>Good sounds can have a profound effect on both the emotions and the body.</p>
    </div>
    </div>
    </div>
	
	
	 <section class="section inactive">
        <div class="container">
            <div class="pb-20 border-b">
                <div class="oflow-hidden mb-20">
                    <h5 class="float-l mb-10 col-md-3"><a href="box.php"><b>Comment Box</b></a></h5>
					<h5 class="float-l mb-10 col-md-4"><a href="video.php"><b>Relaxing Video Gallery</b></a></h5>
					<h5 class="float-l mb-10 col-md-4"><a href="sketch.php"><b>Drawing App</b></a></h5>
                    <a data-page="tracks" data-title="Track" href="track.php" class="not-load link mb-10 float-r"><b><h5>View All</h5></b></a>
                </div>
                <div id="slider-tracks" class="pos-relative"></div>
            </div><!--pb-50 border-b-->
        </div><!--container-->
    </section>
	<br>
	<br>
</section>
 <!---------------About Us--------------->

    <section id="about-us">
    <div class="container">
    <h1 class="title text-center">Why we need StreeFree?</h1>
    <div class="row">
    <div class="col-md-6">
        <p class="about-title">Why we're different?</p>
        <ul>
            <li>Try to satisfy almost all demands of users.</li>
            <li>Our goal is to help users feel relax and stress free.</li>
            <li>Background sounds help our mind feel relax around and keep concentrated on task.</li>
            <li>help us to create an inspiring work environment.</li>
            <li>Our background sounds can help to put ourself into a relaxed mode.</li>
        </ul>
    </div>
    <div class="col-md-6">
        <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
<lottie-player src="https://assets10.lottiefiles.com/packages/lf20_rpZeHP.json"  background="transparent"  speed="1"  style="width: 600px; height: 600px;"  loop  autoplay></lottie-player>
    </div>
    </div>
    </div>
    </section>

    <!-----------Testimonials--------->

    <section id="testimonials">
    <div class="container pt-5">
    <h1 class="title text-center">What clients say?</h1>
    <div class="row offset-1">
    <div class="col-md-5 testimonials">
        <p>StreeFree can be our little helper to enter a familiar workspace & feel relax.</p>
        <img src="img/index-images/undraw_male_avatar_323b.svg">
        <p class="user-details"><b>Jon Doe</b><br>Youtuber</p>
    </div>
    <div class="col-md-5 testimonials">
        <p>StreeFree is a best place to feel relax.<br>Really awesome.</p>
        <img src="img/index-images/undraw_female_avatar_w3jk.svg">
        <p class="user-details"><b>Lucy Grey</b><br>Designer</p>
    </div>
    <div class="col-md-5 testimonials">
      <p>Just awesome.</p>
      <img src="img/index-images/undraw_profile_pic_ic5t.svg">
      <p class="user-details"><b>Henry Adam</b><br>Musician</p>
    </div>
    <div class="col-md-5 testimonials">
    <p>Sounds good.</p>
    <img src="img/index-images/undraw_male_avatar_323b.svg">
    <p class="user-details"><b>Tom Miles</b><br>Youtuber</p>
    
    </div>
	<div class="work-img">
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
<lottie-player src="https://assets2.lottiefiles.com/packages/lf20_qmgFMQ.json"  background="transparent"  speed="1"  style="width: 650px; height: 650px;"  loop  autoplay></lottie-player>
    </div>
    </div>
    </section>

    <!-----------social media section------------->
    <section id="social-media">
    <div class="container pt-5 text-center">
      <p>Find us on Social Media</p>
        <div class="social-icons" >
          <a href="#"><img src="img/index-images/fb.png"></a>
          <a href="#"><img src="img/index-images/insta.png"></a> 
          <a href="#"><img src="img/index-images/tw.png"></a> 
          <a href="#"><img src="img/index-images/wa.png"></a> 
          <a href="#"><img src="img/index-images/link.png"></a> 
          <a href="#"><img src="img/index-images/snap.png"></a>  
        </div>
    </div>
    </section>

    


    <!-----------------smooth scroll----------------->
    <script src="js/smooth-scroll.js"></script>
    <script>
      var scroll = new SmoothScroll('a[href*="#"]');
    </script>



    <!--------------pop up-------------------------->
    
    <script src="js/app.js"></script>


    

</body>
</html>
    
   


   

    <?php echo "<script>currentAction = '" .  MAIN_ACTION  . "'</script>"; ?>

    <script>var paginationElem = '#popular-playlists',
            pagination = 1,
            featuredPage = pagination,
            reachAtTheEnd = false;
    </script>
</main>


<?php require("common/php/php-footer.php"); ?>



<script>

    /*MAIN SCRIPTS*/
    
    (function ($) {
        "use strict";
        
        loadHomePage(pagination);

        $(window).on('scroll', function(e){

            if(!reachAtTheEnd){
                if (($(paginationElem).offset().top + $(paginationElem).height()) < ($(window).scrollTop() + $(window).height())){
                    pagination++;

                    loadHomePage(pagination);
                    
                }
            }
        });

    })(jQuery);

</script>

</body>

</html>

