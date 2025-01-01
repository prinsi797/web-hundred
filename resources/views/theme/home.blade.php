@extends('theme.layouts.app')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
  .select2-container {
    min-width: 300px;
  }

  .select2-container .select2-selection--single {
    height: 35px;
  }

  .select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 35px;
  }

  .download-link {
    color: blue;
    font-weight: bolder;
  }

  .browse_box {
    width: 300px;
    color: #f2f2f2;
    display: flex;
    padding: 10px;
    align-items: center;
    text-align: center;
    background-color: #333;
    cursor: pointer;
  }

  .uploader-style {
    width: 100%;
  }

  .preview-file {
    border: 2px dotted #333;
    border-radius: 5px;
    padding: 10px;
    background-color: #e4f3df;
    display: none;
    text-align: left;
  }

  .process-btn {
    display: none;
  }

  .cancelFileUpload {
    cursor: pointer;
    /* color: red; */
  }

  .file-upload {
    border: 2px solid #ddd;
    border-radius: 5px;
    padding: 10px;
    background-color: #f2f2f2;
  }

  .custom-file-input {
    /* opacity: 1 !important; */
    height: 200px;
    cursor: pointer;
    position: absolute;
  }

  .drop_box {
    border: 2px solid #ddd;
    border-radius: 5px;
    height: 200px;
    cursor: pointer;
    width: 100%;
    color: #9b9696;
    padding: 10px;
    background-color: #f2f2f2;
  }

  .drop_box .row {
    height: 100%;
  }
</style>
@endpush

@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-md-12">
      @if (session('status'))
      <div class="alert alert-success" role="alert">
        {{ session('status') }}
      </div>
      @endif
      @if (session('success'))
      <div class="alert alert-success" role="alert">
        {{ session('success') }}
      </div>
      @endif
      @if (session('error'))
      <div class="alert alert-danger" role="alert">
        {{ session('error') }}
      </div>
      @endif

      @if (isset($errors) && $errors->any())
      <div class="my-3">
        <ul class="list-group list-group-flush">
          @foreach ($errors->all() as $error)
          <li class="list-group-item list-group-item-danger">{{ $error }}</li>
          @endforeach
        </ul>
      </div>

      @endif
    </div>
  </div>
  @if(isset($user) && $user->is_admin)
  <div class="row">
    @foreach($dashboard_cards as $card)
    <div class="col-md-6 col-lg-3">
      <a href="{{ ($card[2]) }}" class="card-hover">
        <div class="widget-small primary coloured-icon">
          <i class="icon {{ $card[3] }} fa-3x"></i>
          <div class="info">
            <h4>{{ $card[0] }}</h4>
            <p><b><?php echo number_format($card[1]); ?></b></p>
          </div>
        </div>
      </a>
    </div>
    @endforeach
  </div>
  
  @else

  <div class="row">
    @foreach($dashboard_cards as $card)
    <div class="col-md-6 col-lg-3">
      <a href="{{ ($card[2]) }}" class="card-hover">
        <div class="widget-small primary coloured-icon">
          <i class="icon {{ $card[3] }} fa-3x"></i>
          <div class="info">
            <h4>{{ $card[0] }}</h4>
            <p><b><?php echo number_format($card[1]); ?></b></p>
          </div>
        </div>
      </a>
    </div>
    @endforeach
  </div>
</div>
@endif
@endsection
