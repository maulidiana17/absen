<!--
    =========================================================
    * Material Dashboard 3 - v3.2.0
    =========================================================

    * Product Page: https://www.creative-tim.com/product/material-dashboard
    * Copyright 2024 Creative Tim (https://www.creative-tim.com)
    * Licensed under MIT (https://www.creative-tim.com/license)
    * Coded by Creative Tim

    =========================================================

    * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
    -->
    <!DOCTYPE html>
    <html lang="en">

    <head>
      <meta charset="utf-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
      <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('admin/assets/img/apple-icon.png') }}">
      <link rel="icon" type="image/png" href="{{ asset('assets/img/smp.png') }}">
      <title>
        Login Admin dan Guru SMPN 1 GENTENG
      </title>
      <!--     Fonts and icons     -->
      <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />
      <!-- Nucleo Icons -->
      <link href="{{ asset('admin/assets/css/nucleo-icons.css') }}" rel="stylesheet" />
      <link href="{{ asset('admin/assets/css/nucleo-svg.css') }}" rel="stylesheet" />
      <!-- Font Awesome Icons -->
      <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
      <!-- Material Icons -->
      <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
      <!-- CSS Files -->
      <link id="pagestyle" href="{{ asset('admin/assets/css/material-dashboard.css?v=3.2.0') }}" rel="stylesheet" />
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    </head>

    <body class="bg-gray-200">
      <main class="main-content mt-0">
        <div class="page-header align-items-start min-vh-100" style="background-image: url('assets/img/smp1.jpg');">
          <span class="mask bg-gradient-dark opacity-6"></span>
          <div class="container my-auto">
            <div class="row">
              <div class="col-lg-4 col-md-8 col-12 mx-auto">
                <div id="loginCard"  class="card z-index-0 fadeIn3 fadeInBottom">
                  <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-dark shadow-dark border-radius-lg py-3 pe-1">
                       <img id="logoImg"
                        src="{{ asset('admin/assets/img/smp.png') }}"
                        data-siswa-img="{{ asset('admin/assets/img/logo-siswa.png') }}"
                        data-default-img="{{ asset('admin/assets/img/smp.png') }}"
                        alt="Logo"
                        style="width: 100px; display: block; margin: 0 auto;">

                      <h4 id="welcomeText" class="text-white font-weight-bolder text-center mt-2 mb-0">
                        Selamat Datang
                        <div class="col">
                            Silahkan Login !
                            <pre>
                            session id: {{ session()->getId() }}
                            token: {{ csrf_token() }}
                            </pre>
                        </div>
                     </h4>

                    </div>
                  </div>
                  <div class="card-body">
                    <form action="/proseslogin" method="POST" role="form" class="text-start">
                        {{--  <input type="hidden" name="_token" value="{{ csrf_token() }}">  --}}
                       @csrf
                        <label class="form-label">Login Sebagai</label>
                        <div class="input-group input-group-outline my-3">
                            <select name="login_sebagai" class="form-control" required>
                                <option value="">Pilih</option>
                                <option value="admin_guru">Admin / Guru</option>
                                <option value="siswa">Siswa</option>
                            </select>
                        </div>

                        <!-- Input Email untuk Admin/Guru -->
                        <div class="input-group input-group-outline my-3" id="emailField">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control">
                        </div>

                        <!-- Input NIS untuk Siswa -->
                        <div class="input-group input-group-outline my-3 d-none" id="nisField">
                            <label class="form-label">NIS</label>
                            <input type="text" name="nis" class="form-control">
                        </div>

                        <div class="input-group input-group-outline mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <div class="form-check form-switch d-flex align-items-center mb-3">
                            <input class="form-check-input" type="checkbox" id="rememberMe">
                            <label class="form-check-label mb-0 ms-3" for="rememberMe">Remember me</label>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn bg-gradient-dark w-100 my-4 mb-2">Sign In</button>
                        </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <footer class="footer position-absolute bottom-2 py-2 w-100">
            <div class="container">
              <div class="row align-items-center justify-content-lg-between">
                <div class="col-12 col-md-6 my-auto">
                  <div class="copyright text-center text-sm text-white text-lg-start">
                   COPYRIGHT © <script>
                      document.write(new Date().getFullYear())
                    </script>,
                        SMP NEGERI 1 GENTENG
                  </div>
                </div>

              </div>
            </div>
          </footer>
        </div>
      </main>
      <!--   Core JS Files   -->
      <script src="{{ asset('admin/assets/js/core/popper.min.js') }}"></script>
      <script src="{{ asset('admin/assets/js/core/bootstrap.min.js') }}"></script>
      <script src="{{ asset('admin/assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
      <script src="{{ asset('admin/assets/js/plugins/smooth-scrollbar.min.js') }}"></script>
      <script>
        var win = navigator.platform.indexOf('Win') > -1;
        if (win && document.querySelector('#sidenav-scrollbar')) {
          var options = {
            damping: '0.5'
          }
          Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
        }
      </script>
      <script>
    const loginRole = document.querySelector('select[name="login_sebagai"]');
    const emailField = document.getElementById('emailField');
    const nisField = document.getElementById('nisField');
    const loginCard = document.getElementById('loginCard');
    const labelLoginRole = document.getElementById('labelLoginRole');
    const logoImg = document.getElementById('logoImg');
    const welcomeText = document.getElementById('welcomeText');

    loginRole.addEventListener('change', function () {
        const allLabels = document.querySelectorAll('label');

        if (this.value === 'siswa') {
            emailField.classList.add('d-none');
            nisField.classList.remove('d-none');

            //loginCard.style.backgroundColor = '#8B0000';
            //loginCard.style.color = 'white';

            //allLabels.forEach(label => label.classList.add('text-white'));
            //labelLoginRole?.classList.add('text-white');

            logoImg.src = logoImg.dataset.siswaImg;
            welcomeText.innerHTML = `Selamat Datang<div class="col">Siswa, silahkan login!</div>`;

            welcomeText.classList.remove('animate__animated', 'animate__fadeIn');
            void welcomeText.offsetWidth;
            welcomeText.classList.add('animate__animated', 'animate__fadeIn');

        } else if (this.value === 'admin_guru') {
            emailField.classList.remove('d-none');
            nisField.classList.add('d-none');

            loginCard.style.backgroundColor = '';
            loginCard.style.color = '';

            allLabels.forEach(label => label.classList.remove('text-white'));
            labelLoginRole?.classList.remove('text-white');

            logoImg.src = logoImg.dataset.defaultImg;
            welcomeText.innerHTML = `Selamat Datang<div class="col">Silahkan Login!</div>`;
        } else {
            emailField.classList.add('d-none');
            nisField.classList.add('d-none');

            loginCard.style.backgroundColor = '';
            loginCard.style.color = '';

            allLabels.forEach(label => label.classList.remove('text-white'));
            labelLoginRole?.classList.remove('text-white');

            logoImg.src = logoImg.dataset.defaultImg;
            welcomeText.innerHTML = `Selamat Datang<div class="col">Silahkan Login!</div>`;
        }
    });
</script>



      <!-- Github buttons -->
      <script async defer src="https://buttons.github.io/buttons.js"></script>
      <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
      <script src="{{ asset('admin/assets/js/material-dashboard.min.js?v=3.2.0') }}"></script>
    </body>

    </html>
