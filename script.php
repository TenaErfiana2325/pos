<!-- AdminLTE App -->
<script src="../aset_web/dist/js/adminlte.min.js"></script>
<!-- jQuery -->
<script src="../aset_web/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap -->
<script src="../aset_web/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE -->
<script src="../aset_web/dist/js/adminlte.js"></script>

<!-- OPTIONAL SCRIPTS -->
<script src="../aset_web/plugins/chart.js/Chart.min.js"></script>

<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="../aset_web/dist/js/pages/dashboard3.js"></script>

<!-- DataTables  & Plugins -->
<script src="../aset_web/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../aset_web/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="../aset_web/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="../aset_web/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="../aset_web/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="../aset_web/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="../aset_web/plugins/jszip/jszip.min.js"></script>
<script src="../aset_web/plugins/pdfmake/pdfmake.min.js"></script>
<script src="../aset_web/plugins/pdfmake/vfs_fonts.js"></script>
<script src="../aset_web/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="../aset_web/plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="../aset_web/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<!-- Page specific script -->
<script>
  $(function () {
    $("#example1").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    $('#example2').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
    });
  });
  </script>

  <!-- SweetAlert2 -->
  <link rel="stylesheet" href="../aset_web/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
  <!-- Toastr -->
  <link rel="stylesheet" href="../aset_web/plugins/toastr/toastr.min.css">
