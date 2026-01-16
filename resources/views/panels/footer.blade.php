<!-- BEGIN: Footer-->

<footer
  class="footer footer-light {{ $configData['footerType'] === 'footer-hidden' ? 'd-none' : '' }} {{ $configData['footerType'] }}">
  <p class="clearfix mb-0">
    <span class="float-md-start d-block d-md-inline-block mt-25">COPYRIGHT &copy;
      <script>
        document.write(new Date().getFullYear())
      </script><a class="ms-25" href="https://panvelcity.in/"
      <span class="d-none d-sm-inline-block">All rights Reserved</span>
    </span>
   </p>
</footer>
<button class="btn btn-primary btn-icon scroll-top" type="button"><i data-feather="arrow-up"></i></button>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
  function formatDate(dateString, format) {
    const date = new Date(dateString);
    let day = date.getDate().toString().padStart(2, '0');
    let month = (date.getMonth() + 1).toString().padStart(2, '0');
    const year = date.getFullYear();

    if (format === 'yyyy-mm-dd') {
      return `${year}-${month}-${day}`;
    } else if (format === 'dd/mm/yyyy') {
      return `${day}/${month}/${year}`;
    } else if (format === 'yyyy/mm/dd') {
      return `${year}/${month}/${day}`;
    } else {
      // Default format: dd-mm-yyyy
      return `${day}-${month}-${year}`;
    }
  }
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.9-1/crypto-js.js"></script>


<!-- END: Footer-->
 