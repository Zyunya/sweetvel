var com = angular.module('com', ['ui.router', 'ngAnimate', 'translation','ngCookies']);
var privatelabel = angular.module('privatelabel', []);
var creategc = angular.module('creategc', ['translation', 'com']);

//var eventapp = angular.module('event'  , ['locconfig'  , 'com'], function ($locationProvider) { $locationProvider.html5Mode(true); });
//var account  = angular.module('account', ['translation', 'com', 'ngAnimate']);
//var sketch = angular.module('sketch' , ['translation', 'com', 'locconfig']);

var swcard = angular.module('swcard', ['translation', 'locconfig']);
var switem = angular.module('switem', ['translation', 'locconfig']);

var alertbox = document.getElementsByClassName('alert_output');
var ringer = new Audio('audio/swaudio/ringer.mp3');
var hash = 16 * 3 * 1992;
var outp = "";
var limit1 = 10;
var limit20 = 20;
var limittest = 2;
var filearr_message = [];
var current_state = '';
var host_prefix = 'http://';
var host = window.location.protocol + '//' + window.location.hostname;
var socket = "";
var error_img = host + '/img/media/imgerror.jpg';
var sw_img = host + '/img/media/icon1.png';
var socket_adress = 'https://sweetvel.com:4000';


angular.module('locconfig', []).config(['$locationProvider', function ($locationProvider) {

    if (window.history && window.history.pushState) {
        $locationProvider.html5Mode({
            enabled: true,
            requireBase: true,
            rewriteLinks: false
        });
    }
    else {
        $locationProvider.html5Mode(false);
    }
}]);


/////////////////////////INDEX PAGE/////////////////////////////

com.controller('indexctr', function ($scope, $http, $filter, $state) {


    $http.defaults.headers.post["Content-Type"] = "application/x-www-form-urlencoded";
    $scope.sendcallback = function () {
        var name = $name('com_name').value;
        var email = $name('com_email').value;
        var comment = $name('com_comment').value;
        var buttonval = $name('com_send');
        var data = { ajax: 'comment', com_name: name, com_email: email, com_comment: comment, time: datejson() };

        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param(data)
        }).then(function (response) { buttonval.innerHTML = $filter('translate')(response.data.record) });

    }

    $scope.sw_login = function () {
        var login = $name('login').value;
        var password = $name('password').value;
        var data = { ajax: 'sw_login', log: login, pass: password };

        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param(data)
        }).then(function (response) {
            if (response.data.status == '1') {
                var id = response.data.id;
                // document.location.href = 'account'+id+'#/profile';
                $state.go('account.home');
            }
            else {
                $name('login_outp').innerHTML = $filter('translate')(response.data.text);
                // return false;
            }
        });

    }
    $scope.fb_login = function () {
        $('#accept_terms_modal').modal({ show: 'true' });
        $('#accept_terms').on('click', function () {
            FB.login(function (response) {
                if (response.status === 'connected') {
                    FB.api('me?fields=id,name,first_name,last_name,gender,verified,email', function (response) {
                        //alert(response.first_name +" "+response.last_name+" " + response.email+" "+response.verified+" "+response.gender);
                        var avatar = "https://graph.facebook.com/" + response.id + "/picture?type=large";
                        var firstname = response.first_name;
                        var lastname = response.last_name;
                        var login = response.email;
                        var gender = response.gender;
                        if (response.verified == true) {
                            $.ajax({
                                type: 'POST',
                                url: 'ajax/ajax.php',
                                data: {
                                    ajax: 'fb_vk_signin',
                                    firstname: firstname,
                                    lastname: lastname,
                                    login: login,
                                    gender: gender,
                                    avatar: avatar,
                                    date: datejson(),
                                    regtype: 'fb'
                                },
                                success: function (data) {
                                    console.log(data);
                                    $state.go('account.home');
                                },
                                error: function (err) { console.log(err); }
                            })
                        }
                        else { return false; }

                    });
                    $('#accept_terms_modal').modal('hide');
                } else if (response.status === 'not_authorized') {
                    // The person is logged into Facebook, but not your app.
                } else {
                    // The person is not logged into Facebook, so we're not sure if// they are logged into this app or not.
                }
            });
        })
    }
    $scope.vk_login = function () {
        function authInfo(response) {
            if (response.status == 'connected' && response.session.user !== undefined) {

                VK.Api.call('users.get', { uid: response.session.mid, fields: "sex,photo_big" }, function (r) {
                    var firstname = r.response[0].first_name;
                    var lastname = r.response[0].last_name;
                    var avatar = r.response[0].photo_big;
                    var gender = r.response[0].sex == 2 ? "male" : "female";
                    var login = response.session.user.id;

                    $.ajax({
                        type: 'POST',
                        url: 'ajax/ajax.php',
                        data: {
                            ajax: 'fb_vk_signin',
                            firstname: firstname,
                            lastname: lastname,
                            login: login,
                            gender: gender,
                            avatar: avatar,
                            date: datejson(),
                            regtype: 'vk'
                        },
                        success: function (data) {
                            $state.go('account.home');
                        },
                        error: function (err) { console.log(err); }
                    })
                });
            }
            else {
                //console.log("Vk_auth_error");

            }
        }
        $('#accept_terms_modal').modal({ show: 'true' });
        document.getElementById('accept_terms').addEventListener('click', function () {
            //VK.Auth.getLoginStatus(authInfo);
            VK.Auth.login(authInfo);
            $('#accept_terms_modal').modal('hide');
        })

    }
    $scope.sw_sign_up = function () {
        var login = $name('sign_up_login').value;
        var pass1 = $name('sign_up_pass1').value;
        var pass2 = $name('sign_up_pass2').value;
        var fname = $name('sign_up_fname').value;
        var lname = $name('sign_up_lname').value;
        var gender = $name('sign_up_gender').value;
        var data = { ajax: 'sign_up', login: login, password1: pass1, password2: pass2, fname: fname, lname: lname, gender: gender };

        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param(data)
        }).then(function (response) {
            $id('sign_up_outp').innerHTML = $filter('translate')(response.data.text);
            console.log(response.data);
        });

    }

})



