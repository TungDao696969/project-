@extends('admin.layouts.main')

@section('title')
    @parent
    Them moi danh muc
@endsection

@section('content')
    <form action="{{ route('admin.categories.addPostCategory') }}" method="post">
        @csrf
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="row">
                        <div class="col-sm-8 m-auto">
                            <div class="card">
                                <div class="card-body">
                                    <div class="card-header-2">
                                        <h5>ADD Category</h5>
                                    </div>

                                    <div class="theme-form theme-form-2 mega-form">
                                        <div class="mb-4 row align-items-center">
                                            <label class="form-label-title col-sm-3 mb-0">Name: </label>
                                            <div class="col-sm-9">
                                                <input class="form-control" name="name" id="name" type="text"
                                                    placeholder="Category Name">
                                                @if ($errors->has('name'))
                                                    <div class="text-danger">{{ $errors->first('name') }}</div>
                                                @endif
                                            </div>
                                        </div>


                                        <div class="mb-4 row align-items-center">
                                            <label class="form-label-title col-sm-3 mb-0">Description: </label>
                                            <div class="col-sm-9">
                                                <textarea name="description" id="description"></textarea>
                                                @if ($errors->has('description'))
                                                    <div class="text-danger">{{ $errors->first('description') }}</div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="mb-4 row align-items-center">
                                            <label class="form-label-title col-sm-3 mb-0">Parent Category: </label>
                                            <div class="col-sm-9">
                                                <select name="parent_id" id="parent_id">
                                                    <option value="">None</option>
                                                    @foreach ($categories as $cat)
                                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                                    @endforeach
                                                </select>
                                                @if ($errors->has('parent_id'))
                                                    <div class="text-danger">{{ $errors->first('parent_id') }}</div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="mb-4 row align-items-center">
                                            <label class="form-label-title col-sm-3 mb-0">Status: </label>
                                            <div class="col-sm-9">
                                                <select name="status" id="status">
                                                    <option value="1">Active</option>
                                                    <option value="0">Inactive</option>
                                                </select>
                                                @if ($errors->has('status'))
                                                    <div class="text-danger">{{ $errors->first('status') }}</div>
                                                @endif
                                            </div>
                                        </div>

                                    </div>
                                    <button class="btn btn-success" type="submit">Save</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <a href="{{ route('admin.categories.listCategory') }}" class="btn btn-theme">Quay lại</a>
    </form>
@endsection
