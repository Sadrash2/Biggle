<!-- single -->
	<div class="single">
		<div class="breadcrumb_dress">
		<!-- <div class="container"> -->
			<ul>
				<li><a href="index.html"><span class="glyphicon glyphicon-home" aria-hidden="true"></span> Home / Products /</a> <?php echo $title; ?></li>
			</ul>
		<!-- </div> -->
	</div>
		<div class="container">
			<div class="col-md-4 single-left">
				<div class="flexslider">
					<ul class="slides">
                           <?php
                           
                            $counter=1;
                                while ($counter <= 5) {
                                	
                                     if(isset($results['_source']['image'.$counter])){
                                          echo '<li data-thumb="'.$results['_source']['image'.$counter].'">
										    <div class="thumb-image"> <img src="'.$results['_source']['image'.$counter].'" data-imagezoom="true" class="img-responsive"> </div>
											 </li> ';
                                    }
                                        $counter++;
                                }
                          
                            ?>
						
				
					</ul>
				</div>
				<!-- flixslider -->
					<script defer src="<?php echo $this->config->item("base_url");?>assets/js/jquery.flexslider.js"></script>
					<link rel="stylesheet" href="<?php echo $this->config->item("base_url");?>assets/css/flexslider.css" type="text/css" media="screen" />
					<script>
					// Can also be used with $(document).ready()
					$(window).load(function() {
					  $('.flexslider').flexslider({
						animation: "slide",
						controlNav: "thumbnails"
					  });
					});
					</script>
				<!-- flixslider -->
				<!-- zooming-effect -->
					<script src="<?php echo $this->config->item("base_url");?>assets/js/imagezoom.js"></script>
				<!-- //zooming-effect -->
			</div>
			<div class="col-md-8">
				<!-- <h3 style="line-height: 35px;"> -->
					<div class="col-md-12">
						
				<?php
				 echo $results['_source']['name']; ?>
				</div>
				<div class="col-md-12">
					
				 	
					<?php
						// var_dump($merchants);
					foreach ($merchants['data'] as $key => $value) {
						if($value['merchantId']==$results['_source']['merchantId']){
							echo '<img class="" src="'.$value['imageUrl'].'" width="150px">';
						}
					}
					?>
				</div>
					
				 <!-- </h3> -->
				<div class="rating1 col-md-12 p-0" style="display: none;">
					<span class="starRating">
						<input id="rating5" type="radio" name="rating" value="5">
						<label for="rating5">5</label>
						<input id="rating4" type="radio" name="rating" value="4">
						<label for="rating4">4</label>
						<input id="rating3" type="radio" name="rating" value="3" checked>
						<label for="rating3">3</label>
						<input id="rating2" type="radio" name="rating" value="2">
						<label for="rating2">2</label>
						<input id="rating1" type="radio" name="rating" value="1">
						<label for="rating1">1</label>
					</span>
				</div>
				
					<div class="description col-md-12">
						<h5><i>Description</i></h5>
						<?php
							if(htmlspecialchars_decode($results['_source']['description'])=='' || !isset($results['_source']['description'])){
								echo '<p> There is no description set for this product from the merchant of it, you can see the details at the shop side, click to get your promo code and enjoy your product cheaper now! </p>';
							}
							else{
								echo '<p>'.htmlspecialchars_decode($results['_source']['description']) .'</p>'; 
							}
						
							?>
					</div>
					
							
				
					<div class="color-quality">
						<!-- <div class="color-quality-left">
							<h5>Color : </h5>
							<ul>
								<li><a href="#"><span></span>Red</a></li>
								<li><a href="#" class="brown"><span></span>Yellow</a></li>
								<li><a href="#" class="purple"><span></span>Purple</a></li>
								<li><a href="#" class="gray"><span></span>Violet</a></li>
							</ul>
						</div> -->
						<div class="color-quality-right">
							<!-- <h5>Quality :</h5>
							 <div class="quantity"> 
								<div class="quantity-select">                           
									<div class="entry value-minus1">&nbsp;</div>
									<div class="entry value1"><span>1</span></div>
									<div class="entry value-plus1 active">&nbsp;</div>
								</div>
							</div> -->
							<!--quantity-->
									<script>
									$('.value-plus1').on('click', function(){
										var divUpd = $(this).parent().find('.value1'), newVal = parseInt(divUpd.text(), 10)+1;
										divUpd.text(newVal);
									});

									$('.value-minus1').on('click', function(){
										var divUpd = $(this).parent().find('.value1'), newVal = parseInt(divUpd.text(), 10)-1;
										if(newVal>=1) divUpd.text(newVal);
									});
									</script>
								<!--quantity-->

								
								
						</div>
						<div class="clearfix"> </div>
					</div>
					<div class="occasional" style="display:none">
						<h5>Occasion :</h5>
						<div class="colr ert">
							<div class="check">
								<label class="checkbox"><input type="checkbox" name="checkbox" checked=""><i> </i>Occasion Wear</label>
							</div>
						</div>
						<div class="colr">
							<div class="check">
								<label class="checkbox"><input type="checkbox" name="checkbox"><i> </i>Party Wear</label>
							</div>
						</div>
						<div class="colr">
							<div class="check">
								<label class="checkbox"><input type="checkbox" name="checkbox"><i> </i>Formal Wear</label>
							</div>
						</div>
						<div class="clearfix"> </div>
					</div>
					<div class="simpleCart_shelfItem occasiona">
						<a class="btn btn-facebook btn-sm" href="https://www.facebook.com/sharer/sharer.php?s=400&p[url]=<?php echo $url; ?>" target="_blank" onclick="window.open(this.href,'targetWindow','toolbar=no,location=0,status=no,menubar=no,scrollbars=yes,resizable=yes,width=100,height=250'); return false"><i class="fa fa-share-alt "></i> Share</a>
						<i class="wishlist glyphicon glyphicon-heart-empty pull-right wishlist_item" id="<?php echo $results['_source']['productId']; ?>" style="cursor:pointer;padding-right: 1em;font-size: 30px"></i>
						<h4>
                                <small>
                                <?php 