/////////////////////////COMMON////////////////////
com.run(function ($rootScope, $http, $location, $compile, $filter, $timeout, $sce, $transitions, $state, $stateParams,$cookies,$document) {

    $transitions.onSuccess({}, function ($transitions, event) { $('.modal').modal('hide'); });
    

    current_state = $state.$current;
    $rootScope.host_prefix = host_prefix;
    $rootScope.host = host;


    //////////////////////////////EMOJI////////////
    $rootScope.emoji_toggle = function () {
        $id('emoji_block').classList.toggle('dp');
    }
    $rootScope.emoji_toggle_arg = function (id) {
        $id(id).classList.toggle('dp');
    }
    $rootScope.show_hide = function (id) {

        $id(id).classList.toggle('dp');
    }
    $rootScope.dp = function (elem) {
        elem.target.parentElement.classList.toggle('dp');
    }
    $rootScope.put_uri = function (param, param2) {
        $location.search({ ps: param.replace(/\s/g, ""), ow: param2 * hash });
        //alert($location.search().ps);
    }
    $rootScope.put_uri2 = function (param) {
        $location.path(param);
    }
    $rootScope.put_uri3 = function (param, param2) {
        $location.search({ name: param, type: param2 });
    }
    var label_number = $location.path().split("/")[3];
    $http.defaults.headers.post["Content-Type"] = "application/x-www-form-urlencoded";
    $http({
        method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'getmetrics' })
    }).then(function (response) { $rootScope.metricsres = response.data.record });
    /////////////////////////////PROFILE INFO//////////////////
    $rootScope.profileinfo = function () {
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'param', param: 'firstname' })
        }).then(function (response) {
            // console.log(response.data.record[0].id == "");
            //console.log(response.data);
            $rootScope.profile = response.data.record[0];

        });
    }
    $rootScope.logout = function () {
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'logout' })
        }).then(function (response) {
            //console.log(response.data);
            if (response.data.status === '1') {
                $timeout(function () { $state.go('index') }, 1000);
            }
            else if (response.data.status === '0')
            {
                 $timeout(function () { $state.go('index') }, 1000);
            }
            else {
                return false;
                console.log(response.data.text);
            }
        });
    }



    $rootScope.mutual_observer = function () {

        $rootScope.$watch(function () {

            var observer = new MutationObserver(function (mutations) {
                mutations.forEach(function (mutation) {
                    if (mutation.addedNodes)
                        window.componentHandler.upgradeElements(mutation.addedNodes);
                })
            });
            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        })
    }
    //$rootScope.mutual_observer();
    ////////////////////////SET ONLINE STATUS /////////////
    $rootScope.online = function (status) {
        var device = mobilecheck2() ? 'mobile' : 'desktop';
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'online', date: datejson(), device: device, status: status })
        }).then(function (response) {
            $rootScope.label = response.data.record;
            console.log(response.data);
        });
    }
    $rootScope.save_video = function (link, url, index) {
        $http({
            method: "POST",
            url: "ajax/ajax.php",
            data: $.param({ ajax: "save_video", link: link, url: url, date: datejson() })
        }).then(function (response) {
            if (response.data.status === '1') {
                $('.status_text').eq(index).text($filter('translate')(response.data.text));
            }
            console.log(response.data);
        })
    }
    //////////////////////////////GALLERY_ACTIONS///////
    $rootScope.label_img_gal = function (elem) {

        $('.item').removeClass('active');

        $rootScope.gallerybox_loaded = false;
        $rootScope.gallerybox_spinner = false;
        var ider = $location.path().split('/')[3];
        $rootScope.comment_type = 'image';
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: "labelimages", label: ider, status: 'null' }),
        }).then(function (response) {
            $rootScope.gallery_img = response.data.record;
            //console.log(response.data.record);
            setTimeout(function () {
                $rootScope.gallerybox_spinner = true;
                $rootScope.gallerybox_loaded = true;
                var data = elem.target.getAttribute('data-img-id');
                $('.item').eq(data).addClass('active');
            });
        });
    }
    $rootScope.timeline_img_gal = function (elem, images) {

        $rootScope.gallery_loaded = false;
        $rootScope.gallerybox_spinner = false;
        $rootScope.comment_type = 'timeline';
        $('.item').removeClass('active');
        var obj = [];
        if (images.length == 0) {
            obj = [{ id: '0', image: sw_img }];
        }
        else {
            for (var i = 0; i < images.length; i++) {
                obj[i] = { id: i.toString(), image: images[i] };
            }
        }
        $rootScope.label_posts_img = obj;
        $timeout(function () {

            $rootScope.gallerybox_spinner = true;
            $rootScope.gallery_loaded = true;
            var data = elem.target.getAttribute('data-img-id');
            $('.item_inner .item').eq(data).addClass('active');

        }, 500);
    }
    $rootScope.post_img_gal = function (elem, obj) {
        $rootScope.gallery_loaded = false;
        $rootScope.gallerybox_spinner = false;
        $rootScope.comment_type = 'post';
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'getobjectimages', limit: 'nolimit', path: "globalimg/post_images/", post: obj.id })
        }).then(function (response) {
            $rootScope.label_posts_img = response.data.record;
            console.log(response.data.record)
            $timeout(function () {
                $rootScope.gallery_loaded = true;
                $rootScope.gallerybox_spinner = true;

                var data = elem.target.getAttribute('data-img-id');
                $('.posts_img.item').eq(data).addClass('active');
            });
        });
    }
    $rootScope.feed_img_gal = function (elem) {
        $rootScope.gallery_loaded = false;
        $rootScope.gallerybox_spinner = false;
        $rootScope.comment_type = 'image';
        var obj = {};
        obj = [{ id: '0', image: elem }];
        console.log(obj);
        $rootScope.label_posts_img = obj;
        $timeout(function () {
            $('.item').eq(0).addClass('active');
            $rootScope.gallerybox_spinner = true;
            $rootScope.gallery_loaded = true;
        })
    }



    $rootScope.delete_message_timeline = function (event, id, index, type, path) {

        $http({ method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'delete_message_timeline', messageid: id, object_type: type, path: path }) })
            .then(function (response) {
                console.log(response.data);
                if (response.data.status === "1") {
                    $('.delete_timeline_post').eq(index).slideUp();
                }
            })
    };


    $rootScope.timeline_play = function (elem) {
        var audio = elem.target.nextElementSibling;
        var allaudio = document.getElementsByTagName('audio');

        for (var i = 0; i < allaudio.length; i++) {
            allaudio[i].pause();
            allaudio[i].previousElementSibling.classList.remove('fa-pause-circle');
            allaudio[i].previousElementSibling.classList.add('fa-play-circle');
        }
        if (audio.currentTime < 1) {
            audio.play();
            elem.target.classList.add('fa-pause-circle');
            elem.target.classList.remove('fa-play-circle');
        }
        else {
            audio.pause();
            elem.target.classList.add('fa-play-circle');
            elem.target.classList.remove('fa-pause-circle');
            audio.currentTime = 0;
        }
        audio.addEventListener("ended", function () {
            elem.target.classList.remove('fa-pause-circle');
            elem.target.classList.add('fa-play-circle');
            audio.currentTime = 0;
        })
    }



    $rootScope.hasGetUserMedia = function () {
        try {

            return !!(navigator.mediaDevices.getUserMedia || navigator.mediaDevices.getUserMedia ||
                navigator.mediaDevices.getUserMedia || navigator.mediaDevices.getUserMedia);
        }
        catch (err) {
            console.log(err);
        }
    }

    $rootScope.audio_recorder_option_timeline = function () {
        if ($rootScope.hasGetUserMedia()) {

            $id('upload_timeline_audio_preview').classList.remove('dp');
        }
        else {
            $rootScope.alert_show("%action_browser_record_no_support%");
        }
    }

    $rootScope.message_preview_timeline = function (elem) {
        var block = $id('upload_timeline_img_preview_block');
        var filer = $id('timeline_file').files;

        if (filearr_message.length < 3 && filer.length < 4) {
            var filearr0 = Array.from(filer);       ////////Files in input for One Operation
            filearr_message = filearr_message.concat(filearr0);//////All files From input for all operations
            block.classList.remove('dp');

            for (var i = 0; i < filearr0.length; i++) {
                var url = URL.createObjectURL(filearr0[i]);
                var div = document.createElement('div');
                var itag = document.createElement('i');
                itag.className = "pull-right  fa fa-times-circle-o  fa-2x textshgrt color5 iconhov cursor";
                div.className = 'message_img_prev';

                div.appendChild(itag);
                div.style.backgroundImage = "url('" + url + "')";
                div.style.width = '100px';
                div.style.height = '100px';
                div.style.backgroundSize = 'cover';
                div.style.backgroundPosition = 'center';
                div.style.display = 'inline-block';
                div.style.margin = '0.15%';
                if (elem.files[i].type.match("image/jpg|image/png|image/jpeg")) {
                    block.appendChild(div);

                    if (filearr0.length == 1) {
                        itag.setAttribute('fileid', filearr_message.length - 1);  ////file_message = filearr_message.length + one new filearr0
                    }
                    if (filearr0.length > 1) {
                        itag.setAttribute('fileid', filearr_message.length - filearr0.length + i);
                    }
                }
                else {
                    $rootScope.alert_show('%action_type%');
                }
                filearr_message.onload = function () {   //img.onload
                    URL.revokeObjectURL(filearr_message); //img.src
                }
                itag.onclick = function (elem) {
                    var fileid = this.getAttribute('fileid');
                    this.parentElement.style.display = 'none';
                    delete filearr_message[fileid];
                }
            }
        }

        else {
            $rootScope.alert_show('%3files%');
        }

    }

    //////////////////////////INVITE_TO_CHAT_COMMON/////////////
    $rootScope.invite_to_chat_common = function (receiver_id) {
        var invitation = 'Welcome To Chat'
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'sendmessage', message: invitation, receiver: receiver_id * hash, date: datejson() })
        }).then(function (response) {
            $rootScope.messengerer();
        })
    }
    //////////////////////////////////USER_CARD//////////////////////////
    $rootScope.user_card_info_block = function (user) {
        $('#user_card').modal({ show: 'true' });
        $rootScope.preloader1 = true;
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'user_card', user: user })
        }).then(function (response) {
            $rootScope.preloader1 = false;
            $rootScope.resultblock_userinfo = true;
            $rootScope.user_card_info = response.data.record;
            $rootScope.user_card_user = response.data.record[0];
            //console.log(response.data);
        });
    }

    ///////////////////SAVE_IMAGE/////////////////

    $rootScope.save_img = function (elem) {
        var img = elem;
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'save_img', img: img, date: datejson() })
        }).then(function (response) {
            $rootScope.$broadcast('get_account_images', {});

        })
    }

    /////////////////////HOST///////////
    $rootScope.personalhost = 'localhost';
    ////////////////////////////////FOLLOW///////////////////////////////

    $rootScope.follow = function () {
        $rootScope.follow_loader = true;
        var followed = $location.path().split('/')[3];
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'follow', followed: followed, date: datejson() })
        }).then(function (response) {
            $rootScope.follow_status0 = $filter('translate')(response.data.status0);
            $rootScope.follow_status1 = response.data.status1;

            $rootScope.follow_loader = false;
            //console.log(response.data.status0);
        });
    }
    $rootScope.follow_sweetvel = function (sw) {
        $rootScope.follow_loader = true;
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'follow', followed: sw, date: datejson() })
        }).then(function (response) {
            $rootScope.follow_status0 = $filter('translate')(response.data.status0);
            $rootScope.follow_status1 = response.data.status1;
            $rootScope.follow_loader = false;
            //console.log(response.data.status0);
        });
    }
    $rootScope.checkfollow_sweetvel = function (sw) {
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'checkfollow', followed: sw })
        }).then(function (response) {
            $rootScope.follow_status0 = $filter('translate')(response.data.status0);
            $rootScope.follow_status1 = response.data.status1;

            //console.log(response.data);
        });
    }
    $rootScope.checkfollow = function () {
        var followed = $location.path().split('/')[3];
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'checkfollow', followed: followed })
        }).then(function (response) {
            $rootScope.follow_status0 = $filter('translate')(response.data.status0);
            $rootScope.follow_status1 = response.data.status1;
            //console.log(response.data);
        });
    }


    ///////////////////////////////POSTS//////////////////////////////////
    $rootScope.search = function (elem) {

        $rootScope.show_search_box = false;
        $rootScope.search_preloader = true;
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'search', search: elem.target.value })
        }).then(function (response) {
            $rootScope.search_preloader = false;
            $rootScope.show_search_box = true;
            $rootScope.searcher = response.data.record;
            $rootScope.searchertags = response.data.record2;
            $rootScope.searcherpersons = response.data.record3;
            //console.log(response.data);
        });
        return false;
    }

    $rootScope.close_modal = function (elem) {
        $id(elem).style.display = 'none';
    }


    $rootScope.label_search_img = function (post) {
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'getobjectimages', limit: 'limit', path: "globalimg/label_images/", post: post.id, })
        }).then(function (response) {
            post.labelsearchimg = response.data.record;
            //console.log(response.data);
        });
    }
    ///////////////////////////////////////POST LIKERS////////////////////

    $rootScope.who_liked_post = function (table, event) {
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'statistic_persons', table: table, id: event.id, object_type: event.object_type, limit: 20 })
        }).then(function (response) {
            console.log(response.data);
            $rootScope.statisticperson_likes = response.data.record;
            if (response.data.record.length > 19) {
                $rootScope.show_more = true;
            }
        })
    }
    $rootScope.get_more_likers = function (event) {
        limit1 += 20;
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'statistic_persons', table: 'likes', id: event.object_id, object_type: 'post', limit: limit1 })
        }).then(function (response) {
            //console.log(response.data);
            $rootScope.statisticperson_likes = response.data.record;
        })
    }
    ////////////////////////////////////////COMMENTS///////////////////////////////////////////////////
    $rootScope.htmlenc = function (str) {
        return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }
    $rootScope.send_comment = function (type, input) {

        var ow = $location.search().ow;
        var id = $location.search().ps;
        var text = $id(input).value;
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({
                ajax: 'send_comment',
                object_owner: ow,
                object_type: type,
                object_id: id,
                text: he.encode(text),
                date: datejson()
            })
        }).then(function (response) {
            $rootScope.get_comments(id, type);
            $id(input).value = '';
            //setTimeout(function(){$('.comments_body').scrollTop($('.comments_body')[0].scrollHeight);},1000);
            //  console.log(response.data);
        });
    }

    $rootScope.get_comments = function (post, type) {
        $rootScope.show_get_more = false;
        $rootScope.comments_loaded = false;
        $rootScope.comments_loading = true;
        var id = $location.search().ps;
        //var text = $id('comment_text').value;
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'get_comments', object_id: post, object_type: type })
        }).then(function (response) {
            if (response.data.record.length > 19) {
                $rootScope.show_get_more = true;
            }
            $rootScope.comments_loaded = true;
            $rootScope.comments_loading = false;
            $rootScope.comments = response.data.record;
            $rootScope.comments_count = response.data.comments;
            $rootScope.object_type = response.data.object_type;

            console.log(response.data);

        });
    }
    $rootScope.get_comments_more = function (type) {
        limit20 += 20;

        var id = $location.search().ps;
        //var text = $id('comment_text').value;
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'get_comments', object_id: id, object_type: type, limit: limit20 })
        }).then(function (response) {

            $rootScope.show_comments_body = true;
            $rootScope.comments = response.data.record;
            $rootScope.comments_count = response.data.comments;
            console.log(response.data);
        });
    }
    $rootScope.delete_comment = function (index, comment, type, owner_status) {
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'delete_comment', comment: comment, object_type: type, object_owner: owner_status })
        }).then(function (response) {
            $('.comment_deleted_pointer_' + index).slideUp();
            //console.log(response.data);
        });
    }

    /////////////////////////////////////VIEWS////////////////////////////////////////////
    $rootScope.views = function (kind) {
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'views', label: label_number, kind: kind, date: datejson() })
        }).then(function (response) {
            //console.log(response.data);
            $rootScope.views_count = response.data.views;
        })
    }



    $rootScope.likes_argument = function (type, arg) {
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({
                ajax: 'likes',
                object_id: arg.id,
                account_receiver: arg.ownerid,
                object_type: type,
                date: datejson()
            })
        }).then(function (response) {
            //console.log(response.data);
            arg.likes_countarg = parseInt(arg.likes_countarg) + parseInt(response.data.statuslike);
        })
    }
    $rootScope.give_like = function (arg) {
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({
                ajax: 'likes',
                object_id: arg.id,
                account_receiver: arg.owner_id,
                object_type: arg.object_type,
                date: datejson()
            })
        }).then(function (response) {
            //console.log(response.data);
            arg.likes_countarg = parseInt(arg.likes_countarg) + parseInt(response.data.statuslike);
        })
    }

    $rootScope.likestatus_argument = function (arg) {
        $rootScope.preloader_likes = true;
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'likestatus', object_id: arg.id, object_type: arg.object_type, date: datejson() })
        }).then(function (response) {
            $rootScope.preloader_likes = false;
            $rootScope.like_preloader = true;
            arg.likestatusarg  = response.data.likestatus;
            arg.likes_countarg = response.data.likes_count;
            console.log(response.data);
            //console.log("likestatus "+response.data.likestatus+" countlikes"+response.data.likes_count);
        })
    }
    $rootScope.changeicon = function ($event, icon1, icon2, color1, color2) {

        $event.target.classList.toggle(icon1);
        $event.target.classList.toggle(icon2);
        $event.target.classList.toggle(color1);
        $event.target.classList.toggle(color2);

    }

    //////////////////////////////////LIKES/////////////////////////////
    $rootScope.wholiked = function () {
        $http({ method: 'POST', url: 'ajax/ajaxlive.php?ajax=wholiked' })
            .then(function (response) {
                $('#live_popup1').fadeIn(1000).delay(3000).fadeOut(500);

                $rootScope.who_liked = response.data.record;
                //console.log(response.data.record);
            })
    }
    $rootScope.wholiked_post = function () {
        $http({ method: 'POST', url: 'ajax/ajaxlive.php?ajax=wholiked_post' })
            .then(function (response) {
                $('#live_popup1').fadeIn(1000).delay(3000).fadeOut(500);

                $rootScope.who_liked = response.data.record;
                ringer.play();
            })
    }

    $rootScope.get_themes = function () {
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'gettheme' })
        }).then(function (response) {
            $rootScope.themeres = response.data.record;
            //console.log(response.data)
        });
    }
    $rootScope.get_themes();
    /////////////////////////////////////////LIVE_COMMON_ACTIVITY/////////////////////
    $rootScope.activity_account = function (refresh) {
        if (refresh == 'refresh') {
            $rootScope.live_common_refresh();
            $rootScope.show_checkupdates_counter = false;
            $rootScope.show_live_common = true;
        }
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'activity', limit: 20 })
        }).then(function (response) {
            console.log(response.data);
            $rootScope.activity = response.data.record;
            $rootScope.activity_param = response.data.record[0];
            if (response.data.record.length == 0) {
                $rootScope.show_activity_feed = true;
            }
            else { $rootScope.show_activity_feed = false; };
            if (response.data.record.length > 19) {
                $rootScope.show_get_more = true;
            }

        });
    }

    $rootScope.live_common_refresh = function () {
        $http({
            method: 'POST', url: 'ajax/ajaxlive.php?ajax=live_common_refresh'
        }).then(function (response) {
            document.getElementById('live_pointer').style.color = 'rgba(118, 188, 28, 1)';
            $rootScope.show_checkupdates_counter = false;
            //console.log(response.data);
        });
    }
    $rootScope.live_common_checkupdates = function () {
        $http({
            method: 'POST', url: 'ajax/ajaxlive.php?ajax=live_common_checkupdates'
        }).then(function (response) {
            if (response.data.status == 1) {
                document.getElementById('live_pointer').style.color = '#ff5959';
                $rootScope.show_checkupdates_counter = true;
                $rootScope.checkupdates_counter = response.data.quantity;
                //console.log(response.data.quantity);
            }
            //console.log(response.data);
        });
    }
    ////////////MAIN LIVE ACTIVITY

    $rootScope.live_common = function () {
        var last_data = "";
        if (typeof (EventSource) !== "undefined") {
            var source = new EventSource("ajax/ajaxlive.php?ajax=live_common");
            source.onmessage = function (event) {
                var datar = JSON.parse(event.data);
                var update = datar.update;
                var quantity = datar.quantity;

                if (update !== last_data && last_data && update > last_data) {
                    $rootScope.activity_account();
                    //$rootScope.get_friends_followers();
                    $('#live_popup3').fadeIn(1000);
                    $rootScope.show_checkupdates_counter = true;
                    //$timeout(function () {$rootScope.show_checkupdates_counter           = false;}, 100);
                    document.getElementById('live_pointer').nextElementSibling.innerHTML = quantity;
                    document.getElementById('live_pointer').style.color = '#ff5959';
                }
                last_data = update;
            }
        }
    }
    $rootScope.live_common();

    $rootScope.closegal = function (mask) {
        $(mask).fadeOut(0);
        $('.item').removeClass('active');

        return false;
    }

    ////////////////////////////////GALLERY/////////////////////////////////////////

    $rootScope.delete_image = function (id, file, type, table) {
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: "delete_image", table: table, type: type, id: id, file: file }),
        }).then(function (response) {
            console.log(response.data);
            if (response.data.status === '1') {

                switch (type) {
                    case 'image': $rootScope.$broadcast('get_account_images', {}); break;
                    case 'labelimage': $rootScope.$broadcast('get_label_images', {}); break;
                }
                $cls('delete_pointer_text_' + id).classList.remove('dp');
                $cls('delete_pointer_' + id).classList.add('imgdark');
            }
        });
    }





    /////////////////MESSENGER LIVE////////////////

    $rootScope.messanger_statusn = function () {
        var last_data = "";
        var last_status = "";
        var last_unread = "";
        if (typeof (EventSource) !== "undefined") {
            var source = new EventSource("ajax/ajaxlive.php?ajax=live_chat");

            source.onmessage = function (event) {
                var datar = JSON.parse(event.data);
                var status = datar.status;
                var messages = datar.messages;
                var unread = datar.unread;
                if ((messages !== last_data && last_data && messages > last_data)) {
                    ringer.play();
                    $rootScope.messengerer();
                    $rootScope.$broadcast('chatptp', { ider: null, sound: true });

                    $('#live_popup2').fadeIn(1000);
                }
                if ((status !== last_status && last_data && status > last_status)) {
                    $rootScope.messengerer();
                    $rootScope.$broadcast('chatptp', { ider: null, sound: false });
                    //$rootScope.chatptp(null,false);
                }
                if ((unread !== last_unread && last_unread && unread > last_unread)) {
                    $rootScope.unreaded_block_show = true;
                    $rootScope.unreaded_block_show_toggle_image = false;
                    $rootScope.unreaded_count = unread;
                }
                last_data = messages;
                last_status = status;
                last_unread = unread;
            }
        }
    }
    //$rootScope.messager_statusn();
    ///////////////////////////////////////////////////////////////////////////
    $rootScope.person_followers = function (event) {
        $rootScope.show_more_person_viewslikes = false;
        $rootScope.show_more_person_followers = false;
        $rootScope.options_block_loaded = false;
        $rootScope.options_preloader = false;
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'statistic_followers', object_followed: event, limit: 20 })
        }).then(function (response) {
            console.log(response.data);
            $rootScope.options_block_loaded = true;
            $rootScope.options_preloader = true;
            if (response.data.record.length > 19) {
                $rootScope.show_more_person_followers = true;
            }
            $rootScope.statisticperson_options = response.data.record;
        });
    }
    $rootScope.get_more_person_followers = function (event) {
        limit20 += 20;
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'statistic_followers', object_followed: event, limit: limit20 })
        }).then(function (response) {
            console.log(response.data);
            $rootScope.statisticperson_options = response.data.record;
            $rootScope.show_more_person_followers = response.data.record;
        });
    }
    /////////////////////////////////////////////////////////////////////////////////////
    $rootScope.person_likes_views = function (table, event, type) {
        $rootScope.show_more_person_followers = false;
        $rootScope.show_more_person_viewslikes = false;
        $rootScope.options_block_loaded = false;
        $rootScope.options_preloader = false;
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'statistic_persons', table: table, id: event, object_type: type, limit: 20 })
        }).then(function (response) {
            //console.log(response.data);
            $rootScope.options_block_loaded = true;
            $rootScope.options_preloader = true;
            if (response.data.record.length > 19) {
                $rootScope.show_more_person_viewslikes = true;
            }
            $rootScope.statisticperson_options = response.data.record;
        });
    }

    $rootScope.get_more_viewslikes = function (table, event, type) {
        limit20 += 20;
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'statistic_persons', table: table, id: event, object_type: type, limit: limit20 })
        }).then(function (response) {
            console.log(response.data);
            $rootScope.statisticperson_options = response.data.record;
        });
    }
    ////////////////////////////////////////////////////////////////////////////////////// 
    $rootScope.alert_show = function (text) {
        $(".alert").fadeIn(0).text($filter('translate')(text));
        setTimeout(function () { $('.alert').hide() }, 5000);
    }
})
///////////////////////////////////////Messanger/////////////////////////////

