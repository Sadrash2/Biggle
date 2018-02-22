<?php 
if (isset($search_key) || isset($search_category)){ ?>

<div class="container text-center" style="padding-top: 4em">

    <?php 
    if(isset($search_category)){
      echo '<h3 class="">category: <b> '.$search_key.'</b></h3>';
              // echo '<h2 class="glyphicon glyphicon-camera "> '.$search_category2.'</h2>';
  }
  else{
            // var_dump($results);
    if(empty($results->hits->hits)){  echo '<h4> 0 results: <span class="text-success">please check your spelling or choose the right shop(s)</span></h4>'; }

        else{ echo '<h4> results for: <span class="text-success" style="text-decoration:underline">'. $search_key.'</span></h4><span style="font-size:14px;">found '.$results->hits->total.' results</span>'; }
        }
        ?>
    </div>

    <!-- new-products -->
    <div class="new-productss" style="padding-top:2em">

        <div class="">

            <div class="col-sm-10 col-md-offset-1 w3ls_dresses_grid_right">
               <div class="w3ls_dresses_grid_right_grid1">

               <div class="w3ls_dresses_grid_right_grid2_left">
                <br>
                <?php

                $pricefrom=isset($pricefrom) ? $pricefrom : 1;
                $priceto=isset($priceto) ? $priceto : 10000;
                ?>
                <b style="font-size: 12px">rm 1 </b> <input id="ex2" type="text"  class="span2" value="" data-slider-min="1" data-slider-max="10000" data-slider-step="5" data-slider-value="[<?php echo $pricefrom.','.$priceto ?>]"/> <b style="font-size: 12px">rm 10,000</b> 
            </div>
            <div class="w3ls_dresses_grid_right_grid2_right">
                <form action="<?php echo $link; ?>" method="get" class="row">
                  <select name="sort" class="select_item" disabled onchange="this.form.submit()">
                    <option  value="sort_price" selected="selected">relevance sorting</option>
                    <option value="sort_price" >sort by price: low to high</option>
                    <option value="sort_price" >sort by price: high to low</option>
                    <!-- <option>sort by popularity</option> -->
                </select>
            </form>
        </div>
        <div class="clearfix"> </div>
    </div>
    <div class="agileinfo_new_products_gridss results_data">
        <?php 
        $imgs= array();
        // var_dump()$this->session->userdata;
        foreach($results->hits->hits as $key=>$val){  $val= $val->_source; 
                        if(!in_array($val->image1, $imgs)) { // tempo repeated_data_remove func, due to the recent issues of duplicates
                            array_push($imgs, $val->image1);
                            ?>
                            <div class="col-md-3 col-xs-12 col-sm-6 index_products">
                                <!-- <img src="assets/img/mer_1.png" width="50px"> -->
                                <div class=" ">
                                    <div class="hs-wrapper1 products">
                                        <?php

                                        if(! isset($val->image1)){
                                            echo '<a href="product/'.ucwords(str_replace(" ","-",$val->productid.'/'.$val->name.'/'.$val->name)).'"><img src="'.$this->config->item("base_url").'assets/img/favicon.png" alt="" class="img-responsive" /> </a>';
                                        }
                                        else{
                                            echo '<a href="product/'.ucwords(str_replace(" ","-",$val->productid.'/'.$val->merchantname.'/'.$val->name)).'"><img src="'.$val->image1.'" alt="" class="img-responsive" /> </a>';
                                        }

                                        ?>

                            </div> 

                            <h5 style="min-height: 43px;max-height: 43px"><a title="<?php echo $val->name; ?>" href="product/<?php echo ucwords(str_replace(" ","-",$val->productid.'/'.$val->merchantname.'/'.$val->name)); ?>"><?php echo mb_strimwidth($val->name, 0, 61, " .."); ?></a></h5>
                            <h5 style="margin-top:0.5em;min-height: 0px;"> <?php echo $val->merchantname; ?> </h5>
                            <div class="simplecart_shelfitem">
                                <h4>
                                    <small>
                                        <?php $price= isset($val->discountprice)? $val->discountprice: $val->price;
                                        ?>
                                        <p class=""><div><?php echo $val->currency. ' '.number_format($price, 2, '.', ''); ?></div> <i class="item_price" style="color: #ed6c05;font-weight: 600;font-style: normal;"><?php echo $val->currency.' '.number_format($val->bigglediscount, 2, '.', ''); ?> </i> (biggle)<i class="wishlist glyphicon glyphicon-heart-empty pull-right wishlist_item" id="<?php echo $val->productid; ?>" style="cursor:pointer"></i></p>
                                    </small></h4>
                                    <!-- <p><a class="item_add" href="#">details / get promo</a></p> -->
                                </div>
                            </div>
                        </div>
                        <?php

                    } 
                } 
            // var_dump($imgs);
                ?>

                <div class="clearfix"> </div>
                <div class="row col-md-12 col-xs-12 text-center" style="padding-bottom: 50px;display:none">
                    <?php
                    $query2 = "http://".$_server['http_host'].$_server['request_uri'];

                    // echo $pagination_link;
                    if($page<1){ 
                      $page_ori=$page;
                      $page+=1;
                      $page_pre=$page-1;
                      echo '<a class=" btn btn-default pull-center" disabled style="margin-top: 2em;"> <-previous</a> 
                      <a class=" btn btn-default pull-center" href="'.$link.'&page='.$page.'" style="margin-top: 2em"> next page -> </a>';
                  }
                  else{ // a quick fix, to be replaced with the CI pagination 
                    $page_ori=$page;
                    $page_pre=$page-1;
                    $page+=1;
                      // $pagination_link="";
                    echo '<a class=" btn btn-default pull-center" href="'.$this->config->item("base_url").$pagination_link.'&page='.$page_pre.'" style="margin-top: 2em;"> <-previous</a>'; 
                      // echo $page;
                    echo '<a class="btn pull-center" style="margin-top: 2em;"> '.$page_ori.'  </a>'; 

                    echo '<a class=" btn btn-default pull-center" href="'.$this->config->item("base_url").$pagination_link.'&page='.$page.'"  style="margin-top: 2em"> next-> </a>';
                }
                ?>

            </div>
            <br>
        </div>
        <!-- <div class="clearfix"> </div> -->
    </div>

</div>
</div>
<a class="btn btn-success center_img load_more" id="50" val="<?php echo $search_key; ?>">load more</a>

<?php }

