# 라라벨용 배송 추적 패키지

## Installation
```
composer require wangta69/laravel_delivery_tracking
```

## How to Use
### call main page
yourdomain.com/delivery-tracking
### call available couriers
```
yourdomain.com/delivery-tracking/couriers
```

### call delivery history
```
yourdomain.com/delivery-tracking/{courier}/{invoicenumber}/{type?}
```
#### type
- default: html
  - html 창으로 출력
- json : json 형식으로 출력

### Use in your controller;
> 컨트롤러나 다른 클래스에서 직접 사용할 경우 아래와 같이 처리하시면 됩니다.
```
use Pondol\DeliveryTracking\Traits\Tracking;
..........
class YourController  {
  use Tracking;

  public function tracking(Request $request, $courier, $invoicenumber) {
    $this->_tracking($courier, $invoicenumber); // ['error', 'status', 'logs']; // 배송 로그 조회
    $this->_couriers(); // ['error', 'status', 'logs']; // 서비스 중인 택배사 정보 조회
  }
}
```
## Error Code
> 정상적으로 진행이되면 error는 false가 출력됩니다. <br>
> 그렇지 않을 경우 각각 다음과 같은 에러가 출력됩니다.
- numberValidaionError : 송장값이 잘못된 경우
- numberValidaionErrorOrnoData : 송장값이 잘못되었거나 데이타가 없을 경우
- courierNotFoundError : 택배사가 존재하지 않는 경우
 
## 서비스 가능 택배사
<table>
  <tr>
    <td>
      CJ대한통운 (CJGLS)
    </td>
    <td>
      우체국택배 (EPOST)
    </td>
    <td>
      한진택배 (HANJIN)
    </td>
  </tr>
   <tr>
    <td>
      경동택배 (KDEXP)
    </td>
    <td>
      로젠택배 (KGB)
    </td>
    <td>
      롯데택배 (LOTTE)
    </td>
  </tr>
</table>