// var_dump($results['_source']);
                                $price= isset($results['_source']['discountPrice'])? $results['_source']['discountPrice']: $results['_source']['price'];
                                ?>
                                <p class="" style="font-size: 20px"><div><?php echo $results['_source']['currency']. ' '.number_format($price, 2, '.', ''); ?> (Normal)</div> <i class="item_price" style="color: #ED6C05;font-weight: 600;font-style: normal;"><?php echo $results['_source']['currency'].' '.number_format($results['_source']['biggleDiscount'], 2, '.', ''); ?> </i> (biggle)</p>
                                </small></h4>
							<div class="clearfix"> </div>
							<p class="pull-left"><a class="item_add get_discount_code" onclick="get_promo('<?php echo $results['_source']['productId']?>','<?php echo $results['_source']['merchantId']?>','<?php echo $results['_source']['promoType']?>','<?php echo $results['_source']['promoBased']; ?>')" style="cursor: pointer">Get Discount Code
							</a> 
								<div id="loader_promo"></div>

							<!--  <a class="item_add" href="<?php echo $results['product']['url']; ?>">Already have a discount, Go To <b><?php echo $results['product']['merchantName']; ?></b></a> --></p>

					</div>

			</div>
			
			<div class="clearfix"> </div>
		</div>
	</div>

 <div class="modal fade" id="promo_code" tabindex="-1" role="dialog" aria-labelledby="promo_code"
        aria-hidden="true">
        <div class="modal-sm modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        &times;</button>
                    <h3 class="text-center biggle_color" id="myModalLabel" style="font-weight: 600">
                     <img src="<?php echo $this->config->item("base_url");?>assets/img/price_tag.png" width="48px" > Copy & Place at the Shop Discount Area!</h3>
                </div>
                <div class="modal-body modal-body-sub" style="padding-top: -10px">
                    <div class="row">
                        <div class="col-md-12 modal_body_left modal_body_left1">
                            <div class="sap_tabs">  
                                <div class="row omb_row-sm-offset-1 omb_socialButtons">
                            <div class="contact text-center">
                                <img src="<?php echo $this->config->item("base_url");?>assets/img/success_tick.png" width="50px">
	                            <div class="text-center content" style="font-weight: 600;font-size: 1.6em">
		                            <p style="font-weight: 700" id="promo_title"></p>
		                            <p style="font-weight: 400;font-size: 13px" id="promo_desc"> The discount codes are unique and you can only get 2 per day, Make sure you utilize them :)</p>
		                            <p class="promo_desc2 text-left" style="font-size: 12px;padding-left: 6em;"></p>
		                            <br>
		   							<!-- <input type="text" id="promocode_input" value="comingup.."><span id="copied" style="font-size: 12px;color:green"></span> -->
		   							<div class="input-group code_place2 col-md-9 col-xs-12" style="margin: 0 auto">
										<input type="text" class="form-control input-lg" id="promocode_input" value="Something went Wrong, pls reload">
									    <a class="input-group-addon copybtn2" id="GTM_Gift_lazada_promo_click copybtn2" onclick="copy_code()" style="cursor: pointer;font-weight: bold">Copy</a>
									  <!-- <input type="text" class="form-control" placeholder="Username" aria-describedby="basic-addon1"> -->
									</div>
	                             </div>
                            	<p> <button disabled redirect="<?php echo $results['_source']['url']; ?>" id="copy_go" class=" btn btn-primary form-control">Copy Code First</button>

                         
                            	</p>
