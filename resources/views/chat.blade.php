<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <title>Chat</title>
    <style>
        .chat{
            border: 1px solid red;
            border-radius: 10px;
            padding: 3px 5px;
            width: max-content;
        }
    </style>
</head>
<body>
<div class="container">
    <div>
        {{--        <p class="text-wrap text-break">token: {{$token}}</p>--}}
        <p>FROM USER: {{$user['user_oid']}}</p>
        <p>WITH USER: {{$dataChat['to_user']['user_oid']}}</p>
        <p id="socket_id"></p>
    </div>
    <div id="form-chat" class="row g-2">
        <div class="form-group col-11">
            {{--            <label>Nội dung tin nhắn</label>--}}
            <textarea placeholder="Nội dung tin nhắn" class="form-control " id="message_text"></textarea>
        </div>
        <div class="col-1 pt-2">
            <button type="button" id="send_text" class="btn btn-primary">Gửi</button>
        </div>
    </div>
    <div id="history-chat">
        @foreach($dataChat['messages'] as $message)
            <p class="chat from-me">{{\App\Helpers\StrHelper::securedDecrypt($message['message'],md5($dataChat['room_oid']))}}</p>
        @endforeach
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
<script
    src="https://code.jquery.com/jquery-3.6.0.min.js"
    integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
    crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/4.4.1/socket.io.js"
    integrity="sha512-MgkNs0gNdrnOM7k+0L+wgiRc5aLgl74sJQKbIWegVIMvVGPc1+gc1L2oK9Wf/D9pq58eqIJAxOonYPVE5UwUFA=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.0.0/crypto-js.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {

        const EVENT = {
            SEND_MESSAGE:'SEND_MESSAGE'
        }

        const TYPE_MESSAGE = {
            TEXT:'TEXT'
        }

        var Sha256 = CryptoJS.SHA256;
        var Hex = CryptoJS.enc.Hex;
        var Utf8 = CryptoJS.enc.Utf8;
        var Base64 = CryptoJS.enc.Base64;
        var AES = CryptoJS.AES;
        let oidMessage = null
        const socket = io("http://localhost:3000", {
            auth:{
                token:'{{$user['token']}}'
            },
            query:{
                room_oid:'{{$dataChat['room_oid']}}'
            },
            transports: ["websocket", "polling"] // use WebSocket first, if available
        });


        $(this).on("click",'#send_text',function (){
            let message = $("#message_text").val().trim()
            if(message==="") return alert("Ê điền nội dung tin nhắn đi đã")
            sendMessageText(message)

        })

        function sendMessageText(message){
            socket.emit(EVENT.SEND_MESSAGE,{
                data:encrypt({
                    type:TYPE_MESSAGE.TEXT,
                    message:message,
                    data:[]
                })
            })
        }

        function encrypt(data={})
        {
            let key = '{{md5($dataChat['room_oid'])}}'
            let iv = key.substring(0,16)
            return  AES.encrypt(JSON.stringify(data), Utf8.parse(key), {
                iv: Utf8.parse(iv),
                padding: CryptoJS.pad.Pkcs7,
                mode: CryptoJS.mode.CBC
            }).toString();

        }

    })
</script>
</body>
</html>
