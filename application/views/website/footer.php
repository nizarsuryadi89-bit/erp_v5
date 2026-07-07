<footer id="footer" class="border-0 bg-dark">
	<div class="container py-5">
		<div class="row gy-4 justify-content-center">
			<!-- Logo -->
			<div class="col-12 col-lg-3 d-flex justify-content-center justify-content-lg-start align-items-center">
				<a href="index.html">
					<img src="<?= base_url('assets/') ?>images/2.jpeg" alt="Logo" class="img-fluid" style="max-width: 200px;">
				</a>
			</div>

			<!-- Pages -->
			<div class="col-6 col-md-4 col-lg-2 text-center text-lg-start">
				<h5 class="text-5 text-transform-none font-weight-semibold text-color-light mb-4">Pages</h5>
				<ul class="list-unstyled">
					<li class="mb-2"><a href="<?= base_url('website/product') ?>" class="link-hover-style-1">Product</a></li>
					<li class="mb-2"><a href="<?= base_url('website/blog') ?>" class="link-hover-style-1">Blog</a></li>
					<li class="mb-2"><a href="<?= base_url('website/digitalprint') ?>" class="link-hover-style-1">Digital Print</a></li>
				</ul>
			</div>

			<!-- Links -->
			<div class="col-6 col-md-4 col-lg-2 text-center text-lg-start">
				<h5 class="text-5 text-transform-none font-weight-semibold text-color-light mb-4">Links</h5>
				<ul class="list-unstyled">
					<li class="mb-2"><a href="page-faq.html" class="link-hover-style-1">FAQ's</a></li>
					<li class="mb-2"><a href="sitemap.html" class="link-hover-style-1">Sitemap</a></li>
				</ul>
			</div>

			<!-- Newsletter -->
			<div class="col-12 col-lg-5 text-center text-lg-start">
				<h5 class="text-5 text-transform-none font-weight-semibold text-color-light mb-4">Newsletter</h5>

				<div class="alert alert-success d-none" id="newsletterSuccess">
					<strong>Success!</strong> You've been added to our email list.
				</div>
				<div class="alert alert-danger d-none" id="newsletterError"></div>

				<form id="newsletterForm" action="<?= base_url('assets/front/') ?>php/newsletter-subscribe.php" method="POST" class="mb-3">
					<div class="input-group input-group-rounded">
						<input type="text" class="form-control form-control-sm bg-light" placeholder="Email Address" name="newsletterEmail" id="newsletterEmail">
						<span class="input-group-append">
							<button class="btn btn-light text-color-dark" type="submit"><strong>GO!</strong></button>
						</span>
					</div>
				</form>

				<p class="mt-3 mb-0">
					<i class="fab fa-whatsapp text-color-primary"></i>
					<span class="text-color-light opacity-7 ps-2">082213424749</span>
					<i class="far fa-envelope text-color-primary ms-4"></i>
					<a href="mailto:info@tgmprint.com" class="opacity-7 ps-2 text-color-light">info@tgmprint.com</a>
				</p>
			</div>
		</div>
	</div>

	<!-- Copyright -->
	<div class="footer-copyright footer-copyright-style-2 background-transparent footer-top-light-border">
		<div class="container py-3">
			<div class="row">
				<div class="col text-center">
					<p class="mb-1">Jl. Serma Marjuki No.50D, RT.005/RW.002, Marga Jaya, Kec. Bekasi Sel., Kota Bks, Jawa Barat 17141</p>
					<p class="mb-0">© Copyright <?= date('Y') ?>. All Rights Reserved.</p>
				</div>
			</div>
		</div>
	</div>
</footer>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

		<!-- Vendor -->
		<script src="<?= base_url('assets/front/')?>vendor/jquery/jquery.min.js"></script>
		<script src="<?= base_url('assets/front/')?>vendor/jquery.appear/jquery.appear.min.js"></script>
		<script src="<?= base_url('assets/front/')?>vendor/jquery.easing/jquery.easing.min.js"></script>
		<script src="<?= base_url('assets/front/')?>vendor/jquery.cookie/jquery.cookie.min.js"></script>
		<script src="<?= base_url('assets/front/')?>vendor/popper/umd/popper.min.js"></script>
		<script src="<?= base_url('assets/front/')?>vendor/bootstrap/js/bootstrap.min.js"></script>
		<script src="<?= base_url('assets/front/')?>vendor/common/common.min.js"></script>
		<script src="<?= base_url('assets/front/')?>vendor/jquery.validation/jquery.validate.min.js"></script>
		<script src="<?= base_url('assets/front/')?>vendor/jquery.easy-pie-chart/jquery.easypiechart.min.js"></script>
		<script src="<?= base_url('assets/front/')?>vendor/jquery.gmap/jquery.gmap.min.js"></script>
		<script src="<?= base_url('assets/front/')?>vendor/jquery.lazyload/jquery.lazyload.min.js"></script>
		<script src="<?= base_url('assets/front/')?>vendor/isotope/jquery.isotope.min.js"></script>
		<script src="<?= base_url('assets/front/')?>vendor/owl.carousel/owl.carousel.min.js"></script>
		<script src="<?= base_url('assets/front/')?>vendor/magnific-popup/jquery.magnific-popup.min.js"></script>
		<script src="<?= base_url('assets/front/')?>vendor/vide/jquery.vide.min.js"></script>
		<script src="<?= base_url('assets/front/')?>vendor/vivus/vivus.min.js"></script>
		
		<!-- Theme Base, Components and Settings -->
		<script src="<?= base_url('assets/front/')?>js/theme.js"></script>
		
		<!-- Current Page Vendor and Views -->
		<script src="<?= base_url('assets/front/')?>vendor/rs-plugin/js/jquery.themepunch.tools.min.js"></script>
		<script src="<?= base_url('assets/')?>vendor/rs-plugin/js/jquery.themepunch.revolution.min.js"></script>
		
		<!-- Theme Custom -->
		<script src="<?= base_url('assets/front/')?>js/custom.js"></script>
		
		<!-- Theme Initialization Files -->
		<script src="<?= base_url('assets/front/')?>js/theme.init.js"></script>

	</body>
</html>