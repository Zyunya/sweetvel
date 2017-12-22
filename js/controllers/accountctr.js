com.controller('accountctr', function ($scope, $http, $location, $filter, $state, $timeout, $interval,$rootScope) {

/////////////////////BROADCAST///////////////////////////

$scope.$on('timeline_update',function(event,opt){
        
        $http({ method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'timeline', limit: 10,receiver : opt.receiver,object_type : opt.type }) })
            .then(function (response) {
                 $scope.timeline = response.data.record;
                 
                 if($scope.timeline < 1)
                        {
                         $scope.show_no_timeline = true;
                        }
                        else{
                         $scope.show_no_timeline = false;
                        }
            })
})


    /////////////////////////SEND_CARD_TO_FRIEND//////////////////////
    $scope.send_card = function (ider, type) {
        var name      = $location.search().name ? $location.search().name : "";
        var obj_type  = $location.search().name ? $location.search().type : "";
      
        if(obj_type == 'swcard')
        {
             $state.go('account.chat', { sn: ider * hash });
        setTimeout(function () {
            $('#comment_text').val(host + '/swcard/' + type + '/' + name);
            $('#comment_text').trigger('paste')
        }, 1000);
    }
     if(obj_type == 'video')
    {
         $state.go('account.chat', { sn: ider * hash });
       setTimeout(function () {
            $('#comment_text').val(name);
            $('#comment_text').trigger('paste')
        }, 1000);
    }
    }
    
    /////////////////////////////ACCOUNT_TIMELINE///////////////////////
    $scope.timeline = function () {
        $scope.timeline_show = false;
        $http({ method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'timeline',object_type : 'account_timeline',limit : 10 }) })
            .then(function (response) {
                $scope.timeline_preloader = true;
                $scope.timeline_show = true;
                $scope.timeline = response.data.record;
                if (response.data.record.length > 9) { $scope.arrows = true; }
                 
                 if(response.data.record.length < 1)
                        {
                         $scope.show_no_timeline = true;
                        }
                        else{
                         $scope.show_no_timeline = false;
                        }
                
            })
    }
  

    ///////////////////////////////TIMELINE_COMMON_MESSAGE////////////////////////
    $scope.sendmessage_timeline = function () {
      
        var formdata = new FormData();
        var file  = $id('timeline_file').files[0];
        var files = $id('timeline_file').files;
        formdata.append('ajax', 'sendmessage_timeline');
        formdata.append('message', he.encode($id('timeline_text').value));
        formdata.append('date', datejson());
        formdata.append('object_type', 'account_timeline');
        for (var i = 0, len = filearr_message.length; i < len; i++) {
            formdata.append("file" + i, filearr_message[i]);
        }
        $http({
            method: 'POST',
            url: 'ajax/ajax.php',
            data: formdata,
            headers: { 'Content-Type': undefined },

            beforeSend: function () {
                $scope.send_message_preloader = true;
            },
            complete: function (response) {
                $scope.send_message_preloader = false;
                console.log(response.data);
               
                $scope.messageval = $id('timeline_text').value;
               
                if (response.data.status == '0') {
                    $scope.alert_show(response.data.text);
                }
                 $scope.$broadcast('timeline_update', {type : 'account_timeline'});
                $id('timeline_text').value = "";
                //Make Empty input file and array with files
                $id('timeline_file').value = "";
                filearr_message = filearr_message.slice(0, 0);

                $id('upload_timeline_img_preview_block').classList.add('dp');
                $('.message_img_prev').remove();
            }
        })
    }
    //////////////////////////UNREADED_MESSAGES_COUNT///////////////////////////////

         
    $http({
        method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'myunreadedmessages' })
    }).then(function (response) {
            $timeout(function(){
                console.log('ok');
       // $('#message_pointer_1111').removeClass('colorsub');
       //angular.element('#message_pointer_1111').css('color', 'black');
     
     
            })
        if (parseInt(response.data) > 0) {
                document.getElementById('message_pointer_1111').style.color = '#ff5959';
            $rootScope.unreaded_block_show = true;
            $rootScope.unreaded_count = response.data;
            
        }
        
    
    });
    ///////////////////////////////TIMELINE_OPENGRAPH_MESSAGE/////////////////////
    $scope.open_graph_timeline = function (elem) {

        setTimeout(function () {
            $http({ method: 'POST', url: 'core/sw_og_parser.php', data: $.param({ oglinkprev: elem.target.value }) })
                .then(function (response) {
                    console.log(response.data.status);
                    if (response.data.status === '1') {

                        $id('sender').classList.add('og_btn');
                        $scope.og_data = response.data;
                        $id('chat_og_preview').classList.remove('dp');
                        $scope.clickenable = true;
                        $('body').off('click').on('click', '.og_btn', function (evt) {
                            var title = response.data.title;
                            var og_desc = response.data.desc;
                            var url = response.data.url;
                            var image = response.data.image;
                            var video = response.data.video;
                            var desc = he.encode($id('timeline_text').value);
                            $http({
                                method: 'POST',
                                url: 'ajax/ajax.php',
                                data: $.param({ ajax: 'sendmessage_timeline_og', title: title,video : video, desc: desc, og_desc: og_desc, url: url, image: image, date: datejson(),object_type : 'account_timeline' })
                            })
                                .then(function (response2) {
                                    $scope.messageval = $id('timeline_text').value;
                                     $scope.$broadcast('timeline_update', {type : 'account_timeline'});
                                    if (response2.data.status == '0') {
                                        $scope.alert_show(response2.data.text);
                                    }
                                    console.log(response2.data);
                                    $id('timeline_text').value = "";
                                    $id('timeline_file').value = "";
                                    $id('sender').classList.remove('og_btn');
                                    $id('chat_og_preview').classList.add('dp');
                                    $scope.clickenable = false;
                                });
                        });
                    };
                });
        });
    }
    //////////////////////////////VOICE MESSAGE//////////////////////////
    $scope.sendmessage_voice = function () {
        try {
            var mediaConstraints = {
                audio: true
            };

            navigator.getUserMedia(mediaConstraints, onMediaSuccess, onMediaError);

            function onMediaSuccess(stream) {
                var mediaRecorder = new MediaStreamRecorder(stream);
                mediaRecorder.mimeType = 'audio/wav'; // check this line for audio/wav
                mediaRecorder.ondataavailable = function (blob) {

                    $id('timeline_audio_preview').src = URL.createObjectURL(blob);

                    document.getElementById('chat_audio_preview_play').addEventListener('click', function () {
                        $id('timeline_audio_preview').play();
                    });
                    document.getElementById('chat_audio_preview_pause').addEventListener('click', function () {
                        $id('timeline_audio_preview').pause();
                    })
                    //console.log(blob);
                    $('#sendmessage_audio').off('click').on('click', function () {

                        var formdata = new FormData();
                        formdata.append('ajax', 'sendmessage_timeline_voice');
                        formdata.append('message', he.encode($id('timeline_text').value));
                        formdata.append('voice', blob);
                        formdata.append('date', datejson());
                        formdata.append('object_type', 'account_timeline');
                        $http({
                            method: 'POST',
                            url: 'ajax/ajax.php',
                            data: formdata,
                            headers: { 'Content-Type': undefined }
                        }).then(function (response) {
                            console.log(response.data);
                             $scope.$broadcast('timeline_update', {type : 'account_timeline'});
                        });

                    })
                };
                $('body').on('click', '.start_record_timeline', function () {
                    $(this).removeClass('backsw3 start_record_timeline fa-microphone').addClass('back9 stop_record_timeline pulse fa-microphone-slash');
                    mediaRecorder.start(10000);
                })
                $('body').on('click', '.stop_record_timeline', function () {
                    $(this).removeClass('back9 stop_record_timeline pulse pulse fa-microphone-slash').addClass('backsw3 start_record_timeline  fa-microphone');
                    mediaRecorder.stop();
                })
            }
            function onMediaError(e) {
                console.error('media error', e);
            }
        }
        catch (err) {
            console.log('recording is not supported in your browser');
            return false;
        }
    }

    $scope.sendmessage_voice();
    //////////////////////////////////////////////////////


    $scope.set_status_block_toggle = function () {
        $id('set_status_block').classList.toggle('dp');
    }

    $scope.country_list = function () {
        $http.get("../strings/locations.json")
            .then(function (response) {
                //var s = response.data;
                //var keys = [];
                //for(var k in s) keys.push(k);
                //$scope.countries = keys;
                $scope.countries = response.data;
            });
    }
    $scope.city_list = function (elem) {
        $scope.preloader2 = true;
        $http.get("../strings/locations.json")
            .then(function (response) {
                var city_c = elem.getAttribute('data-key');
                //$scope.cities = JSON.parse(JSON.stringify(response.data))[city_c];
                $scope.preloader2 = false;
                $scope.cities = response.data[city_c];
            });
    }
    $scope.country_list();
