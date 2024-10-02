@extends('tracking::app')
@section('title', 'HOME')

@section('content')

<div class="container-fluid">

@if($result->error)
  <div class="card">
    @if($result->error == 'courierNotFoundError')
    <div class="card-body">
    존재하지 않는 택배사 입니다.
    </div>
    @elseif($result->error == 'numberValidaionError' || $result->error == 'numberValidaionErrorOrnoData')
    <div class="card-body">
    송장번호가 존재하지 않습니다.
    </div>
    @endif
  </div>
@else
  <div class="card">
    <div class="card-header">
      <span>배송조회</span>
    </div>
    <div class="card-body">
    <div class="row">
        <div class="col">진행상태</div>
        <div class="col-9">{{$result->status}}</div>
      </div>
      <div class="row">
        <div class="col">택배사</div>
        <div class="col-9">{{$result->courier['name']}}</div>
      </div>
      <div class="row">
        <div class="col">송장번호</div>
        <div class="col-9">{{$result->invoicenumber}}</div>
      </div>
      <div class="row">
        <div class="col"></div>
        <div class="col-9"><a class="btn btn-info btn-sm" href="{{$result->courier['query_url']}}" target="_new">직접조회</a></div>
      </div>
    </div>
    <div class="card-header">
      <span>진행상태</span>
    </div>
    <div class="card-body">
      <table class="table">
        <thead>
          <tr>
            <th>날짜</th>
            <th>사업장</th>
            <th>배송상태</th>
            <th>내용</th>
          </tr>
        </thead>
        <tbody class="table-group-divider">
          
          @forelse ($result->logs as $log)
          <tr>
            <td>{{$log['time']}}</td>
            <td>{{$log['location']}}</td>
            <td>{{$log['status']}}</td>
            <td>
              {{$log['statusmsg']}} 
              @if($log['deliveryPerson'])
              (담당자: {{$log['deliveryPerson']}} @if($log['contact']) {{$log['contact'] }}@endif)
              @endif
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="4">배송정보가 존재하지 앟습니다.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
@endif


</div>
@endsection

@section ('styles')
@parent
@endsection

@section ('scripts')
@parent
@endsection
