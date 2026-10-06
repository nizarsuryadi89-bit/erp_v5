<!-- Modal Pricing -->
<div class="modal fade" id="modalPricing" tabindex="-1" aria-labelledby="modalPricingLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 24px;">
            <div class="modal-header border-0 bg-light p-4" style="border-radius: 24px 24px 0 0;">
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-3">
                        <i class="bi bi-currency-dollar text-primary fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-slate-900" id="modalPricingLabel">Matriks Skala Harga</h5>
                        <p class="text-muted small mb-0">Atur strategi harga untuk: <span class="fw-bold text-primary"
                                id="pricingProductName">-</span></p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 pt-0" id="pricingModalBody">
                <!-- Content loaded via AJAX from get_pricing_partial -->
            </div>
            <input type="hidden" id="pricingProductIdInput">
        </div>
    </div>
</div>