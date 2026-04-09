  <?php

  use Illuminate\Support\Facades\Route;

  Route::get('/user', function () {
      return view('createuser.createuser');
  })->name('dashboard.user');