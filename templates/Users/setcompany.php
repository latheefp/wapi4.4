<div class="account-pages my-5 pt-sm-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6 col-xl-5">
                <div class="card overflow-hidden">
                    <div class="bg-primary bg-soft">
                        <div class="row">
                            <div class="col-7">
                                <div class="text-primary p-4">
                                    <?php
                                    use Cake\Core\Configure;
                                    ?>
                                    <h5 class="text-primary" style="color:#fff !important;"><?= Configure::read('app.name') ?> <?= Configure::read('app.version') ?></h5>
                                    <p style="color:#fff !important;">Choose your Company!</p>
                                </div>
                            </div>
                            <div class="col-5 align-self-end">
                                <img src="/images/profile-img.png" alt="" class="img-fluid">
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-0"> 
                        <div>
                            <a href="index.html">
                                <div class="avatar-md profile-user-wid mb-4">
                                    <span class="avatar-title rounded-circle bg-light">
                                        <img src="" alt="" class="rounded-circle" height="34">
                                    </span>
                                </div>
                            </a>
                        </div>
                        <div class="p-2">
                            <?php
                                echo $this->Form->create($form,
                                        [
                                            'type' => 'post',
                                            'class' => 'form-horizontal',
                                            'class' => ["form-horizontal", "needs-validation"],
                                            "novalidate",
                                        ]
                                );
                                ?>
                                <div class="mb-3">
                                    <label for="company">Select Company *</label>
                                    <select class="form-control select2" required name="company_id" id="company_id">
                                        <?php
                                        $state_id = null;
                                        echo $this->Selectlist->buildlist([
                                            'table' => 'Companies',
                                            'selected' => $state_id,
                                            'field' => 'company_name'
                                        ])
                                        ?>
                                    </select>
                                </div>
                                <div class="text-end">
                                    <button class="btn btn-primary w-md waves-effect waves-light" name="choose" type="submit">Choose</button>
                                </div>

                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
