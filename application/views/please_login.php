
<body>
<!-- partial:index.partial.html -->
<h1>Please Login</h1>
<p class="zoom-area">You don't login, redirect to login page in 5 Seccond</p>
<section class="error-container">
  <span id="angka">4</span>
  <span><span class="screen-reader-text">0</span></span>
  <span>3</span>
</section>
<div class="link-container">
  <a href="<?= base_url('auth') ?>" class="more-link">Login Page</a>
</div>
<!-- partial -->
<script>
    var timer = setTimeout(function() {
        window.location='<?= base_url('auth') ?>'
    }, 5000);
</script>
</body>
</html>
