
<section class="panel">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
                        <a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
                    </div>
            
                    <h2 class="panel-title"><?= $title ?></h2>
                </header>
                <div class="panel-body">
                    <div class="row">
                        
                        <div class="col-sm-6"><form action="process.php" method="POST">
                            <label for="nama">Nama:</label>
                            <input type="text" id="nama" name="nama" required class="form-control">

                            <label for="email">Email:</label>
                            <input type="email" id="email" name="email" required class="form-control">

                            <label for="password">Password:</label>
                            <input type="password" id="password" name="password" required class="form-control">

                            <label for="konfirmasi_password">Konfirmasi Password:</label>
                            <input type="password" id="konfirmasi_password" name="konfirmasi_password" required class="form-control">

                            <label for="alamat">Alamat:</label>
                            <textarea id="alamat" name="alamat" required class="form-control"> </textarea>
                            <hr>
                            <input type="submit" value="Submit" class="btn btn-primary">
                            </form>
                        </div>
                    </div>
            </div>
</section>