</div>
                            </div>
                            </div>
                            
                        </div>
                       
                    </div>
                </div>
            </div>
        </div>
    </div>


<div id="note_promo_end" class="modal fade" role="dialog">
  <div class="modal-sm modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header ">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <div class="center_img">
        	
        <img src="<?php echo $this->config->item("base_url"); ?>assets/img/exlamation_success.jpg" width="70px">
        </div>
        <br>
        <h5 class="modal-title text-center" style="font-size: 17px;font-weight: 600">Discount codes for this shop has been finished!</h5>
      </div>
      <div class="modal-body text-center">
        <!-- <p><b>.</b></p> -->
        <p> <img src="<?php echo $this->config->item("base_url"); ?>assets/img/smile.png" width="30px"> Dont Worry! we'r refilling new ones soon! in a day or two </p>
        
        <!-- <p><b>- No purchase needed.</b></p> -->
        <!-- <p>- Please make sure to register at </p> -->
      </div>
      <div class="modal-footer center_img">
        <button type="button" class="btn btn-default btn-lg" data-dismiss="modal">Got it</button>
        <a href="<?php echo $results['_source']['url']; ?>" target="_blank" class="btn btn-primary btn-lg">Buy Anyways</a>
      </div>
    </div>

  </div>
</div>
<style type="text/css">
	
	/* nice bootstrap ready facebook share button http://justincron.com */
