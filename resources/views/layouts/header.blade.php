<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="/assets/images/logo.svg">
    <title>@yield('title' , 'Dashboard')</title>
    <!-- Simple bar CSS -->
    <link rel="stylesheet" href="/css/simplebar.css">
    <link rel="stylesheet" href="/css/footer.css">
    <link rel="stylesheet" href="/css/feather.css">
    <link rel="stylesheet" href="/css/select2.css">
    <link rel="stylesheet" href="/css/dropzone.css">
    <link rel="stylesheet" href="/css/uppy.min.css">
    <link rel="stylesheet" href="/css/jquery.steps.css">
    <link rel="stylesheet" href="/css/jquery.timepicker.css">
    <link rel="stylesheet" href="/css/quill.snow.css">
    <!-- Date Range Picker CSS -->
    <link rel="stylesheet" href="/css/daterangepicker.css">
    <!-- App CSS -->
    <link rel="stylesheet" href="/css/app-light.css" id="lightTheme">
    <link rel="stylesheet" href="/css/app-dark.css" id="darkTheme" disabled>
    @yield('style')
  </head>
  <body class="vertical  light rtl ">
    <div class="wrapper">
<!-- Fixed Shortcut Buttons -->
<div style="
  position: fixed;
  top: 20px;
  left: 50%;
  transform: translateX(-50%);
  z-index: 9999;
  display: flex;
  gap: 10px; /* تقليل المسافة بين الأزرار */
  animation: fadeInScaleUp 0.6s ease-out forwards; /* انيميشن للتصغير والظهور */
  opacity: 0; /* ابدأ بشفافية 0 */
  transform: scale(0.8) translateY(-10px) translateX(-50%); /* ابدأ أصغر وأعلى قليلاً */
">
  <button onclick="window.location.href='{{route('invoice.create.pending')}}'" style="
    padding: 10px 15px; /* تقليل حجم البادينج */
    font-size: 14px; /* تقليل حجم الخط */
    border: none;
    border-radius: 25px; /* تقليل نصف القطر ليناسب الحجم */
    background-color: #007bff;
    color: white;
    cursor: pointer;
    box-shadow: 0 3px 10px rgba(0,0,0,0.2); /* تعديل الظل ليناسب الحجم */
    transition: transform 0.3s ease, background-color 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px; /* تقليل المسافة بين الأيقونة والنص */
  ">
    <span>➕</span> اضافة فاتورة
  </button>

    <button onclick="window.location.href='{{route('invoice.pending')}}'" style="
    padding: 10px 15px; /* تقليل حجم البادينج */
    font-size: 14px; /* تقليل حجم الخط */
    border: none;
    border-radius: 25px; /* تقليل نصف القطر ليناسب الحجم */
    background-color: #28a745;
    color: white;
    cursor: pointer;
    box-shadow: 0 3px 10px rgba(0,0,0,0.2); /* تعديل الظل ليناسب الحجم */
    transition: transform 0.3s ease, background-color 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px; /* تقليل المسافة بين الأيقونة والنص */
  ">
    <span>📋</span> عرض الفواتير
  </button>
</div>

<style>
  @keyframes fadeInScaleUp {
    0% {
      opacity: 0;
      transform: scale(0.8) translateY(-10px) translateX(-50%);
    }
    100% {
      opacity: 1;
      transform: scale(1) translateY(0) translateX(-50%);
    }
  }
</style>