if ($page_status==0){ 

  ?>

  <!-- banner -->
  <!-- <div class="" style=" background: #00000030;"> -->
    <div class="banners" id="home1" style="margin-top: 9em;">
        <div class="container ">
            <?php
            $urls= $_server['http_host'];
           // echo preg_replace('/(?:https?:\/\/)?(?:www\.)?(.*)\/?$/i', '$1', $urls);
            ?>
           <!--  <!-- <div class="col-md-4 key-feature overflowed-hidden">
                <img src="<?php echo $this->config->item("base_url");?>assets/img/45.jpg" style="width: 100%">
            </div> -->
            <div class="row" style="padding-bottom: 0.5em">
                <div class="col-md-12 col-xs-12">
                    <div class="hidden-xs hidden-sm col-md-12 key-feature overflowed-hidden">
                        <img src="<?php echo $this->config->item("base_url");?>assets/img/website-landing-page-image.jpg" style="width: 100%">
                    </div>
                    <div class="hidden-md hidden-lg col-md-4 col-xs-12 key-feature overflowed-hidden">
                        <img src="<?php echo $this->config->item("base_url");?>assets/img/landing1.jpg" style="width: 100%">
                    </div>
                    <div class="hidden-md hidden-lg col-md-4 col-xs-12 key-feature overflowed-hidden">
                        <img src="<?php echo $this->config->item("base_url");?>assets/img/landing2.jpg" style="width: 100%">
                    </div>
                    <div class="hidden-md hidden-lg col-md-4 col-xs-12 key-feature overflowed-hidden">
                        <img src="<?php echo $this->config->item("base_url");?>assets/img/landing3.jpg" style="width: 100%">
                    </div>
                </div>
            </div>
            <div class="row ">
                <div class="col-md-12 col-xs-12">
                    <a class="col-md-6 col-xs-12 key-feature overflowed-hidden" style="padding-right: 0px;">
                        <img src="<?php echo $this->config->item("base_url");?>assets/img/landing-line1.jpg" style="width: 100%">
                    </a> 
                    <a class="col-md-6 col-xs-12 key-feature overflowed-hidden" style="padding-left:0px;">
                        <img src="<?php echo $this->config->item("base_url");?>assets/img/landing-line2.jpg" style="width: 100%">
                    </a> 
                    <a href="<?php echo $this->config->item("base_url");?>gifts" class="col-md-4 col-xs-12 key-feature overflowed-hidden">
                        <img src="<?php echo $this->config->item("base_url");?>assets/img/gifts/landing-1.jpg" style="width: 100%">
                    </a> 
                    <a href="<?php echo $this->config->item("base_url");?>gifts"  class="col-md-4 col-xs-12 key-feature overflowed-hidden">
                        <img src="<?php echo $this->config->item("base_url");?>assets/img/gifts/landing-2.jpg" style="width: 100%">
                    </a> 
                    <a href="<?php echo $this->config->item("base_url");?>gifts"  class="col-md-4 col-xs-12 key-feature overflowed-hidden">
                        <img src="<?php echo $this->config->item("base_url");?>assets/img/gifts/landing-3.jpg" style="width: 100%">
                    </a> 
                </div>
            </div>
            <div class="row" style="display: none">
                <div class="banner-bottom1">
                    <div class="agileinfo_banner_bottom1_grids">
                        <div class="col-md-7 agileinfo_banner_bottom1_grid_left">
                            <h3>happens every 2 weeks!<span>lucky <i>draw</i></span></h3>
                            <a href="#">biggle now</a>
                        </div>
                        <div class="col-md-5 agileinfo_banner_bottom1_grid_right">
                            <h4>next draw</h4>
                            <div class="timer_wrap">
                                <div id="counter"> </div>
                            </div>
                            <!-- <script src="js/jquery.countdown.js"></script> -->
                            <!-- <script src="js/script.js"></script> -->
                        </div>
                        <div class="clearfix"> </div>
                    </div>
                </div>
            </div>
            <div class="row">

                <?php

                if(isset($results_home)){ 
                  $results_home= json_decode($results_home);
                  ?>
                  <div class="agileinfo_new_products_gridss results_datas" style="padding-top: 2em">
                    <?php 

                    $imgs= array();
                    $copy = array();
                    while (count($results_home->hits->hits)) { // remove d duplicates, quick fix
    // takes a rand array elements by its key
                        $element = array_rand($results_home->hits->hits);
    // assign the array and its value to an another array
                        $copy[$element] = $results_home->hits->hits[$element];
    //delete the element from source array
                        unset($results_home->hits->hits[$element]);
                    }

                    $results_home->hits->hits = $copy;

            // var_dump()$this->session->userdata;
                    foreach($results_home->hits->hits as $key=>$val){  $val= $val->_source; 
                        if(!in_array($val->image1, $imgs)) { // ifnot repeated data
                            array_push($imgs, $val->image1);
                            ?>
                            <div class="col-md-3 col-xs-12 col-sm-6 index_products">
                                <!-- <img src="assets/img/mer_1.png" width="50px"> -->
                                <div class=" ">
                                    <div class="hs-wrapper1 products">
                                        <?php

                                        if(! isset($val->image1)){
                                            echo '<a href="product/'.ucwords(str_replace(" ","-",$val->productid.'/'.$val->name.'/'.$val->name)).'"><img src="'.$this->config->item("base_url").'assets/img/favicon.png" alt="" class="img-responsive" /> </a>';
                                        }
                                        else{
                                            echo '<a href="product/'.ucwords(str_replace(" ","-",$val->productid.'/'.$val->merchantname.'/'.$val->name)).'"><img src="'.$val->image1.'" alt="" class="img-responsive" /> </a>';
                                        }

                                        ?>

                                    </div> 

                                    <h5 style="min-height: 43px;max-height: 43px"><a title="<?php echo $val->name; ?>" href="product/<?php echo ucwords(str_replace(" ","-",$val->productid.'/'.$val->merchantname.'/'.$val->name)); ?>"><?php echo mb_strimwidth($val->name, 0, 61, " .."); ?></a></h5>
                                    <h5 style="margin-top:0.5em;min-height: 0px;"> <?php echo $val->merchantname; ?> </h5>
                                    <div class="simplecart_shelfitem">
                                        <h4>
                                            <small>
                                                <?php $price= isset($val->discountprice)? $val->discountprice: $val->price;
                                                ?>
                                                <p class=""><div><?php echo $val->currency. ' '.number_format($price, 2, '.', ''); ?></div> <i class="item_price" style="color: #ed6c05;font-weight: 600;font-style: normal;"><?php echo $val->currency.' '.number_format($val->bigglediscount, 2, '.', ''); ?> </i> (biggle)<i class="wishlist glyphicon glyphicon-heart-empty pull-right wishlist_item" id="<?php echo $val->productid; ?>" style="cursor:pointer"></i></p>
                                            </small></h4>
                                            <!-- <p><a class="item_add" href="#">details / get promo</a></p> -->
                                        </div>
                                    </div>
                                </div>
                                <?php

                            } 
                        } 

                    }
                    ?>
                </div>

            </div>
            <!-- </div> -->
        </div>

        <!-- banner-bottom -->
        <div class="">
            <div class="containesr">
                <br>
                <?php
                $tab1 = utf8_encode($tab1);
                $tab1= json_decode($tab1);
              // var_dump($cats);

                ?> 

                <div class="agileinfo_new_products_gridss results_data">
                    <?php 
                  // echo 's';
                    if(!empty($tab1->products)){ 
                        foreach($tab1->products as $key=>$val){ 
                            $price= $val->price;

                            if(isset($val->discountprice)){
                                $price= $val->discountprice;
                            }
                            ?>
                            <div class="col-md-3 index_products">
                                <!-- <img src="assets/img/mer_1.png" width="50px"> -->
                                <div class=" ">
                                    <div class="hs-wrapper1">
                                        <?php
                                        echo '<a href="product/'.ucwords(str_replace(" ","-",$val->productid.'/'.$val->merchantname.'/'.$val->name)).'"><img src="'.$val->image1.'" alt="" class="img-responsive" /> </a>';
                                        ?>
                                    </div>

                                    <h5 style="min-height: 31px"><a title="<?php echo $val->name; ?>" href="product/<?php echo ucwords(str_replace(" ","-",$val->productid.'/'.$val->merchantname.'/'.$val->name)); ?>" style="color:#999999"><?php echo mb_strimwidth($val->name, 0, 42, "..."); ?></a></h5>
                                    <h5 style="margin-top:0.5em;min-height: 0px;"> <?php echo $val->merchantname; ?> </h5>
                                    <div class="simplecart_shelfitem">
                                        <h4>
                                            <small>
                                                <p class=""><div><?php echo $val->currency. ' '.$discountprice; ?></div> <i class="item_price" style="color: #ed6c05;font-weight: 600;font-style: normal;"><?php echo $val->currency.' '.$val->bigglediscount; ?> </i> (biggle)<i class="wishlist glyphicon glyphicon-heart-empty pull-right wishlist_item" id="<?php echo $val->productid; ?>" style="cursor:pointer"></i></p>
                                            </small>
                                        </h4>
                                        <!-- <p><a class="item_add" href="#">details / get promo</a></p> -->
                                    </div>
                                </div>
                            </div>
                            <?php

                        } 
                    } 
                    ?>
                    <a class="btn btn-success center_img load_more_index" id="50" val="<?php echo $search_key; ?>">load more</a>
                    <?php } ?>
                    <!--modal-video-->
                    <div class="modal video-modal fade" id="mymodal" tabindex="-1" role="dialog" aria-labelledby="mymodal">
                        <div class="modal-dialog" style="max-width: 50em" role="document">
                            <div class="modal-content popup_indexs">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>                        
                                    <?php 

                                    function ismobile() {
                                        return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_server["http_user_agent"]);
                                    }       

                                    if (ismobile()){ ?>
                                    <img style="max-width:99%;" src="<?php echo $this->config->item("base_url");?>assets/img/gifts/mobile-tudung-pop-up.jpg">
                                    <?php } else{ 

                                        ?>

                                        <img style="max-width:99%;" src="<?php echo $this->config->item("base_url");?>assets/img/gifts/website-landing-page-pop-up-tudung-promo.jpg">
                                        <?php } ?>

                                    </div>
                                    <section>
                                        <div class="modal-body">


                                            <!-- <p>tac applied.</p> -->
                                            <!-- <p><a class="item_add pull-right" href="gifts">no, thanks!</a></p> -->

                                        </div>
                                        <div class="clearfix"> </div>
                                    </div>
                                    <div style="">
                                    <!-- <a href="" class="btn btn-success">get it now </a> -->
                                    <a id="gtm_giftpopup" class="item_add" href="gifts">get it now!</a>
                                </div>

                            </section>
                        </div>
                    </div>
                </div>

            </div>
            <div class="clearfix"> </div>
        </div>
    </div>

