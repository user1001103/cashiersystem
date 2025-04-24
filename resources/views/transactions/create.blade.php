@extends('layouts.master')
@section('title' , 'انشاء ملاجظة جديدة')
@section('content')
@if ($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <h4 class="alert-heading text-center">Error!</h4>
    <hr>
    <ul class="list-unstyled mb-0">
        @foreach ($errors->all() as $error)
            <li class="text-danger">
                <i class="fas fa-exclamation-circle mr-2"></i>
                {{ $error }}
            </li>
        @endforeach
    </ul>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif
<div class="container-fluid">
    <div class="row justify-content-center">
      <div class="col-12">
        <h2 class="page-title">اضافة ملاحظة جديدة</h2>
        <div class="card-deck">
          <div class="card shadow mb-4">
            <div class="card-header">
              {{-- <strong class="card-title">قسم : </strong><span>{{ App\Models\Section::whereId($section_id)->first()->name }} </span> --}}
            </div>
            <div class="card-body">
              <form action="{{ route('transactions.store') }}" method="POST">
                @csrf
                {{-- <input type="hidden" name="section_id" value="{{ $section_id }}"> --}}

                <!-- Name Field -->
                <div class="form-row">
                  <div class="form-group col-md-6">
                    <label for="name">اسم العامل</label>
                    <input name="name" value="{{ old('name') }}" type="text" class="form-control" id="name" placeholder="أدخل اسم العامل">
                  </div>

                  <!-- Note Field -->
                  <div class="form-group col-md-6">
                    <label for="note">الملاحظة</label>
                    <textarea name="note" id="note" rows="4" class="form-control" placeholder="أدخل الملاحظة">{{ old('note') }}</textarea>
                  </div>
                </div>

                <!-- Submit Button -->
                <div class="form-group text-center">
                  <button type="submit" class="btn btn-primary">حفظ الملاحظة</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div> <!-- .col-12 -->
    </div> <!-- .row -->
</div> <!-- .container-fluid -->
@endsection
@section('script')
<script src="/js/jquery.min.js"></script>
<script src="/js/popper.min.js"></script>
<script src="/js/moment.min.js"></script>
<script src="/js/bootstrap.min.js"></script>
<script src="/js/simplebar.min.js"></script>
<script src='/js/daterangepicker.js'></script>
<script src='/js/jquery.stickOnScroll.js'></script>
<script src="/js/tinycolor-min.js"></script>
<script src="/js/config.js"></script>
<script src="/js/apps.js"></script>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-56159088-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];

  function gtag()
  {
    dataLayer.push(arguments);
  }
  gtag('js', new Date());
  gtag('config', 'UA-56159088-1');
</script>
@endsection