com.controller('messengerctr', function ($scope, $http, $location, $filter, $compile, $transitions, $state, $timeout,$rootScope) {
        var opponent_num = $location.search().sn;

                ////////////////////NEW MESSAGES_POINTER////////////////////////
        socket.on('chat_msg_messenger_previous', function (data) {
        var id = data.sender;
        var message_list = document.getElementsByClassName('message_list');
        var data_r = data;
       
        $scope.messenger_main = data;
        $scope.$apply(function () {
         // $scope.$parent.unreaded_count = 1;
            ringer.play();
            for (var i = 0; i < message_list.length; i++) {
                if (message_list[i].getAttribute('data-user') == id) {
                    var pointer = i;
                    $scope.messenger[pointer].status = data_r.status;
                    $timeout(function () {
                        
                        message_list[pointer].innerHTML = data_r.message;
                        
                    }, 1600);
                }
            }
            //$timeout(function(){document.getElementById('message_pointer_1').style.color = '#ff5959';})
              
        })
              
        
        })
    //////////////////////////////////////////MESSENGERER////////////////////////
    $scope.messengerer = function () {
        $scope.spinner2 = false;
        $http({ method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'messenger' }) })
            .then(function (response) {
                $scope.spinner2 = true;
                $scope.messenger = response.data.record;

            })
    }
    $scope.messengerer();

    $http.defaults.headers.post["Content-Type"] = "application/x-www-form-urlencoded";
    var idparam = $location.search().sn;
    $scope.arrows = true;
    //$scope.profileinfo();

    var msg_counter = 10;
    $scope.moremessages = function (ider) {

        msg_counter += 10;
        $scope.preloader2 = true;
        $scope.chat_show = false;
        $scope.arrows = false;
        var chatid = $location.search().sn !== undefined ? $location.search().sn / hash : ider;
        $http({ method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'chat', id: chatid, limit: msg_counter }) })
            .then(function (response) {

                $scope.chater = response.data.record;
                $scope.chaterone = response.data.record[0];
                //console.log(response.data);
                $scope.preloader2 = false;
                $scope.chat_show = true;
                $scope.arrows = true;
                setTimeout(function () { document.getElementsByClassName('chat_container_scroll')[0].scrollTop = 0 }, 0);
            })
    }



    $scope.$on('chatptp', function (event, opt) {
        //$(".chater2").remove();
        var chatid = $location.search().sn !== undefined ? $location.search().sn / hash : opt.ider;
        
        $http({ method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'chat', id: chatid, limit: 10 }) })
            .then(function (response) {
                //$(".chater2").remove();
              
                $scope.chat_show = true;
                $scope.chater = response.data.record;
                $scope.chaterone = response.data.record[0];
                if (opt.sound == true) { ringer.play(); };
                // console.log(response.data.record);
            })
    })

    $scope.chat = function (ider) {
        $scope.arrows = false;
        $scope.preloader2 = true;
        $scope.chat_show = false;
        var chatid = $location.search().sn !== undefined ? $location.search().sn / hash : ider;

        $http({ method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'chat', id: chatid, limit: 10 }) })
            .then(function (response) {
                if(response.data.record.length > 9)
                {
                $scope.arrows = true;
                }
                $scope.preloader2 = false;
                $scope.chat_show = true;
                //$scope.messengerer();
                $scope.chater = response.data.record;
                $scope.chater_img = response.data.record2;
                
            })
    }
    $scope.chat();

    var count_press = 0;

    /////////////////////LISTEN_FOR_READED_MESSAGES///
    socket.on('read_chat', function (data) {
      //  alert('readed');
        //document.getElementById('message_pointer_1').style.color = 'rgba(118, 188, 28, 1)';
        var id = data.opponent; ///////??????????????//
        var message_list = document.getElementsByClassName('message_list');
        $scope.unreaded_block_show = false;
       
       
        $scope.$apply(function () {
     
            for (var read in $scope.chater) {
                read.status = 1;
            }

            for (var i = 0; i < message_list.length; i++) {
                if (message_list[i].getAttribute('data-user') == id) {
                    var pointer = i;
                    $scope.messenger[pointer].status = 1;
                }
            }
        })
    })
    //////////////////LISTEN FOR NEW MESSAGES/////////////
    socket.on('chat_msg_client', function (data) {
        console.log('new_message')
        var id = data.id;
        $scope.$apply(function () {
           
            $scope.chater.push(data);
        });

        $('#chat_cont').animate({ scrollTop: $('#chat_cont').prop('scrollHeight') }, 1000);
        //console.log(data);
    });

    //////////////////////////////////////////////////////////
    socket.on('chat_msg_messenger', function (data) {
        var id  = data.sender;
        var opp = data.opponent * 16 * 3 * 1992;
        var message_list = document.getElementsByClassName('message_list');
       
        $scope.$apply(function () {
          
            for (var i = 0; i < message_list.length; i++) {
                if (message_list[i].getAttribute('data-user') == id) {
                    const old_value = $scope.messenger[i].message;
                    var pointer = i;
                    message_list[pointer].innerHTML = 'is typing...';
                    $timeout(function () { message_list[pointer].innerHTML = old_value; }, 1500);
                }
            }
        })

    })



    ////////////////////USER_IS_TYPING///////////////

    socket.on('user_typing_emit', function (data) {
        //alert(data  + " = "+ $location.search().sn );
        if (opponent_num  == data.typer) {
           //alert('typing')
            $('#user_type_status').fadeIn(600).text(data.sender + " " + " is typing...").append("<i class = 'ml_2 fa fa-keyboard-o fsize15px color5 '></i>");
        }
       
       setTimeout(function () { $('#user_type_status').fadeOut(600) }, 3000)
    })

    /////////////////////EMIT_TYPING/////////////////

    $('#comment_text').on('keydown', function (e) {
        count_press += 1;
        if (count_press <= 2) {
    
            socket.emit('user_typing');
            setTimeout(function () { count_press = 0 }, 3000);
        }
    })
    var opponent_status;

    socket.on('opp_online_status', function (data) {
        //console.log(data)
        opponent_status = data.status == 1 ? 1 : 0;
    })

    var imagesrel = [];
    var images = [];

    /////////////////PRELOAD_FILES_AND_SEND_MESSAGE//////

    $scope.sendmessage_preload = function () {

        var opponent = $location.search().sn / hash;
        var og_params = [];

        $('#message_file').on('change', function () {

            var formdata = new FormData();
            var file = $id('message_file').files[0];
            var files = $id('message_file').files;

            formdata.append('ajax', 'sendmessage_preload');

            for (var i = 0, len = files.length; i < len; i++) { formdata.append("file" + i, files[i]); }

            $http({
                method: 'POST',
                url: 'ajax/ajax.php',
                data: formdata,
                headers: { 'Content-Type': undefined }
            })
                .then(function (response) {
                    $id('upload_chat_img_preview_block').classList.remove('dp');
                    $scope.message_img_preview = response.data.files;
                    console.log(response.data);
                    for (let val of response.data.files) { images.push(val.file); imagesrel.push(val.filerel) }
                })
        })

        $('body').off('click').on('click', '.common_message', function () {
            $scope.message_status = 'message_load';
         
            var node_data = { message: $('#comment_text').val(), image: new Array(images), audio: [] ,id : parseInt($scope.chater[$scope.chater.length-1].id) + 1};
            console.log(parseInt($scope.chater[$scope.chater.length-1].id)+1);
            console.log(node_data);
            socket.emit('chat_msg', node_data);

            data = {
                ajax: 'sendmessage_preload_finish',
                files: imagesrel,
                type: 'common',
                message: he.encode($id('comment_text').value),
                receiver: $location.search().sn, date: datejson(),
                status_message: opponent_status
            };
            $http({
                method: 'POST',
                url: 'ajax/ajax.php',
                data: $.param(data)
            })
                .then(function (response) {
                    console.log(response.data);
                    $scope.messageval = $id('comment_text').value;
                    if (response.data.status === '0') {
                        $scope.alert_show(response.data.text);
                        $scope.message_status = 'message_error';
                    }
                    else if (response.data.status === '1') {
                        $scope.message_status = 'message_sended';
                        $id('comment_text').value = "";

                        //Make Empty input file and array with files
                        $id('message_file').value = "";
                        images = [];
                        imagesrel = [];

                        $id('upload_chat_img_preview_block').classList.add('dp');
                        $('.message_img_prev').remove();
                        $('#chat_cont').animate({ scrollTop: $('#chat_cont').prop('scrollHeight') }, 1000);
                    }
                })
        })

        //////////////////////OPEN_GRAPH_MESSAGE////////////////////////////////////
        $('#comment_text').on('paste', function () {

            setTimeout(function () {
                $http({ method: 'POST', url: 'core/sw_og_parser.php', data: $.param({ oglinkprev: $('#comment_text').val() }) })
                    .then(function (response) {
                        console.log(response.data);
                        if (response.data.status === '1') {
                            $id('sender').classList.add('og_message');
                            $id('sender').classList.remove('common_message');
                            $scope.og_data = response.data;
                            og_params.push(response.data);
                            console.log(og_params);
                            $id('chat_og_preview').classList.remove('dp');

                            $(document).off('click').on('click', '.og_message', function () {

                                $scope.message_status = 'message_load';

                                $id('sender').classList.remove('og_message');
                                $id('sender').classList.add('common_message');

                                var node_data = { message: $id('comment_text').value, og_params: og_params, audio: [] };
                                socket.emit('chat_msg', node_data);
                                og_params = [];
                                $id('chat_og_preview').classList.add('dp');
                                var receiver = $location.search().sn;
                                var title = response.data.title;
                                var og_desc = response.data.desc;
                                var video = response.data.video;
                                var url = response.data.url;
                                var image = response.data.image;
                                var desc = he.encode($id('comment_text').value);
                                var data = { ajax: 'sendmessage_og', receiver: receiver, title: title, video: video, desc: desc, og_desc: og_desc, url: url, image: image, date: datejson() }
                                $http({
                                    method: 'POST',
                                    url: 'ajax/ajax.php',
                                    data: $.param(data)
                                })
                                    .then(function (response2) {

                                        if (response2.data.status === '0') {
                                            $scope.alert_show(response2.data.text);
                                            $scope.message_status = 'message_error';
                                        }
                                        else if (response2.data.status === '1') {
                                            $scope.message_status = 'message_sended';
                                            $id('comment_text').value = "";
                                            $id('message_file').value = "";
                                        }
                                    });

                            })

                        }
                    })
            })
        })
    }

    var audios = [];
    var audiosrel = [];
    $scope.send_voice_preload = function () {

        try {

            var mediaConstraints = {
                audio: true
            };

            navigator.getUserMedia(mediaConstraints, onMediaSuccess, onMediaError);

            function onMediaSuccess(stream) {
                var mediaRecorder = new MediaStreamRecorder(stream);
                mediaRecorder.mimeType = 'audio/wav'; // check this line for audio/wav
                mediaRecorder.ondataavailable = function (blob) {
                    $('#sendmessage_audio').fadeIn();
                    $('#deletemessage_audio').fadeIn();

                    $id('chat_audio_preview').src = URL.createObjectURL(blob);

                    document.getElementById('chat_audio_preview_play').addEventListener('click', function () {
                        $id('chat_audio_preview').play();
                    });
                    document.getElementById('chat_audio_preview_pause').addEventListener('click', function () {
                        $id('chat_audio_preview').pause();
                    })

                    console.log(blob);
                    var formdata = new FormData();

                    formdata.append('ajax', 'sendmessage_preload');
                    formdata.append('file', blob);

                    $http({
                        method: 'POST',
                        url: 'ajax/ajax.php',
                        data: formdata,
                        headers: { 'Content-Type': undefined }
                    })
                        .then(function (response) {
                            console.log(response.data);
                            $scope.preloaded_audio = response.data.files[0].filerel;
                            for (let val of response.data.files) { audios.push(val.file); audiosrel.push(val.filerel) }

                            $('#sendmessage_audio').off('click').on('click', function () {
                                $scope.message_status = 'message_load';

                                var node_data = { audio: [new Array(response.data.files[0].file)], og_params: [], message: "", image: [] };
                                socket.emit('chat_msg', node_data);

                                data = { ajax: 'sendmessage_preload_finish', files: audiosrel, type: 'audio', message: he.encode($id('comment_text').value), receiver: $location.search().sn, date: datejson() };
                                $http({
                                    method: 'POST',
                                    url: 'ajax/ajax.php',
                                    data: $.param(data)
                                })
                                    .then(function (response) {
                                        console.log(response.data);
                                        $scope.messageval = $id('comment_text').value;
                                        if (response.data.status === '0') {
                                            $scope.alert_show(response.data.text);
                                            $scope.message_status = 'message_error';
                                        }
                                        else if (response.data.status === '1') {
                                            $scope.message_status = 'message_sended';
                                            $id('comment_text').value = "";
                                            //Make Empty input file and array with files
                                            $id('message_file').value = "";
                                            $id('chat_audio_preview').src = "";

                                            $id('upload_chat_img_preview_block').classList.add('dp');
                                            $('.message_img_prev').remove();
                                            $('#chat_cont').animate({ scrollTop: $('#chat_cont').prop('scrollHeight') }, 1000);

                                        }

                                    })

                            })
                        })
                    audios = [];
                    audiosrel = [];
                }
                $('body').on('click', '.start_record', function () {
                    $(this).removeClass('backsw3 start_record fa-microphone').addClass('back9 stop_record pulse fa-microphone-slash');
                    mediaRecorder.start(10000);
                })
                $('body').on('click', '.stop_record', function () {
                    $(this).removeClass('back9 stop_record pulse pulse fa-microphone-slash').addClass('backsw3 start_record  fa-microphone');
                    mediaRecorder.stop();
                })
            }
            function onMediaError(e) {
                console.error('media error', e);
            }
        }
        catch (err) {
            console.log(err);
        }
    }
    $scope.send_voice_preload();
    $scope.audio_recorder_option = function () {
        if ($scope.hasGetUserMedia()) {
            $id('upload_chat_audio_preview').classList.remove('dp');
        }
        else {
            $scope.alert_show("%action_browser_record_no_support%");
        }
    }

    $scope.delete_preloaded_file = function (item, img) {

        $http({
            method: 'POST',
            url: 'ajax/ajax.php',
            data: $.param({ ajax: 'delete_preloaded_image', file: img })
        })
            .then(function (response) {

                if (response.data.status === "1") {
                    if ($scope.message_img_preview) {
                        $scope.message_img_preview.splice(item, 1);
                        imagesrel.splice(item, 1);
                        images.splice(item, 1);
                        //console.log(imagesrel);
                        //console.log(images);
                    }
                }
                else if (response.data.status === "0") {
                    $scope.message_img_preview.splice(item, 1);
                    imagesrel.splice(item, 1);
                    images.splice(item, 1);
                }
                audiosrel.splice(item, 1);
                console.log(audiosrel);
                if (response.data.lastfile === "1") {
                    $id('upload_chat_img_preview_block').classList.add('dp');
                }
            })
    }




    $scope.chat_play = function (elem) {
        var audio = elem.target.nextElementSibling;
        var allaudio = document.getElementsByTagName('audio');

        for (var i = 0; i < allaudio.length; i++) {
            allaudio[i].pause();
            allaudio[i].previousElementSibling.classList.remove('fa-pause-circle');
            // allaudio[i].previousElementSibling.classList.add('fa-play-circle');
        }
        if (audio.currentTime < 1) {
            audio.play();
            elem.target.classList.add('fa-pause-circle');
            elem.target.classList.remove('fa-play-circle');
        }
        else {
            audio.pause();
            elem.target.classList.add('fa-play-circle');
            elem.target.classList.remove('fa-pause-circle');
            audio.currentTime = 0;
        }
        audio.addEventListener("ended", function () {
            elem.target.classList.remove('fa-pause-circle');
            elem.target.classList.add('fa-play-circle');
            audio.currentTime = 0;
        })
    }



    $scope.hasGetUserMedia = function () {
        try {

            return !!(navigator.mediaDevices.getUserMedia || navigator.mediaDevices.getUserMedia ||
                navigator.mediaDevices.getUserMedia || navigator.mediaDevices.getUserMedia);
        }
        catch (err) {
            console.log(err);
        }
    }




    $scope.invite_to_chat = function (receiver_id) {
        var invitation = 'Welcome To Chat'
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'sendmessage', message: invitation, receiver: receiver_id * hash, date: datejson() })
        }).then(function (response) {
            $scope.messengerer();
            console.log(response.data)
        })
    }
    $scope.search_contact = function () {
        var searchitem = $id('search_contact').value;
        if (searchitem !== "") {
            $http({ method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'searchcontact', search: searchitem }) })
                .then(function (response) {
                    $scope.searchcontact = response.data.record;
                    $scope.searchcontact_box = true;
                    console.log(response.data);
                })
        }
        else {
            $scope.searchcontact_box = false;
        }
    };

    $scope.delete_message = function (index, id) {

        $http({ method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'deletemessage', messageid: id }) })
            .then(function (response) {
                if (response.data.status === '1') {
                    $('.message_deleted_pointer_' + index).css({ transition: '0.7s', transform: 'rotate(180deg)' }).animate({ opacity: '0' }, 300);
                }
                console.log(response.data)
            })

    };
    $scope.hash = 16 * 3 * 1992;

})


