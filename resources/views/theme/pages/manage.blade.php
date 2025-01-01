@extends('theme.layouts.app')
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
@endpush
@section('content')
<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-12">
      @if ($errors->any())
      <div class="alert alert-danger">
        <ul>
          @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
      @endif
      @if(session('success'))
      <div class="alert alert-success">
        {{session('success')}}
      </div>
      @endif
      @if(session('error'))
      <div class="alert alert-danger">
        {{session('error')}}
      </div>
      @endif
    </div>

    <div class="col-md-12 form_page">
      <form action="{{ $form_action }}" class="" method="post">
        @csrf
        @if($edit)
        <input type="hidden" value="{{$data->id}}" name="id">
        @endif

        <div class="card">
          <div class="card-body">
            <div class="row form_sec">
              <div class="col-12">
                <h5>Basic Details</h5>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="name">Name</label>
                  <input type="text" name="name" class="form-control" @if($edit) value="{{$data->name}}" @else value="{{old('name')}}" @endif id="name" aria-describedby="nameHelp">
                  <small id="nameHelp" class="form-text text-muted"></small>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="slug">Slug</label>
                  <input type="text" name="slug" class="form-control" @if($edit) value="{{$data->slug}}" @else value="{{old('slug')}}" @endif id="slug" aria-describedby="slugHelp">
                  <small id="slugHelp" class="form-text text-muted"></small>
                </div>
              </div>
              <div class="col-md-12">
                <div class="form-group">
                  <label for="content">Content</label>
                  <textarea id="editor" class="form-control" name="content"> @if($edit) {{ html_entity_decode($data->content) }} @endif</textarea>
                  <small id="contentHelp" class="form-text text-muted"></small>
                </div>
              </div>
            </div>
          </div>
        </div>
        <br />
        <div class="row">
          <div class="col-md-12">
            <button type="submit" class="btn btn-primary add_site">
              @if($edit)
              Update Changes
              @else
              Add Page
              @endif
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
@push("scripts")
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>

<script>
  $('textarea#editor').summernote();
</script>
<script type="text/javascript">
  $(document).ready(function() {
    $(".add_site").click(function(e) {
      $(this).addClass('disabled');
    });
  });
</script>
@endpush