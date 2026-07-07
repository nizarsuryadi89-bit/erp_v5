<!--end::Sidebar-->

<!--begin::App Main-->
<main class="app-main">

    <!--begin::App Content Header-->
    <div class="app-content-header">

        <!--begin::Container-->
        <div class="container-fluid">

            <!--begin::Row-->
            <div class="row align-items-center">

                <!-- Title -->
                <div class="col-sm-6">
                    <h3 class="mb-0"><?= $title ?></h3>
                </div>

                <!-- Breadcrumb -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end mb-0">
                        <li class="breadcrumb-item">
                            <a href="<?= base_url('dashboard') ?>">Home</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <?= $title ?>
                        </li>
                    </ol>
                </div>

            </div>
            <!--end::Row-->

        </div>
        <!--end::Container-->

    </div>
    <!--end::App Content Header-->

</main>
<!--end::App Main-->