com.controller('feedctr', function ($http, $scope, $filter, $location) {
    $scope.comment_hash = function (id) {
        $location.path(decodeURIComponent("/" + id));
    }

    $scope.getpost = function () {
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'getpost_feed' })
        }).then(function (response) {
            $scope.timeline_preloader = true;
            $scope.timeline_show = true;
            $scope.timeline = response.data.record;
            console.log(response.data);
        });
    }

    $scope.getpost_more = function () {
        limit1 += 10;
        $scope.preloader2 = true;
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'getpost_feed', limit: limit1 })
        }).then(function (response) {

            $scope.feed = response.data.record;
            $scope.preloader2 = false;

            console.log(response.data);
        });
    }

    $scope.friends_activity = function () {
        $http({

            method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'friends_activity' })
        }).then(function (response) {

            $scope.activity_followed = response.data.record;
            console.log(response.data);
        });
    }
    $scope.friends_posts_activity = function () {
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'friends_posts_activity' })
        }).then(function (response) {
            $scope.friends_posts_activity = response.data.record;
            //console.log(response.data);
        });
    }
    $scope.activity_more = function () {
        $scope.preloader2 = true;
        limit1 += 10;
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'friends_activity', limit: limit1 })
        }).then(function (response) {
            $scope.activity = response.data.record;
            $scope.preloader2 = false;
            console.log(response.data);
        });
    }


    $scope.friends_activity();
    $scope.friends_posts_activity();
    $scope.getpost();
})


