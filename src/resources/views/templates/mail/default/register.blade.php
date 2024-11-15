<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>회원가입축하메일</title>
  </head>
  <body>

    <div style="width: 680px;padding: 35px;font-size: 12px;font-family: dotum;line-height: 20px;border: 1px solid #0000002d;">
      <div style="border-bottom: 3px solid #0000002d;padding-bottom: 5px; font-size: 20px;">
        <a href="{{ config('app.url') }}" target="_blank" rel="noreferrer noopener" style="text-decoration: none;color: inherit;">{{ config('app.name') }}</a>
      </div>

      <div style="padding: 20px 5px;">
        안녕하세요. {{$notifiable->name}} 님 <br>
      </div>


      <div style="padding: 20px;">
            <span color=#FF6600>{{config('app.name')}}</span> 회원이 되신것을 진심으로 환영합니다.<br>
            <br />
            회원님이 가입하신 정보는 아래와 같습니다. <br>
            <br />
          <span>email : {{$notifiable->email}}</span><br />
      </div>

      <div style="border-top: 1px solid #cccccc;padding: 10px;border-bottom: 1px solid #cccccc; color: #aaa; font-size: 11px;">
      </div>

    </div>
  <!-- Body End -->
  </body>
</html>