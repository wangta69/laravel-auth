<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Verify Email Address</title>
  </head>
  <body>

    <div style="width: 680px;padding: 35px;font-size: 12px;font-family: dotum;line-height: 20px;border: 1px solid #0000002d;">
      <div style="border-bottom: 3px solid #0000002d;padding-bottom: 5px; font-size: 20px;">
        <a href="{{ config('app.url') }}" target="_blank" rel="noreferrer noopener" style="text-decoration: none;color: inherit;">{{ config('app.name') }}</a>
      </div>

      <div style="padding: 20px 5px;">
        안녕하세요.  {{$notifiable->name}} 님 <br>
      </div>


      <div style="padding: 20px;">

      You are receiving this email because we received a 2fa reset request for your account.<br>
            <br />
          <a href="{{$actionUrl}}" target="_new">Reset 2fa</a><br />
      </div>

      <div style="border-top: 1px solid #cccccc;padding: 10px;border-bottom: 1px solid #cccccc; color: #aaa; font-size: 11px;">
      
    
If you're having trouble clicking the "Reset 2fa" button, copy and paste the URL below into your web browser: {{$actionUrl}}
    </div>

    </div>
  <!-- Body End -->
  </body>
</html>