////////////////////////////////////////////////////FILTERS////////////////////////
com.filter('substr', function () {// таким образом мы на писали глобальный фильтр и теперь с мы можем включить его в любой модуль как выше
    return function (x) {
        if (x) {
            var substr = x.substring(0, 200);
            return substr + ".......";
        }
    }
})
com.filter('prefix_type', function () {
    return function (x) {
        if (x && x.match(/http|https/g)) {

            return x;
        }
        else {
            return '//' + x;
        }
    }
})

com.filter('substr1', function () {// таким образом мы на писали глобальный фильтр и теперь с мы можем включить его в любой модуль как выше
    return function (x) {
        if (x) {
            var substr = x.substring(0, 22);
            return substr + "...";
        }
    }
})
com.filter('substr2', function () {// таким образом мы на писали глобальный фильтр и теперь с мы можем включить его в любой модуль как выше
    return function (x) {
        if (x) {
            var substr = x.substring(0, 37);
            return substr + "..";
        }
    }
})
com.filter('substrshort', function () {
    return function (x) {
        if (x) {
            var substr = x.substring(0, 5);
            return substr;
        }
    }
})

com.filter('trust', ['$sce', function ($sce) {
    var div = document.createElement('span');
    return function (text) {
        div.innerHTML = text;
        return $sce.trustAsHtml(div.textContent);
    };
}]);

com.filter('substrfirst', function () {
    return function (x, param) {
        if (x) {
            var substr = x.substring(param);
            return substr;
        }
    }
})
com.filter('substrcustom', function () {
    return function (x, param) {
        if (x) {
            var substr = x.substring(0, param);
            return substr;
        }
    }
})
com.filter('empty', function ($filter) {
    return function (x, param) {
        if (x == "") {
            return $filter('translate')(param);
        }
        else {
            return x;
        }
    }
})
com.filter('substrshort2', function () {
    return function (x) {
        if (x) {
            var substr = x.substring(0, 15);
            return substr;
        }
        else {
            return x;
        }
    }
})
com.filter('substr3', function () {// таким образом мы на писали глобальный фильтр и теперь с мы можем включить его в любой модуль как выше
    return function (x) {
        if (x) {
            var substr = x.substring(0, 35);
            return substr + "..";
        }
    }
})
com.filter('substr4', function () {
    return function (x) {
        if (x) {
            var substr = x.substring(0, 350);
            return substr + "..";
        }
    }
})
com.filter('replace', function () {
    return function (x) {
        var rep = x.replace(new RegExp(" ", "g"), "_");
        return rep;
    }
})
com.filter('firstletter', function () {

    return function (x) {
        var word = "";
        var rep = x.split(" ");
        for (var i = 0; i < rep.length; i++) {
            word += rep[i].substring(0, 1);

        }
        return word;
    }
})
com.filter('encodeuri', function () {
    return function (x) {
        var rep = encodeURIComponent(x);
        return rep;
    }
})
com.filter('decodeuri', function () {
    return function (x) {
        var rep = decodeURIComponent(x);
        return rep;
    }
})
com.filter('month', function () {
    return function (x) {
        var res = x;
        switch (x) {
            case '01': res = "%jan%"; break;
            case '02': res = "%feb%"; break;
            case '03': res = "%mar%"; break;
            case '04': res = "%apr%"; break;
            case '05': res = "%may%"; break;
            case '06': res = "%jun%"; break;
            case '07': res = "%jul%"; break;
            case '08': res = "%aug%"; break;
            case '09': res = "%sep%"; break;
            case '10': res = "%oct%"; break;
            case '11': res = "%nov%"; break;
            case '12': res = "%dec%"; break;
        }
        return res;
    }
})
com.filter('ten', function () {

    return function (x) {
        if (x < 10) { res = x.substr(1, 3); }
        else { res = x; };
        return res;
    }
})

com.factory("interceptors", [function () {
    return {
        // if beforeSend is defined call it
        'request': function (request) {
            if (request.beforeSend)
                request.beforeSend();
            return request;
        },
        // if complete is defined call it
        'response': function (response) {

            if (response.config.complete)
                response.config.complete(response);
            return response;
        }
    };
    return {
        // if beforeSend is defined call it
        'request': function (request) {
            if (request.beforeSend)
                request.beforeSend();
            return request;
        },
        // if complete is defined call it
        'response': function (response) {
            if (response.config.complete)
                response.config.complete(response);
            return response;
        }
    };

}]);

com.service('$socket', function ($q, $state, $transitions, $location,$rootScope,$timeout) {

    this.socket = function () {

        socket = io(socket_adress);

        ////////////////////////CONNECTION_EVENT_FROM_CLIENT///////

        socket.on('connect', function (data) {

            if ($location.search().sn !== undefined) {
                socket.emit('opponent', $location.search().sn / hash);
            }
        })
     
        $transitions.onFinish({ from: 'account.chat' }, function ($transitions, event) {
            socket.emit('leave_room');
        });

        $transitions.onSuccess({ to: 'account.chat' }, function ($transitions, event) {
            if ($location.search().sn !== undefined) {
                socket.emit('opponent', $location.search().sn / hash);
            }

        });

        $transitions.onSuccess({ to: 'index' }, function ($transitions, event) {
            socket.disconnect('leave_room');
        })

      socket.on('chat_msg_messenger_previous', function (data) {
    
       var unreaded_counter  =  $rootScope.unreaded_count !== undefined ? $rootScope.unreaded_count : 0;
       $rootScope.unreaded_block_show = true;
       $timeout(function(){
        $rootScope.unreaded_count = parseInt(unreaded_counter) + 1;
        
        })
      });
  

    }

})

