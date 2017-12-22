var swcore = require('./js/nodecommon');
var express = require('express');
var https = require('https');
var mysql = require('mysql');
var requestify = require('requestify');
var request = require('request');
var app = express();
var hash = 16 * 3 * 1992;
var host = 'https://sweetvel.com/';
var room;
var room_prev;
var opponent_status;
var clients;
var online_status;
const fs = require('fs');

const options_ssl_opt = {
        key  : fs.readFileSync("/etc/letsencrypt/archive/sweetvel.com/privkey1.pem"),
        cert : fs.readFileSync("/etc/letsencrypt/archive/sweetvel.com/fullchain1.pem"),
        ca   : fs.readFileSync("/etc/letsencrypt/archive/sweetvel.com/chain1.pem")
};
var server = https.createServer(options_ssl_opt,app);///Все наше Express приложение будет уже на сеервере мы как бы завернули его в server

server.listen(3000, function () { console.log('listening on *:4000') });
var io = require('socket.io').listen(server);
io.use(function (socket, next) {

    var handshakeData = socket.request;
    //var  handshakeData = socket.handshake;
    requestify.get(host + 'core/auth.php', {
        dataType: 'json',
        headers: {
            //'Cookie': 'global_sesid=dmko9bdhlm6pr66p3gpj6reid3', Установка куки вручную
            'Cookie'          : handshakeData.headers["cookie"],
            'User-Agent'      : handshakeData.headers["user-agent"],
            'X-Requested-With': 'XMLHttpRequest'
        }
    }).then(function (response) {
        var data = JSON.parse(response.getBody())
        handshakeData.user = data;
        socket.id = data.id * hash
        //console.log(data);
        next();
    }, function (err) {
    });
});

try {
    
    io.on('connection', function (socket) {

        var cookie_header    = socket.request.headers["cookie"];
        var useragent_header = socket.request.headers["user-agent"];
        
        try { swcore.online_status(host, request, cookie_header, useragent_header, 1); } catch (err) { console.log(err) }

        var user = socket.request.user;
        user.typer = socket.id;

        room_prev = user.id * hash;

        if (!io.sockets.adapter.sids[socket.id][room_prev]) {
            socket.join(room_prev);

            console.log(user.sender + '(' + socket.id + ')' + " joined prev_room # ", room_prev);
        }
        else {
            //console.log(io.sockets.adapter.rooms[room_prev]);  
            console.log(' User ' + socket.id + ' is authorized' + ' room ' + room_prev)
        }
        /////////////////////////////////////////////////////////
        var reverse = function (a, b) { return Math.min(a, b) + "-" + Math.max(a, b); }

        socket.on('opponent', function (data) {
            room = reverse(user.id, data);
            opponent = data * hash;
            user.opponent = opponent;
            if (!io.sockets.adapter.sids[socket.id][room]) {
                socket.join(room);
                console.log(user.sender + '(' + socket.id + ')' + " joined room # ", room);
            }
            io.to(room).emit('read_chat', 1);
            io.to(user.opponent).emit('read_chat', { opponent: socket.id });
            try {

                for (clients in io.sockets.adapter.rooms[room].sockets) {
                    clients = io.sockets.adapter.rooms[room].sockets;
                    clients['room'] = room;
                    online_status = clients[opponent] ? 1 : 0;
                }
                console.log(clients);
            } catch (err) { console.log(err) }

            io.to(room).emit('opp_online_status', { status: online_status, username: user.sender });/////////SET OPPONENT STATUS ONLINE

        })
        ///////////////////LEAVE_CHAT_ROOM/////////
        socket.on('leave_room', function () {
            console.log(user.sender + '(' + socket.id + ')' + ' Left room # ' + room);/////////SET OPPONENT STATUS OFFLINE

            socket.leave(room, function () {

                online_status = clients[socket.id] ? 1 : 0;

                io.to(room).emit('opp_online_status', { status: online_status, username: user.sender });
            });

        })
        ///////////GET CHAT_MESSAGE////
        socket.on('chat_msg', function (data) {
            try {
                user.id        = data.id;   
                user.sender_id = socket.id;
                user.message   = data.message;
                user.image     = data.image;
                user.status    = online_status ? 1 : 0;
                user.hours     = new Date().getHours();
                user.minutes   = new Date().getMinutes();
                user.og_params = data.og_params;
                user.audio     = data.audio;
                user.whosend   = socket.id == opponent ? 1 : 0;

                console.log(user);
                io.to(room).emit('chat_msg_client', user);
                io.to(user.opponent).emit('chat_msg_messenger_previous', user);

            } catch (err) { console.log(err); }
        })
        //////////////////////////USER_IS_TYPING////////////

        socket.on('user_typing', function () { 
            io.to(room).emit('user_typing_emit', user);console.log(room);
         })

        //////////////////////////LEAVE_ROOM_PREV//////////

        socket.on('disconnect', function () {

            try { swcore.online_status(host, request, cookie_header, useragent_header, 0); } catch (err) { console.log(err) }

            console.log(user.sender + ' disconnected from room # ' + room + ' and ' + room_prev);
            socket.leave(room);
            socket.leave(room_prev);

        });

        socket.on('user_typing', function (data) {
            if (user.id * hash !== user.opponent) {
                io.to(user.opponent).emit('chat_msg_messenger', user);
            }
        })

    });


}
catch (err) { console.log(err) }