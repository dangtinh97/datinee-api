<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Xác nhận tài khoản email</title>
</head>
<body>
    <input id="token" type="text" style="width: 100%;position: absolute;top: -100px" value="{{$response}}">
<p>
    @if($response==="")
        <strong>Xác nhận không hợp lệ.</strong>
    @else
        <strong>Xác nhận hợp lệ.</strong>
        <button type="button" onclick="copyLink()">Mở app</button>
    @endif
</p>
<script>

    function copyLink()
    {
        let input =document.getElementById('token')
        if (navigator.userAgent.match(/ipad|ipod|iphone/i)) {
            var el = input;
            var editable = el.contentEditable;
            var readOnly = el.readOnly;
            el.contentEditable = true;
            el.readOnly = true;
            var range = document.createRange();
            range.selectNodeContents(el);
            var sel = window.getSelection();
            sel.removeAllRanges();
            sel.addRange(range);
            el.setSelectionRange(0, 999999);
            el.contentEditable = editable;
            el.readOnly = readOnly;
        } else {
            input.select();
        }

        try {
            document.execCommand("copy");  // Security exception may be thrown by some browsers.
            console.log("Copied the text: " + input.value);
        } catch (error) {
            console.warn("Copy to clipboard failed.", error);
            return false;
        }
        input.blur();
    }

</script>
</body>
</html>