com.service('$auth', function ($q, $rootScope, $http, $state, $transitions, $timeout, $location,$cookies) {

    this.session_home = function (event) {
        var defer = $q.defer();
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'checksession' })
        }).then(function (response) {
            console.log(response.data);
            if (response.data.session === '0') {
                defer.resolve(response.data)
            }
            else if (response.data.session === '1') {

                defer.resolve(response.data)

                $timeout(function () { $state.go('account.home'); $state.defaultErrorHandler(function (err) { console.log(err) }); })
            }
            else {
                defer.reject();
            }
        })
        return defer.promise;
    }

    this.session_account = function (event) {
        var defer = $q.defer();
         
                 $transitions.onStart({ }, function ($transitions, event) {
           var defer = $q.defer();
    
            $http({
                method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'checksession' })
            }).then(function (response) {
                  
                if (response.data.session === '0') {
                  
                     $state.go('index'); $state.defaultErrorHandler(function (err) { console.log(err) }); 
                       defer.resolve();
                }
                  else {
                    
                      defer.resolve();
                }
             
            })
             
          return defer.promise;
         })
       
        $timeout(function () {
            $http({
                method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'checksession' })
            }).then(function (response) {

                if (response.data.session === '0') {
                    defer.resolve(response.data)
                    $timeout(function () { $state.go('index'); $state.defaultErrorHandler(function (err) { console.log(err) }); });
                }
                else if (response.data.session === '1') {
                    defer.resolve(response.data)
                }

                else {
                    defer.reject();
                }

                if (response.data.active === '0') {
                    defer.resolve(response.data)
                    $state.go('accountunactive');
                    $rootScope.active_time = response.data.active_time;
                }
                else if (response.data.active === '1') {

                    defer.resolve(response.data)
                    $state.go('account.home');

                }

            })

        })
        return defer.promise;
    }
    this.active_sketch = function () {
        var defer = $q.defer();
         $transitions.onSuccess({ to: 'account.sketch' }, function ($transitions, event) {
            $http({
                method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'checksession', sketch: $location.path().split("/")[3] })
            }).then(function (response) {
                
                console.log(response.data.active + " sketch " + $location.path().split("/")[3]);
                if (response.data.active === '0') {
                    
                    $state.go('account.sketchunactive'); $state.defaultErrorHandler(function (err) { console.log(err) });
                    defer.resolve(response.data);
                
                }
            
                 return defer.promise;
               
           })
         })
    }
});

com.service('$reload_state', function ($window, $state, $transitions, $timeout) {

    this.reload_state = function (event) {
        var hammertime = new Hammer($window);
        hammertime.get('press').set({
            time: 1500
        })
        hammertime.on("press", function (event) {
            var connect_status = navigator.onLine === true ? "Connected To Internet - OK" : "No Network Connection - Error";
            $("#main_ui_view").animate({ opacity: '0.3' }, 200);
            $("#upload_status_bar").css('display', 'block').animate({ top: '60px' }, 200);
            $("#connection_status_text").text(connect_status);
        })
        hammertime.on("pressup", function (event) {
            //$("#main_account_view").animate({opacity : '1'},200);

            $("#connection_status_text").text("Loading...");
            // $state.transitionTo('account.messenger',{}, { reload: true, inherit: true, notify: true })
            $state.go($state.current, {}, { reload: $state.current });
            $transitions.onSuccess({ to: $state.current.name }, function ($transitions, event) {
                $("#main_ui_view").animate({ opacity: '1' }, 200);
                $("#upload_status_bar").fadeOut(1000);
            })
        })

    }
})

com.config(function ($sceDelegateProvider, $compileProvider, $stateProvider, $urlRouterProvider, $locationProvider, $httpProvider) {
    ///$httpProvider.defaults.withCredentials = true;

    $httpProvider.useApplyAsync(true);
    $httpProvider.interceptors.push('interceptors');
    $sceDelegateProvider.resourceUrlWhitelist([
        // Allow same origin resource loads.
        'self',
        // Allow loading from our assets domain. **.
        'https://graph.facebook.com/**',
        'https://timer.od.ua/**',
        'https://**',
        'https://www.facebook.com/sharer/sharer.php/**',
        'viber://forward?text='

    ]);

    $compileProvider.aHrefSanitizationWhitelist(/^\s*(whatsapp|viber|vk|fb-messenger|tg|http|https):/);

    $locationProvider.hashPrefix('');
    $urlRouterProvider.otherwise('/');
    $stateProvider
        .state('index', {
            url: '',
            templateUrl: '../home.html',
            controller: 'indexctr',
            resolve: {
                auth: function ($auth, $q, $http, $state) { return $auth.session_home(); },
            }
        })
        .state('account', {
            url: '/account',
            abstract: true,
            templateUrl: '../account.html',
            controller: 'accountctr',
            resolve: {

               // auth   : function ($auth)         { return $auth.session_account(); },
                socket : function ($socket)       { return $socket.socket(); },
                reload : function ($reload_state) { return $reload_state.reload_state(); },
            }
        })
        .state('accountunactive', {
            url: '/accountunactive',
            templateUrl: 'error_page_1.html',
            controller: 'accountctr',
            resolve: {
                auth: function ($auth) { return $auth.session_account(); }
            }
        })
        .state('account.home', {
            url: '/home',
            templateUrl: '../blocks/account.html',
            controller: 'accountctr'
        })
        .state('account.friends', {
            url: '/friends',
            templateUrl: '../friends.html',
            controller: 'accountctr'
        })
        .state('account.editprofile', {
            url: '/editprofile',
            templateUrl: '../editprofile.html',
            controller: 'accountctr'
        })
        .state('account.weather', {
            url: '/weather',
            templateUrl: '../weather.html',
            controller: 'accountctr'
        })
        .state('account.messenger', {
            url: '/messenger',
            templateUrl: '../blocks/messenger.html',
            controller: 'messengerctr',
            resolve: {

            }
        })
        .state('account.chat', {
            url: '/chat?sn',
            templateUrl: '../blocks/chat.html',
            controller: 'messengerctr'
        })
        .state('account.feed', {
            url: '/feed',
            templateUrl: '../feed.html',
            controller: 'feedctr'
        })
        .state('account.feed.feedlabel', {
            url: '/feedlabel',
            templateUrl: '../blocks/feed/feed_label.html',
            controller: 'feedctr'
        })
        .state('account.feed.friendsactivity', {
            url: '/friendsactivity',
            templateUrl: '../blocks/feed/feed_friendsactivity.html',
            controller: 'feedctr'
        })
        .state('account.feed.posts', {
            url: '/friendspostsactivity',
            templateUrl: '../blocks/feed/feed_friendspostsactivity.html',
            controller: 'feedctr'
        })
        .state('account.privatelabel', {
            url: '/privatelabel/:pl',
            templateUrl: '../private-label.html',
            controller: 'privatelabelctr'
        })
        .state('account.explorer', {
            url: '/explorer/:exp',
            templateUrl: '../explorer.html',
            controller: 'searchpagerctr'
        })
        .state('account.sweetvelcard', {
            abstract: true,
            url: '/sweetvelcard',
            templateUrl: '../sweetvelcard.html',
            controller: 'creategcctr'
        })
        .state('account.sweetvelcard.bussiness', {
            url: '/bussiness',
            templateUrl: '../blocks/sweetvelcard/greetbs.html',
            controller: 'creategcctr'
        })
        .state('account.sweetvelcard.greet', {
            url: '/greet',
            templateUrl: '../blocks/sweetvelcard/greet.html',
            controller: 'creategcctr'
        })
        .state('account.sketch', {
            url: '/sketch/:sk',
            templateUrl: '../sketch.html',
            controller: 'sketchctr',
            resolve: {
                 auth_active: function ($auth) { return $auth.active_sketch(); }
            }
        })
        .state('account.sketchunactive', {
            url: '/sketchunactive',
            templateUrl: 'error_page_sketch.html',
            controller: 'accountctr',

        })
        .state('account.skfriends', {
            url: '/skfriends/:sk',
            templateUrl: '../friends.html',
            controller: 'sketchctr'
        })
        .state('account.createlabel', {
            url: '/createlabel/:cl',
            templateUrl: '../createlabel.html',
            controller: 'createlabelctr'
        })


});

////////////////////////////////Search Page Controller

com.controller('searchpagerctr', function ($scope, $http, $location, $filter) {

    var param = $location.path().split("/")[3];

    $scope.search_page = function (parameter) {
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'search', search: parameter })
        }).then(function (response) {
            $scope.searcher_page = response.data.record;
            //$rootScope.searchertags = response.data.record2;
            console.log(response.data);
        });
    }
    $scope.search_page_more = function () {
        limit1 += 10;
        $scope.preloader2 = true;
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'search', search: param, limit: limit1 })
        }).then(function (response) {
            $scope.searcher_page = response.data.record;
            $scope.preloader2 = false;
            console.log(response.data);
        });
    }
    $scope.$on('$viewContentLoaded', function (event) {
        $scope.search_page(param);
    })

})

////////////////////////////////Create Card controller

