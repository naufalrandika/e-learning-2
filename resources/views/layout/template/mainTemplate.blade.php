<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.shortname') }} | {{ $title }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link href="{{ url('/asset/css/sb-admin-2.min.css') }}" rel="stylesheet">

    <link href="{{ url('/asset/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ url('/asset/css/custom.css') }}" rel="stylesheet" type="text/css">
    {{-- <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet"> --}}
    <link rel="icon" type="image/png" href="{{ url('/asset/img/cbt.png') }}" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins">
    <script src="{{ url('/asset/js/jquery-3.7.0.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('asset/js/ijaboCropTool.min.css') }}">
    <style>
        body {
            font-family: 'Poppins';
        }

        a:hover {
            text-decoration: none;
            /* Menghapus garis bawah pada hover */
            color: inherit;
            /* Menggunakan warna teks warisan (sesuai dengan elemen induk) */
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="stylesheet" href="{{ url('/asset/css/jquery.datetimepicker.min.css') }}" />

    <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />

</head>

<body class="sidebar-toggled">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        @include('layout.navbar.sidebar')
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                @include('layout.navbar.topbar')
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid ">

                    @yield('container')
                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span style="font-weight: bold; font-size: larger;">BIMBEL PATAS</span> <br> Pacu Prestasi
                        bersama Bimbel Patas!
                    </div>
                </div>
            </footer>

            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Konfirmasi Logout</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-8 mx-auto text-center text-danger my-2">
                            {{-- <i class="fa-solid fa-circle-exclamation fa-shake fa-2xl"></i> --}}
                            <img src="{{ url('/asset/img/logout.png') }}" alt="" class="img-fluid"
                                srcset="">
                        </div>
                        <div class="col-12 ">
                            Ingin mengakhiri sesi? anda dapat melanjutkan dengan menekan tombol "logout"
                            dibawah ini.
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="btn btn-secondary animate-btn-small" type="button"
                            data-dismiss="modal">Cancel</button>
                        <button class="btn btn-danger animate-btn-small" type="submit">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script src="https://kit.fontawesome.com/68f43c1324.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous">
    </script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"
        integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous">
    </script>

    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <script>
        feather.replace()
    </script>
    <!-- Bootstrap core JavaScript-->
    <script src="{{ url('/asset/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ url('/asset/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Core plugin JavaScript-->
    <script src="{{ url('/asset/vendor/jquery-easing/jquery.easing.min.js') }}"></script>

    <!-- Custom scripts for all pages-->
    <script src="{{ url('/asset/js/sb-admin-2.min.js') }}"></script>
    <script src="{{ url('/asset/js/jquery.datetimepicker.full.min.js') }}"></script>


    <!-- Page level plugins -->


</body>

</html>