<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Registration Cancelled</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
</head>

<body bgcolor="#FFFFFF" class="container">
<div id="wrapper">
    <div id="meeting_register_container">
        <input type="hidden" id="meeting_number" value="343072140">
        <div class="page-header custom_logo">
            <h2>
                Meeting Approvation Success
            </h2>
        </div>
        <div class="row" style="display:table; margin-top: 20px">
            <div class="webinar_topic">
                <div class="form-group horizontal">
                    <label class="control-label">Topic: <strong>{{$meeting->topic}}</strong></label>
                </div>
                <div class="form-group horizontal" style="margin-top: 20px">
                    <label class="control-label">Time: <strong>{{date(config('zoom.emails_date_format'), strtotime($occurrence->start_time))}} in {{$meeting->timezone}}</strong></label>
                </div>
                <div class="form-group horizontal" style="margin-top: 20px">
                    <label class="control-label">Registrant: <strong>{{$registrant->fullName()}} ({{$registrant->email}})</strong></label>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>