com.controller('creategcctr', function ($scope, $http, $location, $filter) {


    $http.defaults.headers.post["Content-Type"] = "application/x-www-form-urlencoded";
    $scope.eventloc = typeof $location.search().event === "undefined" ? "%events%" : $location.search().event;
    /////////////AUDIOS
    $http({
        method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'getaudio' })
    }).then(function (response) {
        $scope.audiores = response.data.record;

    });
    ////////////////////////////GET MY LABELS///////////////////
    $scope.get_labels_private = function () {
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'getlabels_private', quantity: 'notone' })
        }).then(function (response) { $scope.label = response.data.record; console.log(response.data.record); });
    }
    $scope.get_labels_private();

    /////////////////THEMES////////////////////////////////////////////
    $http({
        method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'gettheme' })
    }).then(function (response) { $scope.themeres = response.data.record; $scope.show = true; });
    ///////////////////GetListBs

    $http({
        method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'getlist', table: 'eventlistbs', row: 'event', lang: langstorage })
    }).then(function (response) { $scope.eventres = response.data.record; });

    $http({
        method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'getlist', table: 'wishlistbs', row: 'wish', lang: langstorage })
    }).then(function (response) { $scope.wishres = response.data.record; });

    $scope.langlistevent = function (langg) {
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'getlist', table: 'eventlistbs', row: 'event', lang: langg })
        }).then(function (response) { $scope.eventres = response.data.record; });
    }
    $scope.langlistwish = function (langg) {
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'getlist', table: 'wishlistbs', row: 'wish', lang: langg })
        }).then(function (response) { $scope.wishres = response.data.record; });
    }

    $scope.listtype = function (listtype, row, res) {
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'getlist', table: listtype, row: row, lang: langstorage })
        }).then(function (respons9e) { $scope[res] = response.data.record; });
    }
    $http({
        method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'getimgset0' })
    }).then(function (response) { $scope.countimgsets = response.data.record;console.log(response.data) });
    //////////////////GetList/////////////////////////
    $http({
        method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'getlist', table: 'eventlist', row: 'event', lang: langstorage })
    }).then(function (response) { $scope.eventrescom = response.data.record; });

    $http({
        method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'getlist', table: 'wishlist', row: 'wish', lang: langstorage })
    }).then(function (response) { $scope.wishrescom = response.data.record; });
    ////////////////////////////////////////////////
    $http({
        method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'getimgset', imgset: 'celebration' })
    }).then(function (response) {
        $scope.loadimgset = true;
        $scope.imgsetres = response.data.record;
        $scope.author = response.data.record[5].author;
        $scope.authorlink = response.data.record[5].authorlink;
        $scope.license = response.data.record[5].license;
        $scope.licenselink = response.data.record[5].licenselink;


    });

    $scope.getset = function (val) {
        $scope.preloader = true;
        $scope.loadimgset = false;
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'getimgset', imgset: val })
        }).then(function (response) {
            $scope.imgsetres = response.data.record;
            $scope.author = response.data.record[5].author;
            $scope.authorlink = response.data.record[5].authorlink;
            $scope.license = response.data.record[5].license;
            $scope.licenselink = response.data.record[5].licenselink;
            $scope.preloader = false;
            $scope.loadimgset = true;

        });

    }

    $scope.sharer = function (data) {

        $id('viber').setAttribute('href', 'viber://forward?text=' + encodeURIComponent(data));
        $id('whatsapp').setAttribute('href', 'whatsapp://send?text=' + encodeURIComponent(data));
        $id('fb').setAttribute('href', 'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(data));
        $id('vkontakte').setAttribute('href', 'http://vk.com/share.php?url=' + encodeURIComponent(data));
        $id('telegram').setAttribute('href', 'tg://msg?text=' + encodeURIComponent(data));
        $id('fbmessenger').setAttribute('href', 'fb-messenger://share?link=' + encodeURIComponent(data));
    }

    $scope.createswcard = function (kind) {

        var email = document.body.contains(document.getElementsByName('email')[0]) ? document.getElementsByName('email')[0].value : "null";
        var vk = document.body.contains(document.getElementsByName('social_vk')[0]) ? document.getElementsByName('social_vk')[0].value : "null";
        var fb = document.body.contains(document.getElementsByName('social_fb')[0]) ? document.getElementsByName('social_fb')[0].value : "null";
        var insta = document.body.contains(document.getElementsByName('social_insta')[0]) ? document.getElementsByName('social_insta')[0].value : "null";
        var phone = document.body.contains(document.getElementsByName('phone')[0]) ? document.getElementsByName('phone')[0].value : "null";
        var label = document.body.contains(document.getElementById('label_id')) ? document.getElementById('label_id').value : "null";

        var social = JSON.stringify({
            label: label,
            vk: vk,
            fb: fb,
            insta: insta,
            email: email,
            phone: phone
        });
        var place = document.body.contains(document.getElementById('place')) ? document.getElementById('place').value : "null";
        var event = document.getElementById('event').value;
        var sender = document.getElementById('sender').value;
        var receiver = document.getElementById('receiver').value;
        var desc = document.getElementById('wishes').value;
        var audio = document.getElementById('audiotrack').value;
        var style = document.getElementById('theme').value;

        if (style == "") { $('#alert_error_text').text($filter('translate')('%alert_theme%')); $('#alert_error').modal({ show: 'true' }); }
        else if (audio == "") { $('#alert_error_text').text($filter('translate')('%alert_audio%')); $('#alert_error').modal({ show: 'true' }); }
        else if (event == "") { $('#alert_error_text').text($filter('translate')('%alert_event%')); $('#alert_error').modal({ show: 'true' }); }
        else if (sender == "") { $('#alert_error_text').text($filter('translate')('%alert_sender%')); $('#alert_error').modal({ show: 'true' }); }
        else if (receiver == "") { $('#alert_error_text').text($filter('translate')('%alert_receiver%')); $('#alert_error').modal({ show: 'true' }); }
        else if (desc == "") { $('#alert_error_text').text($filter('translate')('%alert_desc%')); $('#alert_error').modal({ show: 'true' }); }



        else {
            var formdata = new FormData();
            formdata.append('ajax', 'creategiftcardbs');
            formdata.append('event', event);
            formdata.append('sender', sender);
            formdata.append('receiver', receiver);
            formdata.append('place', place);
            formdata.append('imgset', document.getElementsByClassName('imgset')[0].getAttribute('imgset'));
            formdata.append('description', desc);
            formdata.append('audio', audio);
            formdata.append('style', style);
            formdata.append('social', social);
            formdata.append('kind', kind);
            formdata.append('date', datejson());

            $http({
                method: 'POST',
                url: 'ajax/ajax.php',
                data: formdata,
                headers: { 'Content-Type': undefined }
            }).then(function (response) {
                 console.log(response.data);
                if (response.data !== "") {
                   
                    ogpreview(response.data.replace(/<\/?[^>]+>/g, ''));
                    $scope.sharer(response.data.replace(/<\/?[^>]+>/g, ''));
                    $('#sw_result').modal({ show: 'true' });
                }
                else { $('#alert_error_text').text($filter('translate')('%error_1%')); $('#alert_error').modal({ show: 'true' }); }
            })
        }

    }
    $scope.createitem = function () {
        var social = JSON.stringify({
            label: document.getElementById('label_id').value,
            vk: document.getElementsByName('social_vk')[0].value,
            fb: document.getElementsByName('social_fb')[0].value,
            insta: document.getElementsByName('social_insta')[0].value,
            email: document.getElementsByName('email')[0].value,
            phone: document.getElementById('phone').value
        });
        var place = document.getElementById('place').value;
        var event = document.getElementById('event').value;
        var sender = document.getElementById('sender').value;
        var receiver = document.getElementById('receiver').value;
        var desc = document.getElementById('wishes').value;
        var audio = document.getElementById('audiotrack').value;
        var style = document.getElementById('theme').value;
        var img = document.getElementsByName('img_file')[0].files[0];
        var check_img = document.getElementsByName('img_file')[0].files.length;

        if (style == "") { $('#alert_error_text').text($filter('translate')('%alert_theme%')); $('#alert_error').modal({ show: 'true' }); }
        else if (check_img == 0) { $('#alert_error_text').text($filter('translate')('%alert_image%')); $('#alert_error').modal({ show: 'true' }); }
        else if (audio == "") { $('#alert_error_text').text($filter('translate')('%alert_audio%')); $('#alert_error').modal({ show: 'true' }); }
        else if (event == "") { $('#alert_error_text').text($filter('translate')('%alert_event%')); $('#alert_error').modal({ show: 'true' }); }
        else if (sender == "") { $('#alert_error_text').text($filter('translate')('%alert_sender%')); $('#alert_error').modal({ show: 'true' }); }
        else if (receiver == "") { $('#alert_error_text').text($filter('translate')('%alert_receiver%')); $('#alert_error').modal({ show: 'true' }); }
        else if (desc == "") { $('#alert_error_text').text($filter('translate')('%alert_desc%')); $('#alert_error').modal({ show: 'true' }); }

        else {

            var formdata = new FormData();
            formdata.append('ajax', 'createitem');
            formdata.append('title', event);
            formdata.append('sender', sender);
            formdata.append('receiver', receiver);
            formdata.append('place', place);
            formdata.append('description', desc);
            formdata.append('audio', audio);
            formdata.append('style', style);
            formdata.append('social', social);
            formdata.append('date', datejson());
            formdata.append('img', img);
            $http({
                method: 'POST',
                url: 'ajax/ajax.php',
                data: formdata,
                headers: { 'Content-Type': undefined }
            }).then(function (response) {
                if (response.data !== "") {

                    ogpreview(response.data.replace(/<\/?[^>]+>/g, ''));
                    $scope.sharer(response.data.replace(/<\/?[^>]+>/g, ''));
                    $('#sw_result').modal({ show: 'true' });
                }
                else { $('#alert_error_text').text($filter('translate')('%error_1%')); $('#alert_error').modal({ show: 'true' }); }
            })
        }

    }
    $scope.redir = function (page) {
        document.location.href = page;
    }

    //$scope.$watch('adress_preview', function(){alert('ok');});

});

/////////////////////SKETCH//////////////////////////////

//////////////////////////SWCARD/////////////////////////
swcard.controller('swcardctr', function ($scope, $location, $http, $attrs) {

    $http.defaults.headers.post["Content-Type"] = "application/x-www-form-urlencoded";
    // var keyvalue2 = $location.search().gc;
    var keyvalue = $location.path().split('/')[2];

    if ($location.path().split('/')[3] && $location.path().split('/')[3] !== '0' && $location.path().split('/')[3] !== true) { $scope.sender_exists = 'true' }

    $scope.get_card = function () {
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'getcard', keyvalue: keyvalue })
        }).then(function (response) {
            $scope.card = response.data.record[0];
            if (response.data.record[0].kind == 'business') { $scope.business = true; }
            if (response.data.record[0].vk !== "") { $scope.sw_contacts_check_vk = true; }
            if (response.data.record[0].fb !== "") { $scope.sw_contacts_check_fb = true; }
            if (response.data.record[0].insta !== "") { $scope.sw_contacts_check_insta = true; }
            if (response.data.record[0].label !== "") { $scope.sw_contacts_check_label = true; }
            if (response.data.record[0].phone !== "") { $scope.sw_contacts_check_email = true; }
            if (response.data.record[0].email !== "") { $scope.sw_contacts_check_email = true; }
            if (response.data.record[0].placeadress !== "") { $scope.sw_contacts_check_placeadress = true; }

        });
    }

    $scope.get_switem = function () {
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'getitem', keyvalue: keyvalue })
        }).then(function (response) {
            console.log(response.data)
            $scope.itemcard = response.data.record[0];
            if (response.data.record[0].vk !== "") { $scope.swi_contacts_check_vk = true; }
            if (response.data.record[0].fb !== "") { $scope.swi_contacts_check_fb = true; }
            if (response.data.record[0].insta !== "") { $scope.swi_contacts_check_insta = true; }
            if (response.data.record[0].label !== "") { $scope.swi_contacts_check_label = true; }
            if (response.data.record[0].phone !== "") { $scope.swi_contacts_check_phone = true; }
            if (response.data.record[0].email !== "") { $scope.swi_contacts_check_email = true; }
            if (response.data.record[0].placeadress !== "") { $scope.swi_contacts_check_placeadress = true; }
        });
    }

    $scope.play = function () {
        angular.element(document.querySelector('#audio'))[0].play();
        document.querySelectorAll('.mi1')[0].classList.add("pulse");
        //document.querySelectorAll('.mi2')[0].style.display = "block";

    };
    $scope.pause = function () {
        angular.element(document.querySelector('#audio'))[0].pause();
        document.querySelectorAll('.mi1')[0].classList.remove("pulse");
        //document.querySelectorAll('.mi2')[0].style.display = "none";
    };
    $scope.stop = function () {
        angular.element(document.querySelector('#audio'))[0].pause();
        angular.element(document.querySelector('#audio'))[0].currentTime = 0
        document.querySelectorAll('.mi1')[0].classList.remove("pulse");
        //document.querySelectorAll('.mi2')[0].style.display = "none";

    };
    $scope.card_existance = function (table) {
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'card_existance', table: table, keyvalue: keyvalue })
        }).then(function (response) {
            //console.log(response.data);
            if (response.data.status == 1) {
                $scope.show_card_check = true;
            }
            else {
                $scope.errorpage2 = true;
                $scope.show_card_check = false
            }
        });
    };


})


//////////////////////////////////Create Label//////////////////

