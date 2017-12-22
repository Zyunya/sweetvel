com.controller('sketchctr', function ($scope,$rootScope, $http, $location, $filter, $window, $timeout) {
    
  /////////////////////////////BROADCAST////////////////////////////////  
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

///////////////////////////////////////////////////
    $http.defaults.headers.post["Content-Type"] = "application/x-www-form-urlencoded";


    var sketch = $location.path().split("/")[3];
    $scope.sketch_param = $location.path().split("/")[3];

    $scope.sketch_info = function (id) {
        $scope.sketch_block_loading = false;
        $scope.sketch_block_loading_spinner = false;
        $http({
            method: 'POST',
            url: 'ajax/ajax.php',
            data: $.param({ ajax: 'sketch_info', sketch: id }),
            beforeSend: function () {

            },
            complete: function (response) {
                if(response.data.status === '1')
                {
                $scope.sketch_block_loading         = true;
                $scope.sketch_block_loading_spinner = true;
                $scope.profile_sketch = response.data.record[0];
                console.log(response.data);
                }
            }
        })
    }
    $scope.get_friends_followers_sketch = function (id) {
        $scope.friends_block = false;
        $scope.spinner = false;
        $scope.friends_page = false;
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'get_friends_followers', sketch: id, limit: 20 })
        }).then(function (response) {
            //console.log(response.data);
            $scope.$parent.followers = response.data.record;
            $scope.$parent.friends = response.data.record2;
            $scope.$parent.mefollow = response.data.record3;
            $scope.$parent.followers_count = response.data.record.length;
            $scope.$parent.friends_count = response.data.record2.length;
            $scope.$parent.mefollow_count = response.data.record3.length;
            $scope.friends_block = true;
            $scope.spinner = true;
            $scope.friends_page = true;

            if (response.data.record2.length < 1) {
                $scope.$parent.show_no_friends = true;
                $scope.$parent.show_no_friends_online = true;
            }
            else {
                $scope.$parent.show_no_friends = false;
                $scope.$parent.show_no_friends_online = false;
            }
            //console.log(response.data);
        });
    }

    $scope.get_friends_followers_more = function () {
        limit1 += 10;
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'get_friends_followers', sketch: sketch, limit: limit1 })
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
    $scope.add_to_friends = function (elem) {
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'add_to_friends', requested_friend: sketch, date: datejson() })
        }).then(function (response) {

            //$scope.alert_show(response.data.text);
            elem.target.innerHTML = $filter('translate')(response.data.text);
            console.log(response.data)
        })
    }
    //$scope.friend_status_text = "%unfollow%";

    $scope.check_friend_status = function (friend) {
        var friend = friend || $location.path().split("/")[3];
        $scope.friend_status_preloader = true;
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'check_friend_status', requested_friend: friend })
        }).then(function (response) {
            $scope.friend_status_preloader = false;
            $scope.friend_status_text = response.data.text;
            $scope.friend_status_status = response.data.status;
            console.log(response.data);
        })
    }

    $scope.sketch_info(sketch);
    $scope.get_friends_followers_sketch(sketch);
    $scope.check_friend_status(sketch);

    //////////////////////////SKETCH_GALLERY_OPTIONS////////////////////////
    $scope.account_img_gal = function (elem) {
        $scope.gallerybox_loaded = false;
        $scope.gallerybox_spinner = false;
         $scope.comment_type       = 'image';
        $('.item').removeClass('active');
        $scope.gallery_img = $scope.account_images;
        $timeout(function () {
            $scope.gallerybox_loaded = true;
            $scope.gallerybox_spinner = true;
            var data = elem.target.getAttribute('data-img-id');
            $('.item').eq(data).addClass('active');
        }, 500);
    }
    $scope.account_img_gal_saved = function (elem) {

        $scope.gallerybox_loaded = false;
        $scope.gallerybox_spinner = false;
         $scope.comment_type       = 'image';
        $('.item').removeClass('active');
        $scope.gallery_img = $scope.saved_images;

        $timeout(function () {
            $scope.gallerybox_loaded = true;
            $scope.gallerybox_spinner = true;
            var data = elem.target.getAttribute('data-img-id');
            $('.item').eq(data).addClass('active');
          
        }, 500);
    }
    //////////////////////////////GET_SKETCH_IMAGES//////////////////////////
    $scope.get_sketch_images = function () {
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'get_account_images', sketch: sketch })
        }).then(function (response) {
            $scope.account_images = response.data.record;
            $scope.saved_images = response.data.record2;
            $scope.account_images_count_sketch = response.data.record.length;
            $scope.saved_images_count_sketch = response.data.record2.length;
            $scope.preloader_gallery = true;
            if (response.data.record < 1) { $scope.no_account_images = true; }
            else { $scope.no_account_images = false; }
            if (response.data.record2 < 1) { $scope.no_saved_images = true; }
            else { $scope.no_saved_images = false; }
            //console.log(response.data)
        });
    }

   $scope.get_video = function(){
        $http({
        method : 'POST',
        url    : 'ajax/ajax.php',
        data   : $.param({ajax : 'get_video',sketch : sketch,limit : 10 })
    }).then(function(response){
    $scope.video_list = response.data.record;
    })
}
 $scope.get_video();
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
    $scope.send_message_sketch = function () {
        var receiver = sketch * hash;
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'sendmessage', message: he.encode($id('send_to_sketch_textarea').value), receiver: receiver, date: datejson() })
        }).then(function (response) {
            console.log(response.data);
            $id('send_to_sketch_textarea').value = "";
            $id('send_to_sketch_block').classList.add('dp')
        })
    }

    ////////////////////////SKETCH TIMELINE//////////////////////////////////////////////////////////////
    $scope.timeline = function () {
        $scope.timeline_show    = false;
        $scope.show_no_timeline = false; 
        $http({ method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'timeline', receiver: $location.path().split("/")[3], limit: 10,object_type : 'account_timeline' }) })
            .then(function (response) {
                try {
                    if (response.data.record !== undefined) {
                        $scope.timeline_preloader = true;
                        $scope.timeline_show      = true;
                        $scope.timeline = response.data.record;
                        if (response.data.record.length > 9) {
                            $scope.arrows = true;
                        }
                    
                        if($scope.timeline < 1)
                        {
                         $scope.show_no_timeline = true;
                        }
                        else{
                         $scope.show_no_timeline = false;
                        }
                    }
                    else {
                        throw ('no_availible_data');
                    }
                }
                catch (err) {
                    console.log(err)
                }
            })
    }
    $scope.timeline();
    $scope.more_timeline = function(opt) {
        limit1 += 10;
        $http({ method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'timeline',receiver : opt.owner_id, limit: limit1,object_type : opt.object_type  }) })
            .then(function (response) { 
                $scope.timeline = response.data.record;   
            })
          }

    ///////////////////////////////TIMELINE_COMMON_MESSAGE////////////////////////
    $scope.sendmessage_timeline = function () {
      
        var formdata = new FormData();
        var file     = $id('timeline_file').files[0];
        var files    = $id('timeline_file').files;
        formdata.append('ajax', 'sendmessage_timeline');
        formdata.append('message', he.encode($id('timeline_text').value));
        formdata.append('receiver', $location.path().split("/")[3]);
        formdata.append('object_type', 'account_timeline');
        formdata.append('date', datejson());
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
                //alert(receiver);
                $scope.messageval = $id('timeline_text').value;
                $scope.$broadcast('timeline_update', {receiver : sketch,type : 'account_timeline'});
                if (response.data.status == '0') {
                    $scope.alert_show(response.data.text);
                }
                if($scope.timeline < 1)
                        {
                         $scope.show_no_timeline = true;
                        }
                        else{
                         $scope.show_no_timeline = false;
                        }
                $id('timeline_text').value = "";
                //Make Empty input file and array with files
                $id('timeline_file').value = "";
                filearr_message = filearr_message.slice(0, 0);

                $id('upload_timeline_img_preview_block').classList.add('dp');
                $('.message_img_prev').remove();
            }
        })
    }
    ///////////////////////TIMELINE_OPENGRAPH_MESSAGE///////////////////////////
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
                                data: $.param({ ajax: 'sendmessage_timeline_og', receiver: $location.path().split("/")[3], title: title,video :video, desc: desc, og_desc: og_desc, url: url, image: image, date: datejson(),object_type : 'account_timeline' })
                            })
                                .then(function (response2) {
                                    $scope.messageval = $id('timeline_text').value;
                                     $scope.$broadcast('timeline_update', {receiver : sketch,type : 'account_timeline'});
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
    ///////////////////////TIMELINE_VOICE_MESSAGE///////////////////////////
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
                        formdata.append('receiver', $location.path().split("/")[3]);
                        formdata.append('voice', blob);
                        formdata.append('date', datejson());
                        formdata.append('object_type','account_timeline');
                        $http({
                            method: 'POST',
                            url: 'ajax/ajax.php',
                            data: formdata,
                            headers: { 'Content-Type': undefined }
                        }).then(function (response) {
                             $scope.$broadcast('timeline_update', {receiver : sketch,type : 'account_timeline'});
                            console.log(response.data);
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

})

