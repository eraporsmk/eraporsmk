@extends('layouts.adminpages') 

@section('content')

    <main role="main" class="col-md-9 ml-sm-auto col-lg-10 pt-3 px-4">
      
      <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2">Dashboard</h1>
      </div>

      <div class="row">
          <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
            <div class="info-box facebook-bg">
              <i class="fa fa-cloud-download"></i>
              <div class="count">{{$n_users}}</div>
              <div class="title">Users</div>
            </div>
            <!--/.info-box-->
          </div>
          <!--/.col-->

          <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
            <div class="info-box twitter-bg">
              <i class="fa fa-shopping-cart"></i>
              <div class="count">{{$n_roles}}</div>
              <div class="title">Roles</div>
            </div>
            <!--/.info-box-->
          </div>
          <!--/.col-->

          <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
            <div class="info-box teal-bg">
              <i class="fa fa-thumbs-o-up"></i>
              <div class="count">{{$n_perms}}</div>
              <div class="title">Permissions</div>
            </div>
            <!--/.info-box-->
          </div>
          <!--/.col-->

          <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
            <div class="info-box lime-bg">
              <i class="fa fa-cubes"></i>
              <div class="count">{{$n_logged}}</div>
              <div class="title">Logged In</div>
            </div>
            <!--/.info-box-->
          </div>
          <!--/.col-->

        </div>
    </main>
  </div>
</div>
@endsection