com.controller('createlabelctr', function ($scope, $location, $http, $filter) {
    /////////////BROADCAST///////////////

    //////////////////////////////////
    $http.defaults.headers.post["Content-Type"] = "application/x-www-form-urlencoded";

    $scope.logo_text = "Your Logo";
    var label_number = $location.path().split("/")[3];
    $scope.getpostprivate(label_number);
    //alert(label_number);
    $http({
        method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'gettheme' })
    }).then(function (response) { $scope.themeres = response.data.record; });

    $http({
        method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'getlabels_private', quantity: label_number })
    }).then(function (response) {
        $scope.onelabel = response.data.record[0];
        // console.log(response.data.record[0]);
        if (response.data.record[0] == undefined) { $scope.errorpage = true; } else { $scope.privatelabel = true; }
    });




    ///////////////////////GETLABEL IMAGESS/////
    $scope.getlabelimages = function () {
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: "labelimages", label: label_number, status: 'notnull' }),
        }).then(function (response) {
            $scope.labelimagesprivate = response.data.record;
        });
    }
    $scope.$on('get_label_images', function () {
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: "labelimages", label: label_number, status: 'notnull' }),
        }).then(function (response) {
            $scope.labelimagesprivate = response.data.record;
        });
    })
    ///////////////////////GET POSTS/////


    //////////////////////////////////
    $scope.framelabelurl = "labelframe/" + label_number;//////Setting iframe label url

    var filearr = [];

    $scope.postimagepreview = function (elem) {

        var filer = $id('postfile').files;
        var filearr0 = Array.from(filer);       ////////Files in input for One Operation
        filearr = filearr.concat(filearr0);//////All files From input for all operations

        for (var i = 0; i < filearr0.length; i++) {
            var url = URL.createObjectURL(filearr0[i]);
            var div = document.createElement('div');
            var itag = document.createElement('i');
            itag.className = "pull-right  fa fa-times-circle-o fa-2x textshgrt color5 iconhov cursor";

            //var img = new Image();img.src = url;
            div.appendChild(itag);
            div.style.backgroundImage = "url('" + url + "')";
            div.style.width = '33%';
            div.style.paddingBottom = '28%';
            div.style.backgroundSize = 'cover';
            div.style.backgroundPosition = 'center';
            div.style.display = 'inline-block';
            div.style.margin = '0.15%';
            if (elem.files[i].type.match("image/jpg|image/png|image/jpeg")) {
                $id('post_image_preview').appendChild(div);

                if (filearr0.length == 1) {
                    itag.setAttribute('fileid', filearr.length - 2 + 1);
                }
                if (filearr0.length > 1) {
                    itag.setAttribute('fileid', filearr.length - filearr0.length + i);
                }

                // console.log("Files " + filearr.length+" FILES_ARRAY "+ filearr + " I "+i);
            }
            else {
                console.log('unsupported format');
            }
            filearr.onload = function () {   //img.onload
                URL.revokeObjectURL(filearr); //img.src
            }
            itag.onclick = function (elem) {
                var fileid = this.getAttribute('fileid');
                this.parentElement.style.display = 'none';
                delete filearr[fileid];
                // filearr.splice(fileid,1);
                //console.log("Files "+filearr.length);
            }
            //console.log(url);
        }
    }


    /////////////////////////////////////LABEL_POSTS_TIMELINE///////////
    $scope.timeline = function () {
        $scope.timeline_show = false;
        $http({ method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'timeline', object_type: 'label_timeline' }) })
            .then(function (response) {
                $scope.timeline_preloader = true;
                $scope.timeline_show = true;
                $scope.timeline = response.data.record;
                if (response.data.record.length > 9) { $scope.arrows = true; }
                console.log(response.data.record);
            })
    }
    $scope.timeline();



    $scope.sendmessage_timeline = function () {
        var formdata = new FormData();
        var file = $id('timeline_file').files[0];
        var files = $id('timeline_file').files;
        formdata.append('ajax', 'sendmessage_timeline');
        formdata.append('message', he.encode($id('timeline_text').value));
        formdata.append('date', datejson());
        formdata.append('object_type', 'label_timeline');
        formdata.append('receiver', $location.path().split("/")[3]);
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
                // console.log(response.data);
                //alert(receiver);
                $scope.messageval = $id('timeline_text').value;

                if (response.data.status == '0') {
                    $scope.alert_show(response.data.text);
                }
                $id('timeline_text').value = "";
                //Make Empty input file and array with files
                $id('timeline_file').value = "";
                filearr_message = filearr_message.slice(0, 0);

                $id('upload_timeline_img_preview_block').classList.add('dp');
                $('.message_img_prev').remove();
                $scope.$broadcast('timeline_update', { receiver: label_number, type: 'label_timeline' });

            }
        })
    }
    $scope.open_graph_timeline = function (elem) {

        setTimeout(function () {
            $http({ method: 'POST', url: 'core/og2.php', data: $.param({ oglinkprev: elem.target.value }) })
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
                            var desc = he.encode($id('timeline_text').value);
                            $http({
                                method: 'POST',
                                url: 'ajax/ajax.php',
                                data: $.param({ ajax: 'sendmessage_timeline_og', title: title, desc: desc, og_desc: og_desc, url: url, image: image, date: datejson(), object_type: 'label_timeline' })
                            })
                                .then(function (response2) {
                                    $scope.messageval = $id('timeline_text').value;
                                    $scope.$broadcast('timeline_account_on');
                                    if (response2.data.status == '0') {
                                        $scope.alert_show(response2.data.text);
                                    }
                                    console.log(response2.data);
                                    $id('timeline_text').value = "";
                                    $id('timeline_file').value = "";
                                    $id('sender').classList.remove('og_btn');
                                    $id('chat_og_preview').classList.add('dp');
                                    $scope.clickenable = false;
                                    $scope.$broadcast('timeline_update', { receiver: label_number, type: 'label_timeline' });
                                });
                        });
                    };
                });
        });
    }
    $scope.createlabel = function () {
        // alert(get('label'));
        var labelname = $id('label_logo_input').value;
        var labellogo = $id('theme').value;
        var description = $id("label_description").value;
        var tags = $id("label_tags").value;
        var placename = $id("placename").value;
        var placeadress = $id("placeadress").value;
        var placeid = $id("placeid").value;
        var date = datejson();
        var social = JSON.stringify({
            vk: $name('social_vk').value,
            fb: $name('social_fb').value,
            insta: $name('social_insta').value,
            email: $name('email').value,
            phone: $id('phone').value
        });

        var data = {
            ajax: "createlabel",
            label: label_number,
            label_name: labelname,
            label_logo: labellogo,
            description: description,
            tags: tags,
            date: date,
            placename: placename,
            placeadress: placeadress,
            placeid: placeid,
            social: social
        };

        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param(data)
        }).then(function (response) {
            $scope.alert1 = 'true';
            alertbox[0].innerHTML = $filter('translate')(response.data.record);


        });
    }



    //////////////DELETE LABEL IMAGES////////////////////////

    $scope.deletelabelimage = function (id, file) {
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: "delete", table: "labelimages", id: id, file: file }),
        }).then(function (response) {
            console.log(response.data);
            $scope.getlabelimages();
        });
    }
    ///////////////DELETE POSTS///////////////

    $scope.deletepost = function (id, file) {
        $('#delete').modal({ show: 'true' });
        $('#accept_delete').on('click', function () {
            $http({
                method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: "deletepost", table: "posts", id: id, file: file }),
            }).then(function (response) {
                //console.log(response.data);
                $('#delete').modal('hide');
                $scope.getpostprivate(label_number);
            });
        });
    }

    ////LABEL IMAGES UPLOAD/////////////////
    $scope.imgupload = function () {

        var file = $id('labelimageloader').files[0];
        var formdata = new FormData();
        var reader = new FileReader();

        reader.onload = function () {
            var dataURL = reader.result;
            $id('upload_label_img_preview').src = dataURL;
        }
        reader.readAsDataURL(file);

        $('#upload_label_img_confirm').off('click').on('click', function () {

            $scope.preloader_block = true;
            formdata.append("ajax", "uploadlabelimages");
            formdata.append("label", label_number);
            formdata.append('title', he.encode($id('label_img_text').value));
            formdata.append("date", datejson());
            for (var i = 0, len = document.getElementById('labelimageloader').files.length; i < len; i++) {
                formdata.append("file" + i, document.getElementById('labelimageloader').files[i]);
            }

            $http({
                method: 'POST', url: 'ajax/ajax.php', data: formdata, headers: { 'Content-Type': undefined }
            }).then(function (response) {
                $scope.alert2 = 'true';
                alertbox[1].innerHTML = $filter('translate')(response.data.record);
                $scope.preloader_block = false;
                $scope.getlabelimages();
                //console.log(response.data);
            });

        })
    }
    $scope.uploadlabelimg_canvas = function () {

        html2canvas(document.querySelector('#upload_label_img_canvaspreview'), {
            onrendered: function (canvas) {
                $scope.preloader_block = true;
                var formdata = new FormData();
                var image = canvas.toDataURL('image/png', 1);
                $http({
                    method: 'POST', url: 'ajax/ajax.php', data: $.param({
                        ajax: "uploadlabelimages_canvas",
                        image: image,
                        label: label_number,
                        title: he.encode($id('comment_text_2').value),
                        date: datejson()
                    }),
                }).then(function (response) {
                    $scope.preloader_block = false;
                    $scope.alert2 = 'true';
                    alertbox[1].innerHTML = $filter('translate')(response.data.text);
                    $scope.getlabelimages();
                })
            },
        })
    }
    $scope.getlabelimages();
})
//////////////////////////////////Common Label//////////////////

com.controller('privatelabelctr', function ($scope, $location, $http, $filter) {

    $http.defaults.headers.post["Content-Type"] = "application/x-www-form-urlencoded";

    ////////////////////////////////////////
    $scope.logo_text = "Your Logo";
    var label_number = $location.path().split("/")[3];
    //alert(label_number);
    /////////////////////////////////////LABEL_POSTS_TIMELINE///////////
    $scope.timeline = function () {
        $scope.timeline_show = false;
        $http({ method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'timeline', object_type: 'label_timeline', limit: 10 }) })
            .then(function (response) {
                $scope.timeline_preloader = true;
                $scope.timeline_show = true;
                $scope.timeline = response.data.record;
                if (response.data.record.length > 9) { $scope.arrows = true; }
                console.log(response.data.record);
            })
    }
    $scope.timeline();

    $http({
        method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'gettheme' })
    }).then(function (response) { $scope.themeres = response.data.record; });

    $http({
        method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: 'getlabels_common', quantity: label_number })
    }).then(function (response) {
        $scope.onelabel = response.data.record[0];
        // console.log(response.data.record[0]);
        if (response.data.record[0] == undefined) { $scope.errorpage = true; } else { $scope.privatelabel = true; }
    });


    $http({
        method: 'POST', url: 'ajax/ajax.php', data: $.param({ ajax: "labelimages", label: label_number, status: 'null' }),
    }).then(function (response) {
        $scope.labelimagesall = response.data.record;

        if (response.data.record.length < 1) {
            $scope.no_label_images = true;
        }
        else {
            $scope.no_label_images = false;
        }
    });

    $scope.like_label = function (type, arg) {
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({
                ajax: 'likes',
                object_id: arg.id,
                account_receiver: arg.owner_id,
                object_type: type,
                date: datejson()
            })
        }).then(function (response) {
            // console.log(response.data);
            $scope.likes_count_label = parseInt($scope.likes_count_label) + parseInt(response.data.statuslike);
        })
    }

    $scope.likestatus_label = function (obj, type) {
        $http({
            method: 'POST', url: 'ajax/ajax.php', data: $.param({
                ajax: 'likestatus',
                object_id: obj,
                object_type: type,
                date: datejson()
            })
        }).then(function (response) {
            $scope.likestatus_label = response.data.likestatus;
            $scope.likes_count_label = response.data.likes_count;
            //console.log("likestatus "+response.data.likestatus+" countlikes"+response.data.likes_count);
            // console.log(response.data);
        })
    }
    $scope.likestatus_label(label_number, 'label');

    $scope.$on('$viewContentLoaded', function (event) {

    })


})