.btn-facebook {
	color: #fff;
	background-color: #4C67A1;
}
.btn-facebook:hover {
	color: #fff;
	background-color: #405D9B;
}
.btn-facebook:focus {
	color: #fff;
}
.center_img{
	width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>

<script type="text/javascript">
// Copy to clipboard example
	document.querySelector("#copy_go").onclick = function() {
		var link= $(this).attr('redirect');
  		window.open(link, '_blank');
	};

function copy_code(){
  var copyText = document.getElementById("promocode_input");
  copyText.select();
  document.execCommand("Copy");
  $('#copy_go').attr('disabled',false);
  $('#copy_go').html('Go');
  $('.copybtn2').html('Copied');
}

  var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>';
  var csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';

	function copied_go(text,merchant,id,promoType,promoBase){
		// $('#promo_code').modal('show');
		// alert(promoType);
		// console.log('ss '+text+' '+merchant,id,promoType,promoBase);
		$('#loader_promo').addClass('fa fa-cog fa-spin');

		$.ajax({
	        type : "GET",
	        url:'<?php echo $this->config->item("base_url");?>auth/update_promo_status',
	        data:{'status':text,csrfName : 
	         csrfHash},
	        success:function(data){
	            console.log('status');
	            // console.log(data);
	               var data = $.parseJSON(data);
					get_promo(id,merchant,promoType,promoBase);
	              
	            // console.log('thiss');
	        },
	        error: function(data){
	            console.log(data);
	        }
	    }); 
	}

	function get_promo(id,merchantId,promoType,promoBase){
		$('#loader_promo').addClass('fa fa-cog fa-spin');
	    var productId= id;
	  
	    $.ajax({
	        type : "GET",
	        url:'<?php echo $this->config->item("base_url");?>auth/get_promo',
	        data:{'productId':productId,'merchantId':merchantId,'promoBase':promoBase,'promoType':promoType,csrfName : 
	         csrfHash},
	        success:function(data){
	         
	            if(!JSON.parse(data)){
	                $('#login').modal('show');
						$('#loader_promo').removeClass('fa fa-cog fa-spin');

	            }
	            else{
		            var data = $.parseJSON(data);
		            if( data.success ==false){
		                $('#promo_code_confirm').modal('show');
						$('#loader_promo').removeClass('fa fa-cog fa-spin');
		            }
		            else{
	            		// console.log(data);
	            		if(merchantId=="BGL_MY_LAZADA"){ // quick fix to be automated by backend later on
		            		$('#note_promo_end').modal('show');
							$('#loader_promo').removeClass('fa fa-cog fa-spin');
	            		}
	            		else{
			            	$('#promo_code').modal('show');
							$('#loader_promo').removeClass('fa fa-cog fa-spin');
			            	$('#promo_code .content #promo_title').html(data.message);
			            	$('#promo_code #promocode_input').val(data.data.code);
			            	$('.promo_desc2').append('<ul>'+data.note.data.note+'</ul>');
			            	// console.log();
			            	$('#copied').html(' Copied! we are redirecting you to buy..');
	            		}
		    
		            }
	            }
	        },
	        error: function(data){
	            console.log(data);
	        }
	    }); 
	}


  $('.wishlist_item').click(function(){
  	var id= $(this).attr('id');
  	if ($(this).hasClass('hearted')){ // remove
  		$.ajax({
            //type:'GET',
            url:'<?php echo $this->config->item("base_url");?>index.php/auth/dislike_it',
            type : "GET",
            data:{'id':id,csrfName : 
             csrfHash},
            success:function(data){
                console.log(data);

                if( data =='false'){
                	$('#login').modal('show');
                }
                else{
                	$('#'+id).css('color','grey');
                	$('#'+id).removeClass('hearted');	
                }
            },
            error: function(data){
                // console.log(data);
            }
        }); 
  	}
  	else{ //add
  		$.ajax({
            //type:'GET',
            url:'<?php echo $this->config->item("base_url");?>index.php/auth/like_it',
            type : "GET",
            data:{id:id,csrfName : 
             csrfHash},
            success:function(data){
                console.log(data);
                if( data =='false'){
                	$('#login').modal('show');
                }
                else{
                	$('#'+id).css('color','red');
                	$('#'+id).addClass('hearted');
                }
              
            },
            error: function(data){
                // console.log(data);
            }
        }); 
  	}
		
        }); 
  // jQuery(document).ready(function(){
  //   $('.load_more_search').click(function(){
  //     alert('s');
  //   });
  //   $('.wishlist').click(function(){
  //       alert('Added to wishlist');
  //   });
  // });
 
</script>
