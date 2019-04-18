<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{$info->name}}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="{{asset("css/location.css")}}" rel="stylesheet" type="text/css"/>
</head>
<body>
    <div id="templatemo_wrapper">
        <div id="templatemo_header">
            <h2>{{$info->name}}</h2>
        </div>

        <div id="templatemo_main">
            <div class="cbox_fw">
                <img src="{{asset("images/markers/" . $info->photo)}}" alt="image" class="image_frame image_fl" width="100%"/>
                {!! $info->description !!}
                <div class="cleaner"></div>
            </div>
            <div id="sidebar">
            </div>

            <div id="content">
                <h3></h3>
                <div class="cleaner"></div>
            </div>

            <div class="cleaner"></div>
        </div>
        <div id="templatemo_main_bottom"></div>

        <div id="templatemo_footer">
            Time Travellers {{date("Y")}}

            <div class="cleaner"></div>
        </div>
    </div>
</body>
</html>