$scope.profile_self = function () {
        $scope.account_block_loading = false;
        $scope.account_block_loading_spinner = false;
        $scope.edit_profile_block = false;
        $http({
            method: 'POST',
            url: 'ajax/ajax.php',
            data: $.param({ ajax: 'param' })
        }).then(function (response) {
          
            $scope.account_block_loading         = true;
            $scope.account_block_loading_spinner = true;
            $scope.edit_profile_block            = true;
            $scope.profile_self                  = response.data.record[0];
    
            console.log(response.data.record)
        })
    }

$scope.get_weather = function()
{
      
   if(document.body.contains(document.querySelector( '#openweathermap-widget-19'  )))
        {
            var script = document.createElement('script');script.async = true;
            var s = document.getElementsByTagName('script')[0]; 
            s.parentNode.insertBefore(script, s);
            script.src = "https://openweathermap.org/themes/openweathermap/assets/vendor/owm/js/weather-widget-generator.js";   
        }
 
    $http({
        method : 'POST',
        url    : 'core/weather_server.php',
        data   : $.param({})
    }).then(function(response){
           
        var cityid = response.data.cityid;
        window.myWidgetParam ? window.myWidgetParam : window.myWidgetParam = [];  
        window.myWidgetParam.push({id: 19,cityid: cityid,appid: '17cc0bde28328ebc865f2a0d53ccc69e',units: 'metric',containerid: 'openweathermap-widget-19',  });

        console.log(response.data);
        $scope.weather = response.data;
    })
}   
    $scope.get_weather();
    $scope.profile_self();


    $http({
        method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'personalcards' })
    }).then(function (response) {
        $scope.cardsres = response.data.record;
        //console.log(response.data.record);
        if (response.data.record.length == 0) { $scope.show_nocards = true; }
        else { $scope.show_nocards = false; };

    });
    $scope.get_video = function(){
        $http({
        method : 'POST',
        url    : 'ajax/ajax.php',
        data   : $.param({ajax : 'get_video',limit : 10})
    }).then(function(response){
    $scope.video_list = response.data.record;
    if(response.data.record.length > 9)
    {
        $scope.video_arrow = true
    }
    if(response.data.record  < 1 )
    {
       $scope.show_no_videos = true;
    }
    else
    {
      $scope.show_no_videos = false;
    }
    })
}
   $scope.get_more_video = function(){
       limit1 += 10;
        $http({
        method : 'POST',
        url    : 'ajax/ajax.php',
        data   : $.param({ajax : 'get_video',limit : limit1})
    }).then(function(response){
    $scope.video_list = response.data.record;
    })
}
    $scope.delete_video = function(video,index){
        $http({
        method : 'POST',
        url    : 'ajax/ajax.php',
        data   : $.param({ajax : 'delete_video',video_id : video})
    }).then(function(response){
    if(response.data.status === '1')
    {
        $('.video_delete_pointer').eq(index).slideUp();
    }
    })
}
$scope.get_video();
    $scope.counter = function () {
        var counter_date = [];
        var counter_month = [];
        var counter_year = [];
        for (var i = 1; i <= 31; i++) { counter_date.push(i); }
        for (var x = 1; x <= 12; x++) {if (x < 10) { counter_month.push("0" + x); }else { counter_month.push(x.toString()) }}
        for (var y = 1930; y <= 2003; y++) { counter_year.push(y); }
        $scope.counter_date = counter_date;
        $scope.counter_month = counter_month;
        $scope.counter_year = counter_year;
    }

    $scope.alert = function (text) {
        $(".alert").fadeIn(0).text(text)
        setTimeout(function () { $('.alert').fadeOut(0) }, 5000);
    }
    $scope.set_status_self = function () {
        var statusaccount = he.encode($name('options_status_self').value);
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'set_status_self', statusaccount: statusaccount })
        }).then(function (response) {
            $scope.profile_self.status_account = statusaccount;
            $id('set_status_block').classList.toggle('dp')
            //console.log(response.data);
        })
    }
    $scope.set_state = function (elem) {
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'set_state', state_param: elem })
        }).then(function (response) {
            if (response.data.status === '1') {
                $scope.profile_self.state_point = response.data.param;
                $scope.profile_self.state = response.data.text;
            }
        })
    }
    $scope.set_status = function (elem) {
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'set_status', status_param: elem })
        }).then(function (response) {
            if (response.data.status === '1') {
                $scope.profile_self.status_point = response.data.param;
                $scope.profile_self.status       = response.data.text;
            }
        })
    }

    $scope.ff = function () {
        $state.go('sw');
    }
    $scope.ff();
    $scope.edit_account_profile = function () {
        var data = {
            ajax: 'edit_account_profile',
            fname: $name('options_fname').value,
            lname: $name('options_lname').value,
            gender: $name('options_gender').value,
            country: $name('options_country').value,
            city: $name('options_city').value,
            about_me: $name('options_about_me').value,
            place_name: $name('options_place_name').value,
            place_adress: $name('options_place_adress').value,
            place_id: $name('options_place_id').value,
            date: $name('options_date').value,
            month: $name('options_month').value,
            year: $name('options_year').value,
        };
        if ($name('options_fname').value.length < 3) { $scope.alert($filter('translate')("%name%") + " " + $filter('translate')("%action_length_3%")); return false }
        if ($name('options_lname').value.length < 3) { $scope.alert($filter('translate')("%lname%") + " " + $filter('translate')("%action_length_3%")); return false }
       
        //if($name('options_password'  ).value.length < 3){$scope.alert($filter('translate')("%password%") +" "+$filter('translate')("%action_length_3%"));return false}
        else {
            $http({
                method: 'POST', url: 'ajax/ajax.php', data: $.param(data)
            }).then(function (response) {
                //console.log(response.data);
                $scope.alert($filter('translate')(response.data.record));
                $http({
                    method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'param', param: 'firstname' })
                }).then(function (response) { $scope.profile_self = response.data.record[0] });
            })
        }
    }
    $scope.edit_account_profile_main = function () {
            
        var data = { 
            ajax  : 'edit_account_profile_main',
            login : $name('options_login').value,
            password_old: $name('options_password_old').value,
            password_new: $name('options_password_new').value
        };
    if ($name('options_login').value.length < 3)        { $scope.alert($filter('translate')("%login%")    + " " + $filter('translate')("%action_length_3%")); return false }
    if ($name('options_password_new').value !== "" && $name('options_password_new').value.length < 6) { $scope.alert($filter('translate')("%password%") + " " + $filter('translate')("%action_length_6%")); return false }
    else {
            $http({
                method: 'POST', url: 'ajax/ajax.php', data: $.param(data)
            }).then(function (response) {
               
                    $scope.alert($filter('translate')(response.data.text));
                    console.log(response.data)
                
            })
    }
    }
    $scope.show_if_account = true;

    $scope.get_friends_followers = function () {
        $scope.friends_block = false;
        $scope.spinner = false;
        $scope.friends_page = false;
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'get_friends_followers', limit: 20 }), headers: { 'Auth': 'global_sesid=dmko9bdhlm6pr66p3gpj6reid3' }
        }).then(function (response) {

            console.log(response.data);
           $scope.$parent.followers = response.data.record;
           $scope.$parent.friends = response.data.record2;
           $scope.$parent.mefollow = response.data.record3;
           $scope.$parent.followers_count = response.data.record.length !== "" ? response.data.record.length : 0;
           $scope.$parent.friends_count = response.data.record2.length !== undefined ? response.data.record2.length : 0;
           $scope.$parent.mefollow_count = response.data.record3.length !== undefined ? response.data.record3.length : 0;
            $scope.spinner = true;
            $scope.friends_block = true;
            $scope.friends_page = true;

            if (response.data.record2.length < 1) {
                $scope.$parent.show_no_friends = true;
                $scope.$parent.show_no_friends_online = true;
            }
            else {
                $scope.$parent.show_no_friends = false;
                $scope.$parent.show_no_friends_online = false;
            }

        });
    }
    $scope.get_friends_followers_more = function () {
        limit1 += 10;
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'get_friends_followers', limit: limit1 })
        }).then(function (response) {

            $scope.$parent.followers = response.data.record;
            $scope.$parent.friends = response.data.record2;
            $scope.$parent.mefollow = response.data.record3;
            $scope.$parent.followers_count = response.data.record.length;
            $scope.$parent.friends_count = response.data.record2.length;
            $scope.$parent.mefollow_count = response.data.record3.length;

            if (response.data.record2.length < 1) {
                $scope.$parent.show_no_friends = true;
                $scope.$parent.show_no_friends_online = true;
            }
            else {
                $scope.$parent.show_no_friends = false;
                $scope.$parent.show_no_friends_online = false;
            }

        });
    }

    $scope.confirm_friend = function (account) {
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'confirm_friend', account: account, date: datejson() })
        }).then(function (response) {
            $scope.get_friends_followers();
            $scope.show_no_friends = false;
            //console.log(response.data);
        });
    }
    $scope.delete_friend = function (account) {
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'delete_friend', account: account })
        }).then(function (response) {
            $scope.get_friends_followers();
            //console.log(response.data);
        });
    }
    $scope.follow_out = function (account, elem) {
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'add_to_friends', requested_friend: account, date: datejson() })
        }).then(function (response) {
            $scope.get_friends_followers();
            elem.target.innerHTML = $filter('translate')(response.data.text);
            console.log(response.data)
        })
    }

    $scope.get_friends_followers();
    ///////////////////////////ACCOUNT GALLERY OPERATIONS////////////
    $scope.account_img_gal = function (elem) {
        $scope.$parent.gallerybox_spinner     = false;
        $scope.$parent.gallerybox_loaded      = false;
        $scope.comment_type       = 'image';
       $('.item').removeClass('active');
        $scope.$parent.gallery_img = $scope.account_images;
        console.log($scope.account_images);
        $timeout(function () {
            $scope.$parent.gallerybox_spinner     = true;
            $scope.$parent.gallerybox_loaded      = true;
            var data = elem.target.getAttribute('data-img-id');
            $('.item').eq(data).addClass('active');
        }, 500);
    }

    $scope.account_img_gal_saved = function (elem) {
        $scope.gallerybox_spinner     = false;
        $scope.gallerybox_loaded      = false;
         $scope.comment_type      = 'image';
        $('.item').removeClass('active');
        $scope.gallery_img = $scope.saved_images;
        $timeout(function () {
            $scope.gallerybox_spinner     = true;
            $scope.gallerybox_loaded      = true;
            var data = elem.target.getAttribute('data-img-id');
            $('.item').eq(data).addClass('active');
        }, 500);
    }
    ////////////////////////GET_ACCOUNT_IMAGES//////////////////////

    $scope.$on('get_account_images', function (event, opt) {
        //$scope.gallery_preloader = true;
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'get_account_images' })
        }).then(function (response) {

            //$scope.gallery_preloader = false;
            $scope.account_images = response.data.record;
            $scope.saved_images = response.data.record2;
            $scope.account_images_count = response.data.record.length;
            $scope.saved_images_count = response.data.record2.length;
            if (response.data.record < 1) { $scope.no_account_images = true; }
            else { $scope.no_account_images = false; }
            if (response.data.record2 < 1) { $scope.no_saved_images = true; }
            else { $scope.no_saved_images = false; }
            //console.log(response.data)

        });
    })
    $scope.get_account_images = function () {
        $scope.gallery_preloader = true;
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'get_account_images' })
        }).then(function (response) {

            $scope.gallery_preloader = false;
            $scope.account_images = response.data.record;
            $scope.saved_images = response.data.record2;
            $scope.account_images_count = response.data.record.length;
            $scope.saved_images_count = response.data.record2.length;
            if (response.data.record < 1) { $scope.no_account_images = true; }
            else { $scope.no_account_images = false; }
            if (response.data.record2 < 1) { $scope.no_saved_images = true; }
            else { $scope.no_saved_images = false; }
            //console.log(response.data)

        });
    }
    $scope.create_new_label = function () {
        $scope.spinner_create_new_label = true;
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'create_new_label', title: $name('new_label_title').value })
        }).then(function (response) {
            $scope.spinner_create_new_label = false;
            if (response.data.status === '1') {
                $scope.$broadcast('get_private_labels', {});
            }
        });
    }
    $scope.delete_label = function (id) {
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'delete_label', labelid: id })
        }).then(function (response) {
            if (response.data.status === '1') {
                $scope.$broadcast('get_private_labels', {});
            }
        });

    }
    $scope.upload_account_image = function () {

        var image = event.target.files[0];
        var reader = new FileReader();
        var data = false;

        reader.onload = function () {
            var dataURL = reader.result;
            var output = $id('upload_image_outp');
            output.src = dataURL;
        };
        reader.readAsDataURL(image);
        $('#upload_account_image_button').off('click').on('click', function (event) {
            event.preventDefault();
            $scope.preloader_block = true;
            var formdata = new FormData();
            formdata.append('ajax', 'upload_account_image');
            formdata.append('account_img', $id('upload_account_image').files[0]);
            formdata.append('title', he.encode($id('comment_text_2').value));
            formdata.append('date', datejson());
            $http({
                method: 'POST', url: 'ajax/ajax.php', headers: { 'Content-Type': undefined }, data: formdata
            }).then(function (response) {

                $scope.preloader_block = false;
                $('#upload_account_image_modal').modal('hide');

                if (response.data.status == '0') {
                    $id('output').innerHTML = $filter('translate')(response.data.text);
                }
                else {

                    $scope.get_account_images();
                }
                console.log(response.data)
            });

        })

    }
    $scope.upload_account_avatar = function () {
        var previous_image = $id('upload_avatar_preview').style.backgroundImage;
        var image = event.target.files[0];
        var reader = new FileReader();

        reader.onload = function () {
            var dataURL = reader.result;
            var output = $id('upload_avatar_preview');
            output.style.backgroundImage = "url(" + dataURL + ")";
            $('#upload_avatar_options').fadeIn(600);
        };
        reader.readAsDataURL(image);

        $('#decline_upload_avatar').on('click', function () {
            $id('upload_avatar_preview').style.backgroundImage = previous_image;
            $('#upload_avatar_options').fadeOut(600);
        })

        $('#confirm_upload_avatar').off('click').on('click', function (event) {
            event.preventDefault();
            $scope.preloader_block = true;
            var formdata = new FormData();
            formdata.append('ajax', 'upload_account_avatar');
            formdata.append('avatar_img', $id('upload_avatar_file').files[0]);

            $http({
                method: 'POST', url: 'ajax/ajax.php', headers: { 'Content-Type': undefined }, data: formdata
            }).then(function (response) {
                $scope.preloader_block = false;
                if (response.data.status == '0') {
                    $id('upload_avatar_preview').style.backgroundImage = previous_image;
                    $id('confirm_upload_avatar').innerHTML = $filter('translate')(response.data.text);
                }
                else {
                    $('#upload_avatar_options').fadeOut(600);
                }
                console.log(response.data);
            });

        })

    }
    $scope.upload_account_canvas_image = function () {
        $scope.preloader_block = true;
        html2canvas(document.querySelector('#upload_canvas_outp'), {
            onrendered: function (canvas) {

                var formdata = new FormData();
                var image = canvas.toDataURL('image/png', 1);
                $http({
                    method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: "upload_account_canvas", image: image, date: datejson() }),
                }).then(function (response) {
                    $scope.get_account_images();
                    console.log(response.data);
                    $scope.preloader_block = false;

                })
            },

        })
    }



    $scope.delete_profile = function () {
        $('#delete_profile').modal({ show: 'true' });
        $('#accept_delete_profile').on('click', function () {
            $http({
                method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'delete_profile' })
            }).then(function (response) {
                if (response.data.status == 1) {
                    document.location.href = 'index.html';
                }
                else {
                    return false;
                }
                //console.log(response.data);
            });
        })
    }

    $scope.toggle_profile = function(status)
  {
      $http({
          method: "POST",
          url   : "ajax/ajax.php",
          data  : $.param({ajax : 'toggle_profile','status': status})
      }).then(function(response){
          if(response.data.status === '1')
          {   
            $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'checksession' })
        }).then(function (response) {
            if(response.data.active === '0')
            {
             console.log("checksession_active - "+response.data.active)
             $timeout(function()
             { 
              $state.go('accountunactive') ; 
              console.log("unactivated");
             },1000) ;
            }
            else if(response.data.active === '1')
            {
               $timeout(function()
             { 
              $state.go('account.home'); 
              console.log("activated");
             },1000) ;
            } 
            })
          
          }
         
      })
  }
    $scope.labelactivation = function (event, id) {
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'labelactivation', label: id })
        }).then(function (response) {
            //console.log(response.data.record);
        });

    }


    $scope.deletecard = function (table, key) {
        $('#delete').modal({ show: 'true' });
        $('#accept_delete').on('click', function () {
            $http({
                method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'deletecards', table: table, key: key })
            }).then(function (response) {
                $('#delete').modal('hide');
                console.log(response.data.record);
                $http({
                    method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'personalcards' })
                }).then(function (response) { $scope.cardsres = response.data.record; });
            });
        })
    }
    $scope.deleteitem = function (table, key, path) {
        $('#delete').modal({ show: 'true' });
        $('#accept_delete').on('click', function () {
            $http({
                method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'deletecards', table: table, key: key, path: path })
            }).then(function (response) {
                $('#delete').modal('hide');
                console.log(response.data.record);
                $http({
                    method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'personalitems' })
                }).then(function (response) { $scope.itemres = response.data.record; });
            });
        })
    }
    $scope.cards_preview = function (elem) {
        $id('viewport').setAttribute('content', 'width=device-width, initial-scale=0.6')
        $id('cards_preview_frame').setAttribute('src', elem);
        $('#cardspreview').modal({ show: 'true' });
        $('#cardspreview').on('hidden.bs.modal', function () {
            $id('viewport').setAttribute('content', 'width=device-width, initial-scale=0.9');
        })
    }

    $http({
        method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'labelstatistic' })
    }).then(function (response) {
        //console.log(response.data);
        $scope.statistic = response.data.record;
    });

    $scope.activity_more = function () {
        $scope.preloader2 = true;
        limit1 += 10;
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'activity', limit: limit1 })
        }).then(function (response) {
            $scope.activity = response.data.record;
            $scope.preloader2 = false;
            console.log(response.data);
        });
    }


    var i = 0;
    $scope.cardtype = "CARDS"
    $scope.swcards = true;
    $scope.switcher1 = function (elem) {
        i = i + 180;
        elem.currentTarget.style.transform = "rotate(" + i + "deg)";
        if ($scope.switems !== true) {
            $scope.cardtype = "ITEMS"
            $scope.switems = true;
            $scope.swcards = false;

        }

        else {
            i = 0;
            $scope.switems = false;
            $scope.swcards = true;
            $scope.cardtype = "CARDS"
        }


    }

    ////////////////////////Get_PRIVATE_Labels
    $scope.$on('get_private_labels', function () {
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'getlabels_private', quantity: 'notone' })
        }).then(function (response) {

            $scope.label_private = response.data.record;
            //console.log(response.data);
            if (response.data.record.length < 1 || response.data.record == undefined) {
                $scope.no_labels = true;
            }
            else {
                $scope.no_labels = false;
            }
        });



    })

    $scope.$broadcast('get_private_labels', {});

})