<script type="text/javascript">
    var csrfname = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var csrfhash = '<?php echo $this->security->get_csrf_hash(); ?>';
    $('.wishlist_item').click(function(){
        var id= $(this).attr('id');
    if ($(this).hasclass('hearted')){ // remove
        $.ajax({
            //type:'get',
            url:'<?php echo $this->config->item("base_url");?>index.php/auth/dislike_it',
            type : "get",
            data:{id:id,csrfname : 
               csrfhash},
               success:function(data){
                console.log(data);

                if( data =='false'){
                    $('#login').modal('show');

                }
                else{
                    $('#'+id).css('color','grey');
                    $('#'+id).removeclass('hearted');   
                }
            },
            error: function(data){
                // console.log(data);
            }
        }); 
    }
    else{ //add
        $.ajax({
            //type:'get',
            url:'<?php echo $this->config->item("base_url");?>index.php/auth/like_it',
            type : "get",
            data:{id:id,csrfname : 
               csrfhash},
               success:function(data){
                console.log(data);
                if( data =='false'){
                    $('#login').modal('show');
                }
                else{
                    $('#'+id).css('color','red');
                    $('#'+id).addclass('hearted');
                }

            },
            error: function(data){
                // console.log(data);
            }
        }); 
    }

}); 

    var array = [];
    $('.load_more').click(function(){
        var params={};
        window.location.search.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(str,key,value) {
            params[key] = value;
        // console.log();
    }
    );
        $(this).html('load more <i class=" text-center pull-center fa fa-spinner fa-spin" style="font-size:35px;margin-left:50%"></i>');
        var thiss= $(this);
        var no= parseint($(this).attr('id'));
        var val=$(this).attr('val');
        var priceto=0;
        var pricefrom=0;
        if(geturlvars()["priceto"]){
            priceto= geturlvars()["priceto"];
        }
        if(geturlvars()["pricefrom"]){
            pricefrom= geturlvars()["pricefrom"];
        }
        // alert(window.location.href);
        // var element= $(this).attr('aria-controls');
        $.ajax({
          type:'post',
          url:'auth/load_more',
          data:{'search':val,'priceto':priceto,'pricefrom':pricefrom, 'query':window.location.href, 'page':no, '<?php echo $this->security->get_csrf_token_name(); ?>' : '<?php echo $this->security->get_csrf_hash(); ?>'},
          success:function(data){
              console.log(data);
              var data= $.parsejson(data);

                        var imgs = []; //avoid dublicates
                        $.each(data.hits.hits,function(index,value){
                            if(imgs.indexof(value._source.image1 )== -1){
                        imgs.push(value._source.image1);
                        var value=value._source;

                        var price=value.price;
                        if (value.discountprice){
                            price= value.discountprice;
                        }
                        var name = value.name.split(" ").join("-").tolowercase();
                        var name = value.name;
                        name= name.substr(0, 43);
                        $('.results_data').append('<div class="col-md-3 index_products"><div class="hs-wrapper1"> <a href="product/'+value.productid+'/'+value.merchantname.split(" ").join("-").tolowercase()+'/'+name+'"> <img src="'+value.image1+'" class="img-responsive"></a></div><h5 style="min-height: 43px"><a title="sas" href="product/'+value.productid+'/'+value.merchantname.split(" ").join("-").tolowercase()+'/'+value.name+'" style="color:#999999">'+name+' ..</a></h5> <h5 style="margin-top:0.5em;min-height: 0px;"> '+value.merchantname+' </h5><div class="simplecart_shelfitem"><h4><small><p class=""></p><div> myr '+price.tofixed(2)+' </div> <i class="item_price" style="color: #ed6c05;font-weight: 600;font-style: normal;"> myr '+value.bigglediscount.tofixed(2)+' </i> (biggle)<br><i class="wishlist glyphicon glyphicon-heart-empty pull-right wishlist_item" id="bgl_my_tudung2u_1296" style="cursor:pointer"></i></small></h4></div>');
                        // console.log(value);
                    }

                });

                // alert('s3000');
                $(thiss).html('load more');
                no+=50;
                $(thiss).attr('id',no);
                    // alert(no);
                },
                error: function(data){
                  console.log('s33');
              }

          }); 

        // }
      // }
  });


    function geturlvars() {
        var vars = {};
        var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi,    
            function(m,key,value) {
              vars[key] = value;
          });
        return vars;
    }

</script>
